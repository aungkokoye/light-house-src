<?php

use App\Http\Controllers\Auth\SocialAuthController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// Email verification link handler
Route::get('/email/verify/{token}', function (string $token) {
    $user = User::where('email_verification_token', $token)->first();

    if (! $user) {
        return redirect('/login?verification=invalid');
    }

    if (now()->isAfter($user->email_verification_expires_at)) {
        $user->forceFill([
            'email_verification_token'      => null,
            'email_verification_expires_at' => null,
        ])->save();

        return redirect('/login?verification=expired&email=' . urlencode($user->email));
    }

    $user->forceFill([
        'email_verified_at'             => now(),
        'email_verification_token'      => null,
        'email_verification_expires_at' => null,
    ])->save();

    return $user->activated
        ? redirect('/login?verified=1')
        : redirect('/login?pending=1');
})->name('verification.verify');

// Google OAuth
Route::get('/auth/google/redirect', [SocialAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

// SPA catch-all — must be last
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');