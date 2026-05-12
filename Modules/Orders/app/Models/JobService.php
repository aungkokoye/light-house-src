<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class JobService extends Model
{
    protected $table = 'job_services';

    protected $fillable = [
        'name',
        'description',
        'created_by',
    ];

    public function invoiceJobs(): HasMany
    {
        return $this->hasMany(InvoiceJob::class, 'service_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
