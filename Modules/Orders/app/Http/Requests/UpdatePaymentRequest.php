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
            $payment = $this->route('payment');
            $invoice = Invoice::find($this->input('invoice_id'));
            if (! $invoice) return;

            if ((int) $this->input('invoice_id') !== $payment->invoice_id) {
                $v->errors()->add('invoice_id', 'Invoice mismatch.');
                return;
            }

            $amount       = (int) $this->input('amount', 0);
            $stage        = (int) $this->input('stage', 0);
            $invoiceTotal = $invoice->total;

            $invoice->loadMissing('payments');
            $others   = $invoice->payments->where('id', '!=', $payment->id);
            $netOther = $others->sum('amount'); // negative refunds already deduct

            if ($stage === Payment::STAGE_REFUND) {
                if (! $this->user()->hasRole('admin')) {
                    $v->errors()->add('stage', 'Only admins can set a refund payment.');
                    return;
                }
                $nonRefundOther = $others->where('stage', '!=', Payment::STAGE_REFUND)->sum('amount');
                $refundOther    = $others->where('stage', Payment::STAGE_REFUND)->sum('amount'); // already negative
                $maxRefund      = $nonRefundOther + $refundOther; // net paid by others

                if (abs($amount) > $maxRefund) {
                    $v->errors()->add('amount', "Refund amount cannot exceed net paid ({$maxRefund}).");
                }
                return;
            }

            $totalAfter = $netOther + $amount;
            $hasFinal   = $stage === Payment::STAGE_FINAL
                          || $others->contains('stage', Payment::STAGE_FINAL);

            if ($totalAfter > $invoiceTotal) {
                $v->errors()->add('amount', "Total payments ({$totalAfter}) would exceed the invoice total ({$invoiceTotal}).");
            } elseif ($stage === Payment::STAGE_FINAL && $totalAfter !== $invoiceTotal) {
                $v->errors()->add('amount', "Final payment must bring net paid to exactly {$invoiceTotal} (currently: {$netOther}).");
            } elseif (! $hasFinal && $totalAfter >= $invoiceTotal) {
                $v->errors()->add('amount', "Total payments must be less than the invoice total ({$invoiceTotal}) when there is no final payment.");
            }
        });
    }
}
