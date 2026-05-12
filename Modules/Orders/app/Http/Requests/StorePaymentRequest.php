<?php

namespace Modules\Orders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Orders\Models\Payment;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_id'   => ['required', 'integer'],
            'type_id'      => ['required', 'integer', 'in:' . implode(',', [Payment::TYPE_CASH, Payment::TYPE_BANK, Payment::TYPE_OTHER])],
            'bank_id'      => ['nullable', 'exists:banks,id', $this->input('type_id') == Payment::TYPE_BANK ? 'required' : 'nullable'],
            'stage'        => ['required', 'integer', 'in:' . implode(',', [Payment::STAGE_ADVANCE, Payment::STAGE_FINAL])],
            'amount'       => ['required', 'integer', 'min:0'],
            'note'         => ['nullable', 'string'],
            'payment_date' => ['required', 'date'],
        ];
    }
}
