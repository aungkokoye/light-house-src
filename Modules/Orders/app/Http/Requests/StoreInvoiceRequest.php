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
            'customer_id'              => ['required', 'integer', 'exists:customers,id'],
            'discount'                 => ['nullable', 'integer', 'min:0'],
            'note'                     => ['nullable', 'string', 'max:5000'],
            'jobs'                     => ['required', 'array', 'min:1'],
            'jobs.*.service_id'        => ['required', 'integer', 'exists:job_services,id'],
            'jobs.*.product_id'        => ['required', 'integer', 'exists:products,id'],
            'jobs.*.quantity'          => ['required', 'integer', 'min:1'],
            'jobs.*.unit_price'        => ['required', 'integer', 'min:0'],
            'jobs.*.delivery_date'     => ['required', 'date', 'after_or_equal:today'],
            'jobs.*.note'              => ['nullable', 'string', 'max:1000'],
            'payment'                  => ['nullable', 'array'],
            'payment.payment_type_id'  => ['required_with:payment', 'integer', 'exists:payment_types,id'],
            'payment.stage'            => ['required_with:payment', 'integer', 'in:' . implode(',', [Payment::STAGE_ADVANCE, Payment::STAGE_FINAL])],
            'payment.amount'           => ['required_with:payment', 'integer', 'min:0'],
            'payment.payment_date'     => ['required_with:payment', 'date', 'after_or_equal:today'],
            'payment.note'             => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'jobs.*.product_id.required'           => 'Product is required.',
            'jobs.*.service_id.required'           => 'Service is required.',
            'jobs.*.quantity.required'             => 'Quantity is required.',
            'jobs.*.unit_price.required'           => 'Unit price is required.',
            'jobs.*.delivery_date.required'        => 'Delivery date is required.',
            'jobs.*.delivery_date.date'            => 'Delivery date must be a valid date.',
            'jobs.*.delivery_date.after_or_equal'  => 'Delivery date should be today or later.',
            'payment.payment_type_id.required'     => 'Payment type is required.',
            'payment.stage.required'               => 'Payment stage is required.',
            'payment.amount.required'              => 'Payment amount is required.',
            'payment.payment_date.required'        => 'Payment date is required.',
            'payment.payment_date.date'            => 'Payment date must be a valid date.',
            'payment.payment_date.after_or_equal'  => 'Payment date should be today or later.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            if (!$this->filled('payment')) {
                return;
            }

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
