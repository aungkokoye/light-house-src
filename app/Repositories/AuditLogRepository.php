<?php

namespace App\Repositories;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class AuditLogRepository
{
    public function query(): Builder
    {
        return AuditLog::query()->with('user:id,name,email');
    }

    public function paginate(Builder $query, int $perPage): LengthAwarePaginator
    {
        return $query->paginate($perPage);
    }
}