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
        static::creating(function (Invoice $invoice) {
            if (empty($invoice->invoice_no)) {
                $invoice->invoice_no = static::generateInvoiceNo();
            }
        });
    }

    private static function generateInvoiceNo(): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        do {
            $no = '';
            for ($i = 0; $i < 8; $i++) {
                $no .= $chars[random_int(0, strlen($chars) - 1)];
            }
        } while (static::where('invoice_no', $no)->exists());

        return $no;
    }

    protected $fillable = [
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
