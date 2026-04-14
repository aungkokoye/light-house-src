<?php

namespace App\Http\Controllers;

use App\Services\EmailManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    public function __construct(private readonly EmailManager $emailManager) {}

    public function send(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'contact' => ['required', 'string', 'max:255'],
            'service' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
            'captcha' => ['required', 'string'],
        ]);

        $expected = Session::get('captcha');
        if (! $expected || strtolower($data['captcha']) !== $expected) {
            Session::forget('captcha');
            throw ValidationException::withMessages([
                'captcha' => ['The captcha code is incorrect. Please try again.'],
            ]);
        }
        Session::forget('captcha');

        $this->emailManager->sendContactInquiry(
            $data['name'],
            $data['contact'],
            $data['service'],
            $data['message'],
        );

        return response()->json(['message' => 'Your message has been sent.']);
    }
}