<?php

namespace Modules\Orders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['nullable', 'email', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'title'        => ['nullable', 'string', 'max:255'],
            'phone'        => ['required', 'string', 'max:50', Rule::unique('customers')->where('name', $this->input('name'))->ignore($this->route('customer')->id)],
            'address'      => ['nullable', 'string', 'max:1000'],
        ];
    }
}
