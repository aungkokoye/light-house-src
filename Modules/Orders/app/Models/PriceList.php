<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use Modules\Orders\Database\Factories\PriceListFactory;

class PriceList extends Model
{
    use HasFactory;

    protected $table = 'price_list';

    protected static function newFactory(): PriceListFactory
    {
        return PriceListFactory::new();
    }

    protected $fillable = [
        'job_service_id',
        'product_description',
        'dimension',
        'price',
        'remark',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
        ];
    }

    public function jobService(): BelongsTo
    {
        return $this->belongsTo(JobService::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
