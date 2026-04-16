<?php

namespace App\Services;

use App\Models\CompanyProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CompanyProfileManager
{
    /**
     * Create a brand-new company profile for a user.
     *
     * @param  array{name:string, role:string, description:string|null, address:string, phone:string}  $data
     */
    public function create(User $user, array $data, ?int $createdBy = null): CompanyProfile
    {
        return CompanyProfile::create([
            'user_id'     => $user->id,
            'name'        => $data['name'],
            'role'        => $data['role'] ?? null,
            'description' => $data['description'] ?? null,
            'address'     => $data['address'] ?? null,
            'phone'       => $data['phone'] ?? null,
            'created_by'  => $createdBy,
        ]);
    }

    /**
     * Create or update the company profile for a user.
     *
     * @param  array{name:string, role:string, description:string|null, address:string, phone:string}  $data
     */
    public function upsert(User $user, array $data, ?int $createdBy = null): Model | CompanyProfile
    {
        return $user->companyProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'name'        => $data['name'],
                'role'        => $data['role'] ?? null,
                'description' => $data['description'] ?? null,
                'address'     => $data['address'] ?? null,
                'phone'       => $data['phone'] ?? null,
                'created_by'  => $createdBy,
            ]
        );
    }
}
