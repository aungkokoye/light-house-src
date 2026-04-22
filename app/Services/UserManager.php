<?php

namespace App\Services;

use App\Concerns\DispatchesAuditEvents;
use App\Filters\UserFilter;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\CompanyProfileManager;
use App\Services\StaffProfileManager;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserManager
{
    use DispatchesAuditEvents;

    public function __construct(
        private UserRepository $repo,
        private CompanyProfileManager $companyProfileManager,
        private StaffProfileManager $staffProfileManager,
    ) {}

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
        ?array $companyProfile = null,
        ?array $staffProfile = null,
        ?array $staffRole = null,
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

        $createdBy = Auth::id();

        if ($role === 'customer' && $companyProfile) {
            $this->companyProfileManager->create($user, $companyProfile, $createdBy);
        } elseif ($role !== 'customer' && $staffProfile) {
            $this->staffProfileManager->create($user, $staffProfile, $staffRole ?? [], $createdBy);
        }

        $user = $user->fresh(['roles', 'permissions', 'staffProfile.staffRoles', 'companyProfile']);
        $this->auditCreated($user, $this->buildSnapshot($user));

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
        ?array $companyProfile = null,
        ?array $staffProfile = null,
        ?array $staffRole = null,
    ): User {
        $user->load(['roles', 'permissions', 'staffProfile.staffRoles', 'companyProfile']);
        $oldValues = $this->buildSnapshot($user);
        $hasSuper  = Auth::user()?->hasPermissionTo('super') ?? false;

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

        $createdBy = Auth::id();

        if ($role === 'customer' && $companyProfile) {
            $this->companyProfileManager->upsert($user, $companyProfile, $createdBy);
        } elseif ($role !== 'customer' && $staffProfile) {
            $this->staffProfileManager->upsert($user, $staffProfile, $staffRole, $createdBy);
        }

        $user = $user->fresh(['roles', 'permissions', 'staffProfile.staffRoles', 'companyProfile']);
        $this->auditUpdated($user, $oldValues, $this->buildSnapshot($user));

        return $user;
    }

    public function delete(User $user): void
    {
        $user->load(['roles', 'permissions', 'staffProfile.staffRoles', 'companyProfile']);
        $this->auditDeleted($user, $this->buildSnapshot($user));
        $user->syncRoles([]);
        $user->syncPermissions([]);
        $this->repo->delete($user);
    }

    public function availableRoles(): array
    {
        return Role::pluck('name')->toArray();
    }

    private function buildSnapshot(User $user): array
    {
        return array_merge(
            $this->filterAuditValues($user->getAttributes()),
            [
                'roles'          => $user->roles->pluck('name')->toArray(),
                'permissions'    => $user->permissions->pluck('name')->toArray(),
                'staffProfile'   => $user->staffProfile?->toArray(),
                'companyProfile' => $user->companyProfile?->toArray(),
            ]
        );
    }

    private function filterPermissions(array $permissions): array
    {
        $caller = Auth::user();

        if (! $caller) {
            return $permissions;
        }

        if ($caller->hasPermissionTo('super')) {
            return $permissions;
        }

        return array_values(array_filter($permissions, fn($p) => $p !== 'super'));
    }
}