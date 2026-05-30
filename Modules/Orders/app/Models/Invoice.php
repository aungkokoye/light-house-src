<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use Modules\Orders\Models\Customer;

class Invoice extends Model
{
    protected static function booted(): void
    {
        static::created(function (Invoice $invoice) {
            $prefix = env('INVOICE_PREFIX', 'LHPI');
            $no = $prefix . '-' . str_pad($invoice->id, 8, '0', STR_PAD_LEFT);
            $invoice->updateQuietly(['invoice_no' => $no]);
        });
    }

    protected $fillable = [
        'invoice_no',
        'customer_id',
        'discount',
        'total',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'discount' => 'integer',
            'total'    => 'integer',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(InvoiceJob::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
