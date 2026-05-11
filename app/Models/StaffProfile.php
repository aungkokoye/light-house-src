<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class StaffProfile extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'father_name',
        'gender',
        'marital_status',
        'religion',
        'ethnic_group',
        'uniform_size',
        'education_qualification',
        'work_experience',
        'home_address',
        'note',
        'nrc_no',
        'dob',
        'address',
        'phone',
        'created_by',
        'start_date',
        'photo',
    ];

    protected $appends = ['photo_url'];

    protected function casts(): array
    {
        return [
            'dob'        => 'date',
            'start_date' => 'date',
        ];
    }

    protected function photoUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->photo ? Storage::disk('public')->url($this->photo) : null,
        );
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

    public function currentRole(): HasOne
    {
        return $this->hasOne(StaffRole::class)->whereNull('end_date')->latestOfMany('start_date');
    }
}
