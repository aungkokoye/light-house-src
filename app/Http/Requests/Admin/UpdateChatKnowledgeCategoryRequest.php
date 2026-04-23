<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateChatKnowledgeCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:100', Rule::unique('chat_knowledge_categories', 'name')->ignore($this->route('chatKnowledgeCategory'))],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order'  => ['integer', 'min:0'],
        ];
    }
}
