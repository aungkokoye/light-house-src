<?php

namespace App\Services;

use App\Filters\PermissionFilter;
use App\Models\User;
use App\Repositories\PermissionRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class PermissionManager
{
    public function __construct(private readonly PermissionRepository $repo) {}

    /**
     * Full list (no pagination — permissions are few and used in dropdowns too).
     * Attaches users_count to each item in one batch query.
     */
    public function list(Request $request): Collection
    {
        $query = PermissionFilter::for($this->repo->query())
            ->search($request->input('search'))
            ->sort($request->input('sort_by', 'id'), $request->input('sort_dir', 'asc'))
            ->query();

        $permissions = $this->repo->getAll($query);

        $counts = $this->repo->userCounts($permissions->pluck('id'));
        $permissions->each(fn($p) => $p->users_count = $counts[$p->id] ?? 0);

        return $permissions;
    }

    public function show(Permission $permission): Permission
    {
        $permission->users_count = $this->repo->userCount($permission->id);

        $creator = $permission->created_by
            ? User::find($permission->created_by, ['id', 'name', 'email'])
            : null;

        $permission->created_by_name  = $creator?->name  ?? config('app.default_creator_name');
        $permission->created_by_email = $creator?->email ?? config('app.default_creator_email');

        return $permission;
    }

    public function create(string $name): Permission
    {
        $permission = $this->repo->create([
            'name'       => $name,
            'guard_name' => 'web',
            'created_by' => Auth::id(),
        ]);

        $permission->users_count = 0;

        return $permission;
    }

    public function update(Permission $permission, string $name): Permission
    {
        $permission = $this->repo->update($permission, ['name' => $name]);
        $permission->users_count = $this->repo->userCount($permission->id);

        return $permission;
    }

    public function delete(Permission $permission): void
    {
        $this->repo->delete($permission);
    }
}
