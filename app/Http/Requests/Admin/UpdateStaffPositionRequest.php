<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStaffPositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $positionId = $this->route('staffPosition')->id;

        return [
            'name'        => ['required', 'string', 'max:255', Rule::unique('staff_positions', 'name')->ignore($positionId)],
            'description' => ['nullable', 'string', 'max:10000'],
        ];
    }
}
