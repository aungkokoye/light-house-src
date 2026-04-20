<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\ContactInquiryNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class EmailManager
{
    public const TOKEN_EXPIRES_HOURS = 24;

    public function sendContactInquiry(string $name, string $contact, string $service, string $message): void
    {
        Notification::route('mail', config('mail.contact_inquiry_email'))
            ->notify(new ContactInquiryNotification($name, $contact, $service, $message));
    }

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