<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StaffProfile extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'nrc_no',
        'dob',
        'address',
        'phone',
        'created_by',
        'start_date',
    ];

    protected function casts(): array
    {
        return [
            'dob'        => 'date',
            'start_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function staffRoles(): HasMany
    {
        return $this->hasMany(StaffRole::class);
    }

    public function currentRole(): HasMany
    {
        return $this->hasMany(StaffRole::class)->whereNull('end_date')->latest('start_date');
    }
}
