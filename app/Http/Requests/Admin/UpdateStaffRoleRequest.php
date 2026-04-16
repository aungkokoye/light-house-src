<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStaffRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'staff_position_id' => ['required', 'exists:staff_positions,id'],
            'site_id'           => ['nullable', 'exists:sites,id'],
            'salary'            => ['required', 'integer', 'min:0'],
            'start_date'        => ['required', 'date'],
            'end_date'          => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }
}
