<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use Modules\Orders\Database\Factories\PaymentTypeFactory;

class PaymentType extends Model
{
    use HasFactory;

    protected static function newFactory(): PaymentTypeFactory
    {
        return PaymentTypeFactory::new();
    }

    protected $fillable = [
        'name',
        'created_by',
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
