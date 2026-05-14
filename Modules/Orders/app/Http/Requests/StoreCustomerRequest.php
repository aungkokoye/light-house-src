<?php

namespace Modules\Orders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Orders\Models\Invoice;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Invoice::class);
    }

    public function rules(): array
    {
        return [
            'name'                    => ['required', 'string', 'max:255'],
            'email'                   => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'company_profile'         => ['required', 'array'],
            'company_profile.name'    => ['required', 'string', 'max:255'],
            'company_profile.role'    => ['required', 'string', 'max:255'],
            'company_profile.phone'   => ['required', 'string', 'max:50'],
            'company_profile.address' => ['required', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'                    => 'name',
            'email'                   => 'email',
            'company_profile.name'    => 'company name',
            'company_profile.role'    => 'title / role',
            'company_profile.phone'   => 'phone',
            'company_profile.address' => 'address',
        ];
    }
}
