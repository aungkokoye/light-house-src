<?php

namespace App\Services;

use App\Filters\UserFilter;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserManager
{
    public function __construct(private UserRepository $repo) {}

    public function list(Request $request, int $perPage): LengthAwarePaginator
    {
        $query = UserFilter::for($this->repo->query())
            ->search($request->input('search'))
            ->position($request->input('position'))
            ->role($request->input('role'))
            ->activated($request->input('activated'))
            ->emailVerified($request->input('email_verified'))
            ->updatedFrom($request->input('updated_from'))
            ->updatedTo($request->input('updated_to'))
            ->sort($request->input('sort_by', 'updated_at'), $request->input('sort_dir', 'desc'))
            ->query();

        return $this->repo->paginate($query, $perPage);
    }

    public function create(
        string $name,
        string $email,
        string $password,
        string $role,
        bool $activated = true,
        bool $emailVerified = false,
        array $permissions = [],
    ): User {
        $user = $this->repo->create([
            'name'              => $name,
            'email'             => $email,
            'password'          => Hash::make($password),
            'activated'         => $activated,
            'email_verified_at' => $emailVerified ? now() : null,
            'created_by'        => Auth::id(),
        ]);

        $user->assignRole($role);
        $user->syncPermissions($this->filterPermissions($permissions));

        return $user;
    }

    public function update(
        User $user,
        string $name,
        string $email,
        string $role,
        bool $activated,
        bool $emailVerified,
        ?string $password = null,
        array $permissions = [],
    ): User {
        $hasSuper = Auth::user()?->hasPermissionTo('super') ?? false;

        $data = [
            'name'              => $name,
            'email'             => $email,
            'activated'         => $activated,
            'email_verified_at' => $emailVerified ? ($user->email_verified_at ?? now()) : null,
            'created_by'        => Auth::id(),
        ];

        if ($hasSuper && $password) {
            $data['password'] = Hash::make($password);
        }

        $user = $this->repo->update($user, $data);

        if ($hasSuper) {
            $user->syncRoles([$role]);
            $user->syncPermissions($this->filterPermissions($permissions));
        }

        return $user->fresh('roles');
    }

    private function filterPermissions(array $permissions): array
    {
        $caller = Auth::user();

        // No authenticated user (CLI / seeder) — allow everything through
        if (! $caller) {
            return $permissions;
        }

        if ($caller->hasPermissionTo('super')) {
            return $permissions;
        }

        return array_values(array_filter($permissions, fn($p) => $p !== 'super'));
    }

    public function delete(User $user): void
    {
        $user->syncRoles([]);
        $user->syncPermissions([]);
        $this->repo->delete($user);
    }

    public function availableRoles(): array
    {
        return Role::pluck('name')->toArray();
    }
}
