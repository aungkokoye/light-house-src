<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'name'      => ['required', 'string', 'max:255', Rule::unique('users')->ignore($userId)],
            'email'     => ['required', 'email', Rule::unique('users')->ignore($userId)],
            'password'  => ['nullable', 'min:8', 'confirmed'],
            'role'      => ['required', 'in:admin,user'],
            'activated'      => ['boolean'],
            'email_verified' => ['boolean'],
            'permissions'    => ['nullable', 'array'],
            'permissions.*'  => ['string', 'exists:permissions,name'],
        ];
    }
}