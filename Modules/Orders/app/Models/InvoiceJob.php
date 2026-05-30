<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class InvoiceJob extends Model
{
    protected $fillable = [
        'invoice_id',
        'service_id',
        'product_id',
        'quantity',
        'unit_price',
        'total',
        'delivery_date',
        'note',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'quantity'      => 'integer',
            'unit_price'    => 'integer',
            'total'         => 'integer',
            'delivery_date' => 'date',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(JobService::class, 'service_id');
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
