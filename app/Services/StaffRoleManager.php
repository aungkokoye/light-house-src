<?php

namespace App\Services;

use App\Filters\StaffRoleFilter;
use App\Models\StaffRole;
use App\Models\User;
use App\Repositories\StaffRoleRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class StaffRoleManager
{
    const array PER_PAGE_LIST    = [10, 20, 30, 40, 50];
    const int   DEFAULT_PER_PAGE = 20;

    public function __construct(private readonly StaffRoleRepository $repo) {}

    public function list(Request $request, User $user, int $perPage): LengthAwarePaginator
    {
        $perPage = in_array($perPage, self::PER_PAGE_LIST) ? $perPage : self::DEFAULT_PER_PAGE;

        $query = StaffRoleFilter::for($this->repo->queryForUser($user->id))
            ->sort($request->input('sort_by', ''), $request->input('sort_dir', 'desc'))
            ->query()
            ->with(['position', 'site']);

        return $this->repo->paginate($query, $perPage);
    }

    public function show(StaffRole $staffRole): StaffRole
    {
        return $staffRole->load(['position', 'site', 'createdBy:id,name,email']);
    }

    public function create(User $user, array $data): StaffRole
    {
        $staffProfile = $user->staffProfile;

        if (! $staffProfile) {
            abort(422, 'User does not have a staff profile.');
        }

        $this->repo->closeActive($staffProfile, $data['start_date']);

        $role = $this->repo->create([
            'staff_profile_id'  => $staffProfile->id,
            'staff_position_id' => $data['staff_position_id'],
            'site_id'           => $data['site_id'],
            'salary'            => $data['salary'],
            'start_date'        => $data['start_date'],
            'created_by'        => Auth::id(),
        ]);

        return $role->load(['position', 'site', 'createdBy:id,name,email']);
    }

    public function update(StaffRole $staffRole, array $data): StaffRole
    {
        $staffRole = $this->repo->update($staffRole, $data);

        return $staffRole->load(['position', 'site', 'createdBy:id,name,email']);
    }

    public function delete(StaffRole $staffRole): void
    {
        if (is_null($staffRole->end_date)) {
            abort(422, 'Cannot delete the current active staff role.');
        }

        $this->repo->delete($staffRole);
    }

    public function authorize(User $user, StaffRole $staffRole): void
    {
        $staffRole->loadMissing('staffProfile');

        if ($staffRole->staffProfile->user_id !== $user->id) {
            abort(403);
        }
    }
}
