<?php

namespace App\Services;

use App\Filters\AuditLogFilter;
use App\Models\AuditLog;
use App\Repositories\AuditLogRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class AuditLogManager
{
    const array PER_PAGE_LIST    = [10, 20, 30, 40, 50];
    const int   DEFAULT_PER_PAGE = 20;

    public function __construct(private readonly AuditLogRepository $repo) {}

    public function list(Request $request, int $perPage): LengthAwarePaginator
    {
        $perPage = in_array($perPage, self::PER_PAGE_LIST) ? $perPage : self::DEFAULT_PER_PAGE;

        $query = AuditLogFilter::for($this->repo->query())
            ->search($request->input('search'))
            ->event($request->input('event'))
            ->sort($request->input('sort_by', 'created_at'), $request->input('sort_dir', 'desc'))
            ->query();

        return $this->repo->paginate($query, $perPage);
    }

    public function show(AuditLog $log): AuditLog
    {
        return $log;
    }
}