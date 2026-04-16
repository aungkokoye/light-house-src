<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CompanyProfileManager;
use App\Services\EmailManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request, EmailManager $emailManager, CompanyProfileManager $companyProfileManager): JsonResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255', 'unique:users'],
            'email'    => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:8', 'confirmed'],
            'captcha'  => ['required', 'string'],

            'company_profile'             => ['required', 'array'],
            'company_profile.name'        => ['required', 'string', 'max:255'],
            'company_profile.role'        => ['required', 'string', 'max:255'],
            'company_profile.description' => ['nullable', 'string', 'max:10000'],
            'company_profile.address'     => ['required', 'string', 'max:2000'],
            'company_profile.phone'       => ['required', 'string', 'max:50'],
        ], [], [
            'company_profile.name'        => 'company name',
            'company_profile.role'        => 'role / title',
            'company_profile.description' => 'description',
            'company_profile.address'     => 'address',
            'company_profile.phone'       => 'phone',
        ]);

        $expected = Session::get('captcha');

        if (! $expected || strtolower($request->captcha) !== $expected) {
            Session::forget('captcha');
            throw ValidationException::withMessages([
                'captcha' => ['The captcha code is incorrect. Please try again.'],
            ]);
        }

        Session::forget('captcha');

        try {
            $user = DB::transaction(function () use ($validated, $companyProfileManager) {
                $user = User::create([
                    'name'     => $validated['name'],
                    'email'    => $validated['email'],
                    'password' => $validated['password'],
                ]);

                $user->assignRole('customer');

                $companyProfileManager->create($user, $validated['company_profile']);

                return $user;
            });

            $emailManager->sendVerificationEmail($user);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'We could not process your request. Please try again.',
            ], 500);
        }

        return response()->json([
            'message' => 'Registration successful. Please check your email to verify your account.',
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
            'captcha'  => ['required', 'string'],
        ]);

        $expected = Session::get('captcha');

        if (! $expected || strtolower($request->captcha) !== $expected) {
            Session::forget('captcha');
            throw ValidationException::withMessages([
                'captcha' => ['The captcha code is incorrect. Please try again.'],
            ]);
        }

        Session::forget('captcha');

        if (! Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (! $request->user()->hasVerifiedEmail()) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => ['Please verify your email address before logging in.'],
            ]);
        }

        if (! $request->user()->activated) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated. Please contact support.'],
            ]);
        }

        $token = $request->user()->createToken('api-token')->plainTextToken;

        return response()->json(['token' => $token]);
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->currentAccessToken();
        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }

        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['roles', 'permissions']);

        if ($user->isCompany()) {
            $user->load('companyProfile');
        } elseif ($user->isStaff()) {
            $user->load([
                'staffProfile.staffRoles' => fn($q) => $q->whereNull('end_date')->with(['position', 'site'])->limit(1),
            ]);
        }

        return response()->json($user);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->password) {
            return response()->json(['message' => 'Password cannot be changed for Google accounts.'], 422);
        }

        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $user->forceFill(['password' => Hash::make($request->password)])->save();

        return response()->json(['message' => 'Password updated successfully.']);
    }

    public function completeCompanyProfile(Request $request, CompanyProfileManager $companyProfileManager): JsonResponse
    {
        $user = $request->user()->load('roles');

        if (! $user->isCompany()) {
            return response()->json(['message' => 'Only customer accounts can have a company profile.'], 403);
        }

        if ($user->companyProfile()->exists()) {
            return response()->json(['message' => 'Company profile already exists.'], 422);
        }

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'role'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'address'     => ['required', 'string', 'max:2000'],
            'phone'       => ['required', 'string', 'max:50'],
        ], [], [
            'name'    => 'company name',
            'role'    => 'role / title',
            'address' => 'address',
            'phone'   => 'phone',
        ]);

        $companyProfileManager->create($user, $data);

        return response()->json(['message' => 'Company profile saved.']);
    }

    public function resendVerification(Request $request, EmailManager $emailManager): JsonResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = User::where('email', $request->email)->firstOrFail();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.']);
        }

        try {
            $emailManager->sendVerificationEmail($user);
        } catch (\Throwable $e) {
            return response()->json(
                ['message' => 'Failed to send verification email. Please try again.'],
                500
            );
        }

        return response()->json(['message' => 'Verification email sent.']);
    }
}
