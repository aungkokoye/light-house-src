<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChatKnowledgeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'chat_knowledge_category_id' => ['required', 'integer', 'exists:chat_knowledge_categories,id'],
            'title'                      => ['required', 'string', 'max:255'],
            'content'                    => ['required', 'string', 'max:10000'],
            'active'                     => ['boolean'],
            'sort_order'                 => ['integer', 'min:0'],
        ];
    }
}
