<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\HasUserProfileRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    use HasUserProfileRules;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge(
            $this->baseRules(),
            $this->permissionRules(),
            $this->customerRules(),
            $this->staffRules()
        );
    }

    private function baseRules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255', 'unique:users,name'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],

            'role' => ['required', 'string', Rule::exists('roles', 'name')],

            'activated'      => ['sometimes', 'boolean'],
            'email_verified' => ['sometimes', 'boolean'],
        ];
    }
}
