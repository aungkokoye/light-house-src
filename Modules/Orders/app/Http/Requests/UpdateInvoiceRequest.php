<?php

namespace Modules\Orders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Modules\Orders\Models\Invoice;
use Modules\Orders\Models\Payment;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('invoice'));
    }

    public function rules(): array
    {
        return [
            'customer_id'              => ['required', 'integer', 'exists:users,id'],
            'discount'                 => ['nullable', 'integer', 'min:0'],
            'note'                     => ['nullable', 'string', 'max:5000'],
            'jobs'                     => ['required', 'array', 'min:1'],
            'jobs.*.service_id'        => ['required', 'integer', 'exists:job_services,id'],
            'jobs.*.product_id'        => ['required', 'integer', 'exists:products,id'],
            'jobs.*.quantity'          => ['required', 'integer', 'min:1'],
            'jobs.*.unit_price'        => ['required', 'integer', 'min:0'],
            'jobs.*.delivery_date'     => ['required', 'date'],

            'payments'                 => ['nullable', 'array'],
            'payments.*.id'            => ['nullable', 'integer', 'exists:payments,id'],
            'payments.*.type_id'       => ['required', 'integer', 'in:' . implode(',', [Payment::TYPE_CASH, Payment::TYPE_BANK, Payment::TYPE_OTHER])],
            'payments.*.bank_id'       => ['nullable', 'integer', 'exists:banks,id', 'required_if:payments.*.type_id,' . Payment::TYPE_BANK],
            'payments.*.stage'         => ['required', 'integer', 'in:' . implode(',', [Payment::STAGE_ADVANCE, Payment::STAGE_FINAL])],
            'payments.*.amount'        => ['required', 'integer', 'min:0'],
            'payments.*.payment_date'  => ['required', 'date'],
            'payments.*.note'          => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $jobs     = $this->input('jobs', []);
            $discount = (int) $this->input('discount', 0);
            $subtotal = array_sum(array_map(
                fn($j) => (int) ($j['quantity'] ?? 0) * (int) ($j['unit_price'] ?? 0),
                $jobs
            ));
            $invoiceTotal = max(0, $subtotal - $discount);

            $invoice    = $this->route('invoice');
            $invoice->loadMissing('payments');
            $pmtInputs  = $this->input('payments', []);

            // Reject payment IDs that don't belong to this invoice
            foreach ($pmtInputs as $idx => $p) {
                if (!empty($p['id']) && !$invoice->payments->contains('id', (int) $p['id'])) {
                    $v->errors()->add("payments.{$idx}.id", 'Payment does not belong to this invoice.');
                }
            }
            if ($v->errors()->has('payments.*.id')) return;

            // Index submitted payments by id for updates
            $submittedById = [];
            foreach ($pmtInputs as $p) {
                if (!empty($p['id'])) {
                    $submittedById[(int) $p['id']] = $p;
                }
            }

            $totalAfter = 0;
            $hasFinal   = false;

            // Existing payments with updates applied
            foreach ($invoice->payments as $ep) {
                if (isset($submittedById[$ep->id])) {
                    $u           = $submittedById[$ep->id];
                    $totalAfter += (int) ($u['amount'] ?? $ep->amount);
                    $stage       = (int) ($u['stage']  ?? $ep->stage);
                } else {
                    $totalAfter += $ep->amount;
                    $stage       = $ep->stage;
                }

                if ($stage === Payment::STAGE_FINAL) $hasFinal = true;
            }

            // New payments (no id)
            foreach ($pmtInputs as $p) {
                if (!empty($p['id'])) continue;
                $totalAfter += (int) ($p['amount'] ?? 0);
                if ((int) ($p['stage'] ?? 0) === Payment::STAGE_FINAL) $hasFinal = true;
            }

            if ($totalAfter > $invoiceTotal) {
                $v->errors()->add('payments', "Total payments ({$totalAfter}) would exceed the invoice total ({$invoiceTotal}).");
            } elseif ($hasFinal && $totalAfter !== $invoiceTotal) {
                $v->errors()->add('payments', "Final payment must bring total payments to exactly {$invoiceTotal}.");
            } elseif (! $hasFinal && $totalAfter >= $invoiceTotal) {
                $v->errors()->add('payments', "Total payments must be less than the invoice total ({$invoiceTotal}) when there is no final payment.");
            }
        });
    }
}
