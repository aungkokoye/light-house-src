<?php

namespace App\Services;

use App\Models\StaffProfile;
use App\Models\StaffRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StaffProfileManager
{
    public function create(User $user, array $profileData, array $roleData, ?int $createdBy = null): StaffProfile
    {
        $profile = StaffProfile::create([
            'user_id'                  => $user->id,
            'full_name'                => $profileData['full_name'] ?? null,
            'father_name'              => $profileData['father_name'] ?? null,
            'gender'                   => $profileData['gender'] ?? null,
            'marital_status'           => $profileData['marital_status'] ?? null,
            'religion'                 => $profileData['religion'] ?? null,
            'ethnic_group'             => $profileData['ethnic_group'] ?? null,
            'uniform_size'             => $profileData['uniform_size'] ?? null,
            'education_qualification'  => $profileData['education_qualification'] ?? null,
            'work_experience'          => $profileData['work_experience'] ?? null,
            'home_address'             => $profileData['home_address'] ?? null,
            'note'                     => $profileData['note'] ?? null,
            'nrc_no'                   => $profileData['nrc_no'] ?? null,
            'dob'                      => $profileData['dob'] ?? null,
            'address'                  => $profileData['address'] ?? null,
            'phone'                    => $profileData['phone'] ?? null,
            'start_date'               => $profileData['start_date'] ?? null,
            'created_by'               => $createdBy,
        ]);

        StaffRole::create([
            'staff_profile_id'  => $profile->id,
            'staff_position_id' => $roleData['staff_position_id'] ?? null,
            'site_id'           => $roleData['site_id'] ?? null,
            'salary'            => isset($roleData['salary'])      ? (int) $roleData['salary']      : null,
            'overtime_hourly_rate'       => isset($roleData['overtime_hourly_rate']) ? (int) $roleData['overtime_hourly_rate'] : null,
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
                'full_name'               => $profileData['full_name'] ?? null,
                'father_name'             => $profileData['father_name'] ?? null,
                'gender'                  => $profileData['gender'] ?? null,
                'marital_status'          => $profileData['marital_status'] ?? null,
                'religion'                => $profileData['religion'] ?? null,
                'ethnic_group'            => $profileData['ethnic_group'] ?? null,
                'uniform_size'            => $profileData['uniform_size'] ?? null,
                'education_qualification' => $profileData['education_qualification'] ?? null,
                'work_experience'         => $profileData['work_experience'] ?? null,
                'home_address'            => $profileData['home_address'] ?? null,
                'note'                    => $profileData['note'] ?? null,
                'nrc_no'                  => $profileData['nrc_no'] ?? null,
                'dob'                     => $profileData['dob'] ?? null,
                'address'                 => $profileData['address'] ?? null,
                'phone'                   => $profileData['phone'] ?? null,
                'start_date'              => $profileData['start_date'] ?? null,
                'created_by'              => $createdBy,
            ]
        );

        if ($roleData && ! empty(array_filter($roleData))) {
            StaffRole::updateOrCreate(
                ['staff_profile_id' => $profile->id, 'end_date' => null],
                [
                    'staff_position_id' => $roleData['staff_position_id'] ?? null,
                    'site_id'           => $roleData['site_id'] ?? null,
                    'salary'            => isset($roleData['salary'])      ? (int) $roleData['salary']      : null,
                    'overtime_hourly_rate'       => isset($roleData['overtime_hourly_rate']) ? (int) $roleData['overtime_hourly_rate'] : null,
                    'start_date'        => $roleData['start_date'] ?? null,
                    'created_by'        => $createdBy,
                ]
            );
        }

        return $profile;
    }

    public function uploadPhoto(StaffProfile $profile, UploadedFile $file): string
    {
        if ($profile->photo) {
            Storage::disk('public')->delete($profile->photo);
        }

        $mime = $file->getMimeType();
        $src  = match (true) {
            str_contains($mime, 'png')  => imagecreatefrompng($file->getRealPath()),
            str_contains($mime, 'webp') => imagecreatefromwebp($file->getRealPath()),
            default                     => imagecreatefromjpeg($file->getRealPath()),
        };

        $origW = imagesx($src);
        $origH = imagesy($src);

        if ($origW > 1920) {
            $newW = 1920;
            $newH = (int) round($origH * (1920 / $origW));
            $dst  = imagecreatetruecolor($newW, $newH);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
            imagedestroy($src);
            $src = $dst;
        }

        $maxBytes = 10 * 1024 * 1024;
        $jpeg     = '';
        foreach ([85, 60, 40] as $quality) {
            ob_start();
            imagejpeg($src, null, $quality);
            $jpeg = ob_get_clean();
            if (strlen($jpeg) <= $maxBytes) {
                break;
            }
        }

        imagedestroy($src);

        $path = 'staff-photos/' . Str::uuid() . '.jpg';
        Storage::disk('public')->put($path, $jpeg);
        $profile->update(['photo' => $path]);

        return $path;
    }
}