<?php

namespace Modules\Orders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Modules\Orders\Models\Invoice;
use Modules\Orders\Models\Payment;

class UpdatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('payment'));
    }

    public function rules(): array
    {
        return [
            'invoice_id'   => ['required', 'integer', 'exists:invoices,id'],
            'type_id'      => ['required', 'integer', 'in:' . implode(',', [Payment::TYPE_CASH, Payment::TYPE_BANK, Payment::TYPE_OTHER])],
            'bank_id'      => ['nullable', 'integer', 'exists:banks,id', 'required_if:type_id,' . Payment::TYPE_BANK],
            'stage'        => ['required', 'integer', 'in:' . implode(',', [Payment::STAGE_ADVANCE, Payment::STAGE_FINAL])],
            'amount'       => ['required', 'integer', 'min:0'],
            'note'         => ['nullable', 'string', 'max:5000'],
            'payment_date' => ['required', 'date'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $payment = $this->route('payment');
            $invoice = Invoice::find($this->input('invoice_id'));
            if (! $invoice) return;

            // SEC-2: ensure submitted invoice_id belongs to this payment
            if ((int) $this->input('invoice_id') !== $payment->invoice_id) {
                $v->errors()->add('invoice_id', 'Invoice mismatch.');
                return;
            }

            $amount       = (int) $this->input('amount', 0);
            $stage        = (int) $this->input('stage', 0);
            $invoiceTotal = $invoice->total;

            $invoice->loadMissing('payments');
            $others      = $invoice->payments->where('id', '!=', $payment->id);
            $alreadyPaid = $others->sum('amount');
            $totalAfter  = $alreadyPaid + $amount;
            $hasFinal    = $stage === Payment::STAGE_FINAL
                           || $others->contains('stage', Payment::STAGE_FINAL);

            if ($totalAfter > $invoiceTotal) {
                $v->errors()->add('amount', "Total payments ({$totalAfter}) would exceed the invoice total ({$invoiceTotal}).");
            } elseif ($stage === Payment::STAGE_FINAL && $totalAfter !== $invoiceTotal) {
                $v->errors()->add('amount', "Final payment must bring total payments to exactly {$invoiceTotal} (currently paid: {$alreadyPaid}).");
            } elseif (! $hasFinal && $totalAfter >= $invoiceTotal) {
                $v->errors()->add('amount', "Total payments must be less than the invoice total ({$invoiceTotal}) when there is no final payment.");
            }
        });
    }
}
