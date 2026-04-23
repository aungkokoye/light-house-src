<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\ChatKnowledgeCategory;

class ChatKnowledge extends Model
{
    protected $fillable = [
        'chat_knowledge_category_id',
        'title',
        'content',
        'active',
        'sort_order',
        'created_by',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ChatKnowledgeCategory::class, 'chat_knowledge_category_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
