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
        return [
            'invoice_id'   => ['required', 'integer', 'exists:invoices,id'],
            'bank_id'      => ['required', 'integer', 'exists:banks,id'],
            'stage'        => ['required', 'integer', 'in:' . implode(',', [Payment::STAGE_ADVANCE, Payment::STAGE_FINAL])],
            'amount'       => ['required', 'integer', 'min:0'],
            'note'         => ['nullable', 'string', 'max:5000'],
            'payment_date' => ['required', 'date'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $invoice = Invoice::find($this->input('invoice_id'));
            if (! $invoice) return;

            $amount       = (int) $this->input('amount', 0);
            $stage        = (int) $this->input('stage', 0);
            $invoiceTotal = $invoice->total;

            $invoice->loadMissing('payments');

            if ($invoice->payments->contains('stage', Payment::STAGE_FINAL)) {
                $v->errors()->add('invoice_id', 'This invoice already has a final payment and cannot accept more payments.');
                return;
            }

            $alreadyPaid = $invoice->payments->sum('amount');
            $totalAfter  = $alreadyPaid + $amount;
            $hasFinal    = $stage === Payment::STAGE_FINAL;

            if ($amount > $invoiceTotal) {
                $v->errors()->add('amount', "Payment amount must not exceed the invoice total ({$invoiceTotal}).");
            } elseif ($stage === Payment::STAGE_FINAL && $totalAfter !== $invoiceTotal) {
                $v->errors()->add('amount', "Final payment must bring total payments to exactly {$invoiceTotal} (currently paid: {$alreadyPaid}).");
            } elseif (! $hasFinal && $totalAfter >= $invoiceTotal) {
                $v->errors()->add('amount', "Total payments must be less than the invoice total ({$invoiceTotal}) when there is no final payment.");
            }
        });
    }
}
