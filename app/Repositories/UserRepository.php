<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class UserRepository
{
    public function query(): Builder
    {
        return User::with([
            'roles',
            // Load profiles for all users; companyProfile for customers,
            // staffProfile.currentRole.position for staff — Eloquent batches these
            // so it's 3 queries regardless of result set size.
            'companyProfile',
            'staffProfile.currentRole.position',
        ]);
    }

    public function paginate(Builder $query, int $perPage): LengthAwarePaginator
    {
        return $query->paginate($perPage);
    }

    public function findById(int $id): ?User
    {
        return User::with('roles')->find($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user;
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}