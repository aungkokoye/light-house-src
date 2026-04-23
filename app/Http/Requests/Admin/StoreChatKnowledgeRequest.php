<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreChatKnowledgeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category'   => ['required', 'string', 'max:100'],
            'title'      => ['required', 'string', 'max:255'],
            'content'    => ['required', 'string', 'max:10000'],
            'active'     => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ];
    }
}
