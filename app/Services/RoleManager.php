<?php

namespace App\Services;

use App\Filters\RoleFilter;
use App\Models\User;
use App\Repositories\RoleRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class RoleManager
{
    const array PER_PAGE_LIST   = [10, 20, 30, 40, 50];
    const int   DEFAULT_PER_PAGE = 20;

    public function __construct(private readonly RoleRepository $repo) {}

    public function list(Request $request, int $perPage): LengthAwarePaginator
    {
        $perPage = in_array($perPage, self::PER_PAGE_LIST) ? $perPage : self::DEFAULT_PER_PAGE;

        $query = RoleFilter::for($this->repo->query())
            ->search($request->input('search'))
            ->sort($request->input('sort_by', 'updated_at'), $request->input('sort_dir', 'desc'))
            ->query();

        $paginated = $this->repo->paginate($query, $perPage);

        $counts = $this->repo->userCounts($paginated->pluck('id'));
        $paginated->each(fn($role) => $role->users_count = $counts[$role->id] ?? 0);

        return $paginated;
    }

    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repo->all();
    }

    public function show(Role $role): Role
    {
        $role->users_count = $this->repo->userCount($role->id);

        $creator = $role->created_by
            ? User::find($role->created_by, ['id', 'name', 'email'])
            : null;

        $role->created_by_name  = $creator?->name  ?? config('app.default_creator_name');
        $role->created_by_email = $creator?->email ?? config('app.default_creator_email');

        return $role;
    }

    public function create(string $name): Role
    {
        $role = $this->repo->create([
            'name'       => $name,
            'guard_name' => 'web',
            'created_by' => Auth::id(),
        ]);

        $role->users_count = 0;

        return $role;
    }

    public function update(Role $role, string $name): Role
    {
        $role = $this->repo->update($role, [
            'name'       => $name,
            'created_by' => Auth::id(),
        ]);

        $role->users_count = $this->repo->userCount($role->id);

        return $role;
    }

    public function delete(Role $role): void
    {
        $this->repo->delete($role);
    }
}
