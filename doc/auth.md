# Authentication

Authentication is handled using [Laravel Sanctum](https://laravel.com/docs/sanctum) with token-based auth. Supports email/password login with email verification, and Google OAuth via [Laravel Socialite](https://laravel.com/docs/socialite). CAPTCHA is required on all public form submissions.

---

## Overview

```
Email/Password: Register (+ captcha) → Email Verification → Login (+ captcha) → Authenticated Requests → Logout
Forgot Password: Forgot Password → Reset Email → Set New Password → Login
Google OAuth:   Click "Continue with Google" → Google Consent → Callback → Token → Home
Complete Profile: Google user without company profile → /complete-profile (+ captcha) → Home
```

All auth API endpoints are prefixed with `/api`. The frontend is a Vue 3 SPA — forms submit via Axios, tokens are stored in `localStorage`, and every request automatically attaches the token via an Axios request interceptor.

---

## Password Requirements

All passwords must:
- Be at least **8 characters**
- Contain at least one **uppercase letter**
- Contain at least one **lowercase letter**
- Contain at least one **number**

Backend: `Password::min(8)->mixedCase()->numbers()` (`Illuminate\Validation\Rules\Password`)
Frontend placeholder: `"Min. 8 chars, A-z & 0-9"`
Frontend hint: `"Min. 8 characters with uppercase, lowercase and a number."`

---

## CAPTCHA

Public form submissions are protected by a server-rendered image CAPTCHA using `gregwar/captcha`.

- Image endpoint: `GET /captcha` (web route — starts PHP session)
- Consuming API routes must use `->middleware('web')` to share the session
- Server stores the answer in `Session::put('captcha', strtolower($phrase))`
- Validation: `strtolower($request->captcha) === Session::get('captcha')`
- Session key cleared after every attempt (pass or fail)

Applied to: `POST /api/register`, `POST /api/login`, `POST /api/profile/company`

---

## 1. Register

**`POST /api/register`** — requires `web` middleware (session for CAPTCHA)

**Request body:**
```json
{
    "name": "Jane Smith",
    "email": "jane@example.com",
    "password": "Secret123",
    "password_confirmation": "Secret123",
    "captcha": "ab3x",
    "company_profile": {
        "name": "Acme Co.",
        "role": "CEO",
        "description": "Optional",
        "address": "123 Main St",
        "phone": "+95 9 111 222 333"
    }
}
```

**Flow:**
1. Validate all fields (captcha checked separately after validation)
2. Verify captcha against session
3. DB transaction: create user (role: `customer`) + company profile
4. Send verification email
5. Return `201`

**Success `201`:**
```json
{ "message": "Registration successful. Please check your email to verify your account." }
```

---

## 2. Email Verification

Verification uses custom token fields on the `users` table — not Laravel's built-in `MustVerifyEmail`.

| Column | Description |
|--------|-------------|
| `email_verification_token` | Random 64-char string |
| `email_verification_expires_at` | 24 hours from generation |

**`GET /email/verify/{token}`** (web route)

| Outcome | Redirect |
|---------|----------|
| Valid token | `/login?verified=1` |
| Expired token | `/login?verification=expired&email=...` |
| Invalid token | `/login?verification=invalid` |

**`POST /api/email/resend`** — resends verification email, refreshes token and 24hr expiry.

---

## 3. Login

**`POST /api/login`** — requires `web` middleware (session for CAPTCHA)

**Request body:**
```json
{
    "email": "jane@example.com",
    "password": "Secret123",
    "captcha": "ab3x"
}
```

**Flow:**
1. Validate fields
2. Verify captcha
3. Check account is activated
4. `Auth::attempt()` — verify credentials
5. Check email is verified
6. Create Sanctum token
7. Return token

**Success `200`:**
```json
{ "token": "2|CroLB6izlIzbmeTPsa2ZdwKd..." }
```

Token is stored in `localStorage` and attached to every subsequent request.

---

## 4. Logout

**`POST /api/logout`** — requires Bearer token

Deletes the current token from `personal_access_tokens`. Other active sessions are unaffected.

---

## 5. Change Password

**`PUT /api/password`** — requires Bearer token

```json
{
    "current_password": "OldPass1",
    "password": "NewPass2",
    "password_confirmation": "NewPass2"
}
```

Not available for Google-linked accounts (`password = null`).

---

## 6. Forgot / Reset Password

**`POST /api/forgot-password`** — sends reset link via email.

**`POST /api/reset-password`** — verifies token and updates password.

Reset link is customised in `AppServiceProvider` to point to the Vue SPA:
```
/reset-password?token=...&email=...
```

Tokens expire after **60 minutes** (Laravel default, configured in `config/auth.php`).

---

## 7. Google OAuth

**`GET /auth/google/redirect`** → redirects to Google.
**`GET /auth/google/callback`** → exchanges code, creates/links user, issues token.

After callback, user is redirected to `/login?token={token}`. Vue stores the token and navigates to `/`.

| Scenario | Behaviour |
|----------|-----------|
| New Google user | Create user (role: `customer`), email auto-verified |
| Existing user matched by email | Link google_id, issue token |
| Existing user matched by google_id | Issue token directly |
| No company profile yet | `needs_profile: true` flag → redirect to `/complete-profile` |

---

## 8. Complete Profile

**`POST /api/profile/company`** — requires Bearer token + `web` middleware (CAPTCHA)

For Google users who registered without a company profile (via OAuth). Requires CAPTCHA.

```json
{
    "name": "Acme Co.",
    "role": "CEO",
    "description": "Optional",
    "address": "123 Main St",
    "phone": "+95 9 111 222 333",
    "captcha": "ab3x"
}
```

---

## API Endpoints Summary

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/register` | No (web) | Register + company profile |
| POST | `/api/login` | No (web) | Login, get token |
| POST | `/api/logout` | sanctum | Delete current token |
| GET | `/api/me` | sanctum | Current user with roles/permissions |
| PUT | `/api/password` | sanctum | Change own password |
| POST | `/api/email/resend` | No | Resend verification email |
| GET | `/email/verify/{token}` | No | Verify email from link |
| POST | `/api/forgot-password` | No | Send reset link |
| POST | `/api/reset-password` | No | Reset password |
| POST | `/api/profile/company` | sanctum (web) | Complete company profile |
| GET | `/auth/google/redirect` | No | Redirect to Google |
| GET | `/auth/google/callback` | No | Google OAuth callback |
| GET | `/captcha` | No (web) | CAPTCHA image |

---

## Files Reference

| File | Role |
|------|------|
| `app/Http/Controllers/Auth/AuthController.php` | register, login, logout, me, updatePassword, completeCompanyProfile, resendVerification |
| `app/Http/Controllers/Auth/PasswordResetController.php` | sendResetLink, reset |
| `app/Http/Controllers/Auth/SocialAuthController.php` | Google OAuth |
| `app/Http/Controllers/Auth/CaptchaController.php` | CAPTCHA image generation |
| `app/Services/UserManager.php` | User creation, permission filtering |
| `app/Services/EmailManager.php` | Verification tokens, email dispatch |
| `app/Services/CompanyProfileManager.php` | Create/upsert company profile |
| `routes/api.php` | API route definitions |
| `routes/web.php` | Web routes (verify email, captcha, Google OAuth, SPA catch-all) |
| `resources/js/pages/auth/` | All auth Vue pages |
