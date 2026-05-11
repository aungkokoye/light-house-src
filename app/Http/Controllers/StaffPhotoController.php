<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\StaffProfileManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StaffPhotoController extends Controller
{
    public function __construct(private readonly StaffProfileManager $manager) {}

    public function uploadForUser(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:20480'],
        ]);

        $profile = $user->staffProfile;

        if (! $profile) {
            return response()->json(['message' => 'Staff profile not found.'], 404);
        }

        $this->manager->uploadPhoto($profile, $request->file('photo'));

        return response()->json(['photo_url' => $profile->fresh()->photo_url]);
    }
}
