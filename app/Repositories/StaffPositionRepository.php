<?php

namespace App\Repositories;

use App\Models\StaffPosition;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class StaffPositionRepository
{
    public function query(): Builder
    {
        return StaffPosition::query();
    }

    public function paginate(Builder $query, int $perPage): LengthAwarePaginator
    {
        return $query->paginate($perPage);
    }

    public function all(): Collection
    {
        return StaffPosition::orderBy('name')->get(['id', 'name']);
    }

    public function create(array $data): StaffPosition
    {
        return StaffPosition::create($data);
    }

    public function update(StaffPosition $position, array $data): StaffPosition
    {
        $position->update($data);

        return $position;
    }

    public function delete(StaffPosition $position): void
    {
        $position->delete();
    }
}
