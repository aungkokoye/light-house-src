<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'created_by',
    ];

    public function prices(): HasMany
    {
        return $this->hasMany(PaymentPrice::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
