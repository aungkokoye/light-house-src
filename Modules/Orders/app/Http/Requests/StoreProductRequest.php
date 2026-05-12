<?php

namespace Modules\Orders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:255', 'unique:products,name'],
            'description'    => ['nullable', 'string'],
            'per_price'      => ['required', 'integer', 'min:0'],
        ];
    }
}
