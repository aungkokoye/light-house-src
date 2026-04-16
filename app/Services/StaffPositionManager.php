<?php

namespace App\Services;

use App\Filters\StaffPositionFilter;
use App\Models\StaffPosition;
use App\Repositories\StaffPositionRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class StaffPositionManager
{
    const array PER_PAGE_LIST   = [10, 20, 30, 40, 50];
    const int   DEFAULT_PER_PAGE = 20;

    public function __construct(private readonly StaffPositionRepository $repo) {}

    public function list(Request $request, int $perPage): LengthAwarePaginator
    {
        $perPage = in_array($perPage, self::PER_PAGE_LIST) ? $perPage : self::DEFAULT_PER_PAGE;

        $query = StaffPositionFilter::for($this->repo->query())
            ->search($request->input('search'))
            ->createdFrom($request->input('created_from'))
            ->createdTo($request->input('created_to'))
            ->sort($request->input('sort_by', 'name'), $request->input('sort_dir', 'asc'))
            ->query();

        return $this->repo->paginate($query, $perPage);
    }

    public function all(): Collection
    {
        return $this->repo->all();
    }

    public function show(StaffPosition $position): StaffPosition
    {
        return $position->load(['createdBy:id,name,email']);
    }

    public function create(array $data): StaffPosition
    {
        $position = $this->repo->create([...$data, 'created_by' => Auth::id()]);

        return $position->load(['createdBy:id,name,email']);
    }

    public function update(StaffPosition $position, array $data): StaffPosition
    {
        $position = $this->repo->update($position, $data);

        return $position->load(['createdBy:id,name,email']);
    }

    public function delete(StaffPosition $position): void
    {
        $this->repo->delete($position);
    }
}
