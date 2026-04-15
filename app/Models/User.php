<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    const int STAFF_TYPE_ID   = 1;
    const int COMPANY_TYPE_ID = 2;

    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'type',
        'name',
        'email',
        'password',
        'google_id',
        'activated',
        'email_verified_at',
        'created_by',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'type'              => 'integer',
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function staffProfile(): HasOne
    {
        return $this->hasOne(StaffProfile::class);
    }

    public function companyProfile(): HasOne
    {
        return $this->hasOne(CompanyProfile::class);
    }

    public function isStaff(): bool
    {
        return $this->type === self::STAFF_TYPE_ID;
    }

    public function isCompany(): bool
    {
        return $this->type === self::COMPANY_TYPE_ID;
    }

    public function getTypeName(): ?string
    {
        return match($this->type) {
            self::STAFF_TYPE_ID   => 'Staff',
            self::COMPANY_TYPE_ID => 'Company',
            default               => null,
        };
    }
}
