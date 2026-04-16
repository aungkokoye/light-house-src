<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleRepository
{
    public function query(): Builder
    {
        return Role::query();
    }

    public function paginate(Builder $query, int $perPage): LengthAwarePaginator
    {
        return $query->paginate($perPage);
    }

    public function all(): Collection
    {
        return Role::orderBy('name')->get(['id', 'name']);
    }

    public function findById(int $id): ?Role
    {
        return Role::find($id);
    }

    public function create(array $data): Role
    {
        return Role::create($data);
    }

    public function update(Role $role, array $data): Role
    {
        $role->update($data);

        return $role;
    }

    public function delete(Role $role): void
    {
        // Clean up Spatie pivot — no FK cascade on polymorphic model_id
        DB::table('model_has_roles')->where('role_id', $role->id)->delete();
        $role->delete();
    }

    /**
     * Return a role_id → user count map for the given role IDs.
     */
    public function userCounts(iterable $roleIds): \Illuminate\Support\Collection
    {
        return DB::table('model_has_roles')
            ->selectRaw('role_id, count(*) as total')
            ->whereIn('role_id', $roleIds)
            ->groupBy('role_id')
            ->pluck('total', 'role_id');
    }

    /**
     * Return the user count for a single role.
     */
    public function userCount(int $roleId): int
    {
        return DB::table('model_has_roles')->where('role_id', $roleId)->count();
    }
}
