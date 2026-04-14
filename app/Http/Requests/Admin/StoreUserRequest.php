<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:255', 'unique:users'],
            'email'     => ['required', 'email', 'unique:users'],
            'password'  => ['required', 'min:8', 'confirmed'],
            'role'      => ['required', 'string', Rule::exists('roles', 'name')],
            'activated'      => ['boolean'],
            'email_verified' => ['boolean'],
            'permissions'    => ['nullable', 'array'],
            'permissions.*'  => ['string', 'exists:permissions,name'],
        ];
    }
}