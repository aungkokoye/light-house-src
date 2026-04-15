<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StaffPosition extends Model
{
    protected $fillable = [
        'name',
        'description',
        'created_by',
    ];

    public function staffRoles(): HasMany
    {
        return $this->hasMany(StaffRole::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
