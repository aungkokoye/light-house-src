<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $siteId = $this->route('site')->id;

        return [
            'name'        => ['required', 'string', 'max:255', Rule::unique('sites', 'name')->ignore($siteId)],
            'description' => ['nullable', 'string', 'max:10000'],
            'address'     => ['nullable', 'string', 'max:2000'],
            'phone'       => ['nullable', 'string', 'max:50'],
        ];
    }
}
