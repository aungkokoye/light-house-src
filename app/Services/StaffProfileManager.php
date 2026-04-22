<?php

namespace App\Services;

use App\Models\StaffProfile;
use App\Models\StaffRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class StaffProfileManager
{
    public function create(User $user, array $profileData, array $roleData, ?int $createdBy = null): StaffProfile
    {
        $profile = StaffProfile::create([
            'user_id'    => $user->id,
            'full_name'  => $profileData['full_name'] ?? null,
            'nrc_no'     => $profileData['nrc_no'] ?? null,
            'dob'        => $profileData['dob'] ?? null,
            'address'    => $profileData['address'] ?? null,
            'phone'      => $profileData['phone'] ?? null,
            'start_date' => $profileData['start_date'] ?? null,
            'created_by' => $createdBy,
        ]);

        StaffRole::create([
            'staff_profile_id'  => $profile->id,
            'staff_position_id' => $roleData['staff_position_id'] ?? null,
            'site_id'           => $roleData['site_id'] ?? null,
            'salary'            => isset($roleData['salary']) ? (int) $roleData['salary'] : null,
            'start_date'        => $roleData['start_date'] ?? null,
            'created_by'        => $createdBy,
        ]);

        return $profile;
    }

    public function upsert(User $user, array $profileData, ?array $roleData = null, ?int $createdBy = null): StaffProfile | Model
    {
        $profile = $user->staffProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'full_name'  => $profileData['full_name'] ?? null,
                'nrc_no'     => $profileData['nrc_no'] ?? null,
                'dob'        => $profileData['dob'] ?? null,
                'address'    => $profileData['address'] ?? null,
                'phone'      => $profileData['phone'] ?? null,
                'start_date' => $profileData['start_date'] ?? null,
                'created_by' => $createdBy,
            ]
        );

        if ($roleData && ! empty(array_filter($roleData))) {
            StaffRole::updateOrCreate(
                ['staff_profile_id' => $profile->id, 'end_date' => null],
                [
                    'staff_position_id' => $roleData['staff_position_id'] ?? null,
                    'site_id'           => $roleData['site_id'] ?? null,
                    'salary'            => isset($roleData['salary']) ? (int) $roleData['salary'] : null,
                    'start_date'        => $roleData['start_date'] ?? null,
                    'created_by'        => $createdBy,
                ]
            );
        }

        return $profile;
    }
}