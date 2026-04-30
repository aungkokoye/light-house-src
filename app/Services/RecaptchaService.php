<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class RecaptchaService
{
    public function verify(string $token, ?string $ip = null): void
    {
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => config('services.recaptcha.secret'),
            'response' => $token,
            'remoteip' => $ip,
        ]);

        if (! $response->successful() || ! $response->json('success')) {
            throw ValidationException::withMessages([
                'recaptcha_token' => ['reCAPTCHA verification failed. Please try again.'],
            ]);
        }
    }
}
