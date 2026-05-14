<?php

namespace App\Services;

use App\Filters\PermissionFilter;
use App\Models\User;
use App\Repositories\PermissionRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class PermissionManager
{
    const array PER_PAGE_LIST    = [10, 20, 30, 40, 50];
    const int   DEFAULT_PER_PAGE = 20;

    public function __construct(private readonly PermissionRepository $repo) {}

    public function list(Request $request, int $perPage): LengthAwarePaginator
    {
        $perPage = in_array($perPage, self::PER_PAGE_LIST) ? $perPage : self::DEFAULT_PER_PAGE;

        $query = PermissionFilter::for($this->repo->query())
            ->search($request->input('search'))
            ->sort($request->input('sort_by', 'id'), $request->input('sort_dir', 'asc'))
            ->query();

        $paginated = $this->repo->paginate($query, $perPage);

        $counts = $this->repo->userCounts($paginated->pluck('id'));
        $paginated->each(fn($p) => $p->users_count = $counts[$p->id] ?? 0);

        return $paginated;
    }

    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repo->all();
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
