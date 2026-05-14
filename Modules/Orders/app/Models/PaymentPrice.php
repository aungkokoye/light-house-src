<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class PaymentPrice extends Model
{
    protected $table = 'product_prices';

    protected $fillable = [
        'product_id',
        'per_price',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'per_price' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
