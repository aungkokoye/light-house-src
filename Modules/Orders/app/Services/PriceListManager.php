<?php

namespace Modules\Orders\Services;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Modules\Orders\Filters\PriceListFilter;
use Modules\Orders\Models\PriceList;

class PriceListManager
{
    public function list(Request $request, int $perPage): LengthAwarePaginator
    {
        $query = PriceList::with(['jobService:id,name', 'createdBy:id,name']);

        return PriceListFilter::for($query)
            ->search($request->input('search'))
            ->jobService($request->integer('job_service_id') ?: null)
            ->sort($request->input('sort_by', 'created_at'), $request->input('sort_dir', 'desc'))
            ->query()
            ->paginate($perPage);
    }

    public function show(PriceList $priceList): PriceList
    {
        return $priceList->load(['jobService:id,name', 'createdBy:id,name']);
    }

    public function create(array $data): PriceList
    {
        $priceList = PriceList::create([...$data, 'created_by' => Auth::id()]);

        return $priceList->load(['jobService:id,name', 'createdBy:id,name']);
    }

    public function update(PriceList $priceList, array $data): PriceList
    {
        $priceList->update($data);

        return $priceList->refresh()->load(['jobService:id,name', 'createdBy:id,name']);
    }

    public function delete(PriceList $priceList): void
    {
        $priceList->delete();
    }
}
