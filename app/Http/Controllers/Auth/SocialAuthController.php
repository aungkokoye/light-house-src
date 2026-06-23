<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserLoggedIn;
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

        $isNew = false;

        if ($user) {
            if (! $user->google_id) {
                $user->forceFill(['google_id' => $googleUser->getId()])->save();
            }

            if (! $user->activated) {
                return redirect('/login?error=account_deactivated');
            }
        } else {
            return redirect('/login?error=google_not_registered');
        }

        $token = $user->createToken('api-token')->plainTextToken;

        UserLoggedIn::dispatch(
            $user,
            request()->ip(),
            request()->userAgent() ?? '',
        );

        $needsProfile = $user->isCompany() && ! $user->companyProfile()->exists();

        $url = '/auth/callback?token=' . urlencode($token);
        if ($needsProfile) {
            $url .= '&needs_profile=1';
        }

        return redirect($url);
    }
}
