<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreSiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255', 'unique:sites,name'],
            'description' => ['nullable', 'string', 'max:10000'],
            'address'     => ['nullable', 'string', 'max:2000'],
            'phone'       => ['nullable', 'string', 'max:50'],
        ];
    }
}
