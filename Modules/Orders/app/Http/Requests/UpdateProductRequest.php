<?php

namespace Modules\Orders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:255', Rule::unique('products', 'name')->ignore($this->route('product')->id)],
            'description'    => ['nullable', 'string'],
            'per_price'      => ['nullable', 'integer', 'min:0'],
        ];
    }
}
