<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class PermissionRepository
{
    public function query(): Builder
    {
        return Permission::query();
    }

    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return Permission::orderBy('name')->get(['id', 'name']);
    }

    public function paginate(Builder $query, int $perPage): LengthAwarePaginator
    {
        return $query->paginate($perPage);
    }

    public function getAll(Builder $query): \Illuminate\Database\Eloquent\Collection
    {
        return $query->get();
    }

    public function create(array $data): Permission
    {
        return Permission::create($data);
    }

    public function update(Permission $permission, array $data): Permission
    {
        $permission->update($data);

        return $permission;
    }

    public function delete(Permission $permission): void
    {
        // Clean up Spatie pivot — no FK cascade on polymorphic model_id
        DB::table('model_has_permissions')->where('permission_id', $permission->id)->delete();
        $permission->delete();
    }

    /**
     * Return a permission_id → user count map for the given permission IDs.
     */
    public function userCounts(iterable $permissionIds): \Illuminate\Support\Collection
    {
        return DB::table('model_has_permissions')
            ->selectRaw('permission_id, count(*) as total')
            ->whereIn('permission_id', $permissionIds)
            ->groupBy('permission_id')
            ->pluck('total', 'permission_id');
    }

    /**
     * Return the user count for a single permission.
     */
    public function userCount(int $permissionId): int
    {
        return DB::table('model_has_permissions')
            ->where('permission_id', $permissionId)
            ->count();
    }
}
