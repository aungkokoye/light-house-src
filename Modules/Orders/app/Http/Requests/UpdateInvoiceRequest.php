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
            'customer_id'              => ['required', 'integer', 'exists:customers,id'],
            'discount'                 => ['nullable', 'integer', 'min:0'],
            'note'                     => ['nullable', 'string', 'max:5000'],
            'jobs'                     => ['required', 'array', 'min:1'],
            'jobs.*.service_id'        => ['required', 'integer', 'exists:job_services,id'],
            'jobs.*.product_id'        => ['required', 'integer', 'exists:products,id'],
            'jobs.*.quantity'          => ['required', 'integer', 'min:1'],
            'jobs.*.unit_price'        => ['required', 'integer', 'min:0'],
            'jobs.*.delivery_date'     => ['required', 'date'],
            'jobs.*.note'              => ['nullable', 'string', 'max:1000'],

            'payments'                 => ['nullable', 'array'],
            'payments.*.id'            => ['nullable', 'integer', 'exists:payments,id'],
            'payments.*.payment_type_id' => ['required', 'integer', 'exists:payment_types,id'],
            'payments.*.stage'         => ['required', 'integer', 'in:' . implode(',', [Payment::STAGE_ADVANCE, Payment::STAGE_FINAL, Payment::STAGE_REFUND])],
            'payments.*.amount'        => ['required', 'integer'],
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

            $netTotal = 0;
            $hasFinal = false;
            $hasRefund = false;

            // Existing payments with updates applied
            foreach ($invoice->payments as $ep) {
                if (isset($submittedById[$ep->id])) {
                    $u      = $submittedById[$ep->id];
                    $amt    = (int) ($u['amount'] ?? $ep->amount);
                    $stage  = (int) ($u['stage']  ?? $ep->stage);
                } else {
                    $amt   = $ep->amount;
                    $stage = $ep->stage;
                }

                // Validate refund requires admin
                if ($stage === Payment::STAGE_REFUND) {
                    $hasRefund = true;
                    if (! $this->user()->hasRole('admin')) {
                        $v->errors()->add('payments', 'Only admins can set refund payments.');
                        return;
                    }
                    // amount must be negative for refund
                    if ($amt >= 0) {
                        $v->errors()->add('payments', 'Refund payment amount must be negative.');
                        return;
                    }
                } else {
                    if ($stage === Payment::STAGE_FINAL) $hasFinal = true;
                }

                $netTotal += $amt;
            }

            // New payments (no id)
            foreach ($pmtInputs as $p) {
                if (!empty($p['id'])) continue;
                $amt   = (int) ($p['amount'] ?? 0);
                $stage = (int) ($p['stage']  ?? 0);

                if ($stage === Payment::STAGE_REFUND) {
                    $hasRefund = true;
                    if (! $this->user()->hasRole('admin')) {
                        $v->errors()->add('payments', 'Only admins can add refund payments.');
                        return;
                    }
                    if ($amt >= 0) {
                        $v->errors()->add('payments', 'Refund payment amount must be negative.');
                        return;
                    }
                } else {
                    if ($stage === Payment::STAGE_FINAL) $hasFinal = true;
                }

                $netTotal += $amt;
            }

            if ($hasRefund && $netTotal < 0) {
                $v->errors()->add('payments', 'Total refunds cannot exceed total payments made.');
            } elseif ($netTotal > $invoiceTotal) {
                $v->errors()->add('payments', "Net paid ({$netTotal}) would exceed the invoice total ({$invoiceTotal}).");
            } elseif ($hasFinal && $netTotal !== $invoiceTotal) {
                $v->errors()->add('payments', "Final payment must bring net paid to exactly {$invoiceTotal}.");
            } elseif (! $hasFinal && $netTotal >= $invoiceTotal) {
                $v->errors()->add('payments', "Net paid must be less than the invoice total ({$invoiceTotal}) when there is no final payment.");
            }
        });
    }
}
