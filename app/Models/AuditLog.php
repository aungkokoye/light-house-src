<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    public const null UPDATED_AT = null;

    public const string EVENT_CREATED          = 'created';
    public const string EVENT_UPDATED          = 'updated';
    public const string EVENT_DELETED          = 'deleted';
    public const string EVENT_LOGIN            = 'login';
    public const string EVENT_LOGOUT           = 'logout';
    public const string EVENT_PASSWORD_CHANGED = 'password_changed';

    public static function events(): array
    {
        return [
            self::EVENT_CREATED,
            self::EVENT_UPDATED,
            self::EVENT_DELETED,
            self::EVENT_LOGIN,
            self::EVENT_LOGOUT,
            self::EVENT_PASSWORD_CHANGED,
        ];
    }

    protected $fillable = [
        'user_id',
        'user_email',
        'event',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }
}
