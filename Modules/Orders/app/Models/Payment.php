<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Payment extends Model
{
    const STAGE_ADVANCE = 1;
    const STAGE_FINAL   = 2;

    protected $fillable = [
        'invoice_id',
        'payment_type_id',
        'stage',
        'amount',
        'note',
        'payment_date',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount'       => 'integer',
        ];
    }

    public static function stageOptions(): array
    {
        return [
            ['id' => self::STAGE_ADVANCE, 'name' => 'Advance / Deposit'],
            ['id' => self::STAGE_FINAL,   'name' => 'Final Payment'],
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
