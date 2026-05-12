<?php

namespace Modules\Orders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Modules\Orders\Models\Invoice;
use Modules\Orders\Models\Payment;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Invoice::class);
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
            'payment'                  => ['required', 'array'],
            'payment.type_id'          => ['required', 'integer', 'in:' . implode(',', [Payment::TYPE_CASH, Payment::TYPE_BANK, Payment::TYPE_OTHER])],
            'payment.bank_id'          => ['nullable', 'integer', 'exists:banks,id', 'required_if:payment.type_id,' . Payment::TYPE_BANK],
            'payment.stage'            => ['required', 'integer', 'in:' . implode(',', [Payment::STAGE_ADVANCE, Payment::STAGE_FINAL])],
            'payment.amount'           => ['required', 'integer', 'min:0'],
            'payment.payment_date'     => ['required', 'date'],
            'payment.note'             => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $jobs     = $this->input('jobs', []);
            $discount = (int) $this->input('discount', 0);
            $amount   = (int) $this->input('payment.amount', 0);

            $subtotal = array_sum(array_map(
                fn($j) => (int) ($j['quantity'] ?? 0) * (int) ($j['unit_price'] ?? 0),
                $jobs
            ));
            $total = max(0, $subtotal - $discount);

            $stage = (int) $this->input('payment.stage', 0);

            if ($amount > $total) {
                $v->errors()->add(
                    'payment.amount',
                    "Payment amount must not exceed the invoice total ({$total})."
                );
            } elseif ($stage === Payment::STAGE_FINAL && $amount !== $total) {
                $v->errors()->add(
                    'payment.amount',
                    "Final payment amount must equal the invoice total ({$total})."
                );
            } elseif ($stage !== Payment::STAGE_FINAL && $amount >= $total) {
                $v->errors()->add(
                    'payment.amount',
                    "Total payments must be less than the invoice total ({$total}) when there is no final payment."
                );
            }
        });
    }
}
