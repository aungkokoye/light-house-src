<?php

namespace Modules\Orders\Services;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Modules\Orders\Filters\JobServiceFilter;
use Modules\Orders\Models\JobService;

class JobServiceManager
{
    public function list(Request $request, int $perPage): LengthAwarePaginator
    {
        return JobServiceFilter::for(JobService::withCount('invoiceJobs')->with('createdBy:id,name'))
            ->search($request->input('search'))
            ->sort($request->input('sort_by', 'name'), $request->input('sort_dir', 'asc'))
            ->query()
            ->paginate($perPage);
    }

    public function show(JobService $service): JobService
    {
        return $service->load('createdBy:id,name');
    }

    public function create(array $data): JobService
    {
        return JobService::create([...$data, 'created_by' => Auth::id()])
            ->load('createdBy:id,name');
    }

    public function update(JobService $service, array $data): JobService
    {
        $service->update($data);

        return $service->refresh()->load('createdBy:id,name');
    }

    public function delete(JobService $service): void
    {
        $service->delete();
    }
}
