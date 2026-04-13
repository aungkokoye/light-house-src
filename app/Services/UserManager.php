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

class UserManager
{
    public function __construct(private UserRepository $repo) {}

    public function list(Request $request, int $perPage): LengthAwarePaginator
    {
        $query = UserFilter::for($this->repo->query())
            ->search($request->input('search'))
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
    ): User {
        $data = [
            'name'              => $name,
            'email'             => $email,
            'activated'         => $activated,
            'email_verified_at' => $emailVerified ? ($user->email_verified_at ?? now()) : null,
            'created_by'        => Auth::id(),
        ];

        if ($password) {
            $data['password'] = Hash::make($password);
        }

        $user = $this->repo->update($user, $data);
        $user->syncRoles([$role]);

        return $user->fresh('roles');
    }

    public function delete(User $user): void
    {
        $this->repo->delete($user);
    }

    public function availableRoles(): array
    {
        return Role::pluck('name')->toArray();
    }
}
