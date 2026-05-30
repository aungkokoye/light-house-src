<?php

namespace Modules\Orders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Modules\Orders\Models\Invoice;
use Modules\Orders\Models\Payment;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Payment::class);
    }

    public function rules(): array
    {
        $isRefund = (int) $this->input('stage', 0) === Payment::STAGE_REFUND;

        return [
            'invoice_id'      => ['required', 'integer', 'exists:invoices,id'],
            'payment_type_id' => ['required', 'integer', 'exists:payment_types,id'],
            'stage'           => ['required', 'integer', 'in:' . implode(',', [Payment::STAGE_ADVANCE, Payment::STAGE_FINAL, Payment::STAGE_REFUND])],
            'amount'          => $isRefund ? ['required', 'integer', 'max:-1'] : ['required', 'integer', 'min:1'],
            'note'            => ['nullable', 'string', 'max:5000'],
            'payment_date'    => ['required', 'date'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $invoice = Invoice::find($this->input('invoice_id'));
            if (! $invoice) return;

            $amount = (int) $this->input('amount', 0);
            $stage  = (int) $this->input('stage', 0);

            $invoice->loadMissing('payments');

            // Net paid = sum of all amounts (refunds already stored negative)
            $netPaid = $invoice->payments->sum('amount');

            if ($stage === Payment::STAGE_REFUND) {
                if (! $this->user()->hasRole('admin')) {
                    $v->errors()->add('stage', 'Only admins can create refund payments.');
                    return;
                }

                if (abs($amount) > $netPaid) {
                    $v->errors()->add('amount', "Refund amount cannot exceed net paid ({$netPaid}).");
                }
                return;
            }

            $invoiceTotal = $invoice->total;

            if ($invoice->payments->contains('stage', Payment::STAGE_FINAL)) {
                $v->errors()->add('invoice_id', 'This invoice already has a final payment and cannot accept more payments.');
                return;
            }

            $totalAfter = $netPaid + $amount;
            $hasFinal   = $stage === Payment::STAGE_FINAL;

            if ($amount > $invoiceTotal) {
                $v->errors()->add('amount', "Payment amount must not exceed the invoice total ({$invoiceTotal}).");
            } elseif ($stage === Payment::STAGE_FINAL && $totalAfter !== $invoiceTotal) {
                $v->errors()->add('amount', "Final payment must bring net paid to exactly {$invoiceTotal} (currently: {$netPaid}).");
            } elseif (! $hasFinal && $totalAfter >= $invoiceTotal) {
                $v->errors()->add('amount', "Total payments must be less than the invoice total ({$invoiceTotal}) when there is no final payment.");
            }
        });
    }
}
