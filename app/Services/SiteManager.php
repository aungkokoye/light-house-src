<?php

namespace App\Services;

use App\Filters\SiteFilter;
use App\Models\Site;
use App\Repositories\SiteRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class SiteManager
{
    const array PER_PAGE_LIST   = [10, 20, 30, 40, 50];
    const int   DEFAULT_PER_PAGE = 20;

    public function __construct(private readonly SiteRepository $repo) {}

    public function list(Request $request, int $perPage): LengthAwarePaginator
    {
        $perPage = in_array($perPage, self::PER_PAGE_LIST) ? $perPage : self::DEFAULT_PER_PAGE;

        $query = SiteFilter::for($this->repo->query())
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

    public function show(Site $site): Site
    {
        return $site->load(['createdBy:id,name,email']);
    }

    public function create(array $data): Site
    {
        $site = $this->repo->create([...$data, 'created_by' => Auth::id()]);

        return $site->load(['createdBy:id,name,email']);
    }

    public function update(Site $site, array $data): Site
    {
        $site = $this->repo->update($site, $data);

        return $site->load(['createdBy:id,name,email']);
    }

    public function delete(Site $site): void
    {
        $this->repo->delete($site);
    }
}
