<?php

namespace Modules\Orders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePriceListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'job_service_id'      => ['required', 'integer', 'exists:job_services,id'],
            'product_description' => ['required', 'string'],
            'dimension'           => ['nullable', 'string', 'max:225'],
            'price'               => ['required', 'integer', 'min:0'],
            'remark'              => ['nullable', 'string'],
        ];
    }
}
