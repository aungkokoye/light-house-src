<?php

namespace App\Repositories;

use App\Models\Site;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class SiteRepository
{
    public function query(): Builder
    {
        return Site::query();
    }

    public function paginate(Builder $query, int $perPage): LengthAwarePaginator
    {
        return $query->paginate($perPage);
    }

    public function all(): Collection
    {
        return Site::orderBy('name')->get(['id', 'name']);
    }

    public function create(array $data): Site
    {
        return Site::create($data);
    }

    public function update(Site $site, array $data): Site
    {
        $site->update($data);

        return $site;
    }

    public function delete(Site $site): void
    {
        $site->delete();
    }
}
