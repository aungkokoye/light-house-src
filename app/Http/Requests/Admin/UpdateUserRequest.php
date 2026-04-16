<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\HasUserProfileRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('user')->id;

        return [
            'name'     => ['required', 'string', 'max:255', Rule::unique('users', 'name')->ignore($userId)],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()],

            'role' => ['required', 'string', Rule::exists('roles', 'name')],

            'activated'      => ['sometimes', 'boolean'],
            'email_verified' => ['sometimes', 'boolean'],
        ];
    }
}
