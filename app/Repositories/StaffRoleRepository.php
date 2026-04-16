<?php

namespace App\Repositories;

use App\Models\StaffProfile;
use App\Models\StaffRole;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class StaffRoleRepository
{
    public function queryForUser(int $userId): Builder
    {
        return StaffRole::query()
            ->whereHas('staffProfile', fn($q) => $q->where('user_id', $userId));
    }

    public function paginate(Builder $query, int $perPage): LengthAwarePaginator
    {
        return $query->paginate($perPage);
    }

    public function create(array $data): StaffRole
    {
        return StaffRole::create($data);
    }

    public function update(StaffRole $staffRole, array $data): StaffRole
    {
        $staffRole->update($data);

        return $staffRole;
    }

    public function delete(StaffRole $staffRole): void
    {
        $staffRole->delete();
    }

    public function closeActive(StaffProfile $staffProfile, string $beforeDate): void
    {
        StaffRole::where('staff_profile_id', $staffProfile->id)
            ->whereNull('end_date')
            ->update(['end_date' => Carbon::parse($beforeDate)->subDay()->toDateString()]);
    }
}
