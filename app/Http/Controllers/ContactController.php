<?php

namespace App\Http\Controllers;

use App\Services\EmailManager;
use App\Services\RecaptchaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function __construct(private readonly EmailManager $emailManager) {}

    public function send(Request $request, RecaptchaService $recaptcha): JsonResponse
    {
        $data = $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'contact'         => ['required', 'string', 'max:255'],
            'service'         => ['required', 'string', 'max:255'],
            'message'         => ['required', 'string', 'max:2000'],
            'recaptcha_token' => ['required', 'string'],
        ]);

        $recaptcha->verify($request->recaptcha_token, $request->ip());

        $this->emailManager->sendContactInquiry(
            $data['name'],
            $data['contact'],
            $data['service'],
            $data['message'],
        );

        return response()->json(['message' => 'Your message has been sent.']);
    }
}
