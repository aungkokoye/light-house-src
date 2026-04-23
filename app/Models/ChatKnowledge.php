<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatKnowledge extends Model
{
    protected $fillable = [
        'category',
        'title',
        'content',
        'active',
        'sort_order',
        'created_by',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
