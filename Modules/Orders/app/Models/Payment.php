<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Payment extends Model
{
    const TYPE_CASH  = 1;
    const TYPE_BANK  = 2;
    const TYPE_OTHER = 3;

    const STAGE_ADVANCE = 1;
    const STAGE_FINAL   = 2;

    protected $fillable = [
        'invoice_id',
        'type_id',
        'bank_id',
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

    public static function typeOptions(): array
    {
        return [
            ['id' => self::TYPE_CASH,  'name' => 'Cash'],
            ['id' => self::TYPE_BANK,  'name' => 'Bank'],
            ['id' => self::TYPE_OTHER, 'name' => 'Other'],
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

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
