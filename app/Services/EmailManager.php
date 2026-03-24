<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;

class EmailManager
{
    public const TOKEN_EXPIRES_HOURS = 24;

    public function sendVerificationEmail(User $user): void
    {
        if ($user->hasVerifiedEmail()) {
            return;
        }

        $token = Str::random(64);

        $user->forceFill([
            'email_verification_token'      => $token,
            'email_verification_expires_at' => now()->addHours(self::TOKEN_EXPIRES_HOURS),
        ])->save();

        $user->notify(new \App\Notifications\VerifyEmailNotification($token));
    }
}