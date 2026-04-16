<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Throwable $e) {
                return redirect('/login?error=google_failed');
        }

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            if (! $user->google_id) {
                $user->forceFill(['google_id' => $googleUser->getId()])->save();
            }
        } else {
            $name = $googleUser->getName();

            // Ensure name is unique
            if (User::where('name', $name)->exists()) {
                $name = $name . '_' . substr($googleUser->getId(), -4);
            }

            try {
                $user = User::create([
                    'name'              => $name,
                    'email'             => $googleUser->getEmail(),
                    'google_id'         => $googleUser->getId(),
                    'email_verified_at' => now(),
                    'password'          => null,
                ]);
                $user->assignRole('customer');
            } catch (\Throwable $e) {
                return redirect('/login?error=google_failed');
            }
        }

        if (! $user->activated) {
            return redirect('/login?error=account_deactivated');
        }

        $token = $user->createToken('api-token')->plainTextToken;

        $needsProfile = $user->isCompany() && ! $user->companyProfile()->exists();

        $url = '/auth/callback?token=' . urlencode($token);
        if ($needsProfile) {
            $url .= '&needs_profile=1';
        }

        return redirect($url);
    }
}
