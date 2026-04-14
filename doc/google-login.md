# Google Login (OAuth 2.0)

Google login is implemented using [Laravel Socialite](https://laravel.com/docs/socialite) with a stateless OAuth 2.0 flow. Users are redirected to Google to authenticate and returned with a Sanctum token — no password required.

---

## How It Works

```
User clicks "Continue with Google"
        │
        ▼
GET /auth/google/redirect
        │
        ▼
Laravel → redirects to Google consent screen
        │
        ▼
User signs in and approves access
        │
        ▼
Google → redirects to GET /auth/google/callback?code=...
        │
        ▼
Laravel exchanges code for Google user profile
        │
     ┌──┴──────────────────────────────┐
  OAuth error                    Got Google profile
     │                                  │
     ▼                     Search by google_id OR email
redirect /login                    ┌───┴────────────┐
?error=google_failed           found             not found
                                 │                   │
                                 ▼                   ▼
                          Link google_id        Create new user
                          if not set             (see below)
                                 │                   │
                                 └────────┬──────────┘
                                          ▼
                                   createToken()
                                          │
                                          ▼
                            redirect /login?token={token}
                                          │
                                          ▼
                       Vue stores token in localStorage
                       Redirects to /
```

---

## Installation

Run inside the app container:

```bash
composer require laravel/socialite
php artisan migrate
```

---

## Google Cloud Console Setup

### 1. Create a Project

1. Go to [console.cloud.google.com](https://console.cloud.google.com)
2. Click the project dropdown → **New Project**
3. Give it a name (e.g. `Light House`) and click **Create**

### 2. Enable Google+ API

1. Go to **APIs & Services → Library**
2. Search for **Google+ API** or **Google People API**
3. Click **Enable**

### 3. Create OAuth Credentials

1. Go to **APIs & Services → Credentials**
2. Click **Create Credentials → OAuth 2.0 Client ID**
3. If prompted, configure the **OAuth consent screen** first:
   - User type: **External**
   - Fill in app name, support email, developer contact
   - Add scope: `email`, `profile`, `openid`
   - Add test users if in development
4. Application type: **Web application**
5. Under **Authorized redirect URIs**, add:

| Environment | URI |
|-------------|-----|
| Local | `http://localhost:8375/auth/google/callback` |
| Production | `https://yourdomain.com/auth/google/callback` |

6. Click **Create**
7. Copy the **Client ID** and **Client Secret**

---

## Environment Variables

Add to `.env`:

```env
GOOGLE_CLIENT_ID=your-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI=http://localhost:8375/auth/google/callback
```

> For production, change `GOOGLE_REDIRECT_URI` to your live domain.

---

## Configuration

### config/services.php

```php
'google' => [
    'client_id'     => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect'      => env('GOOGLE_REDIRECT_URI'),
],
```

---

## Routes

Defined in `routes/web.php` (not `api.php`) because these are browser redirects, not JSON API calls:

```php
Route::get('/auth/google/redirect', [SocialAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
```

---

## Controller

**`app/Http/Controllers/Auth/SocialAuthController.php`**

### redirectToGoogle()

Redirects the browser to Google's OAuth consent screen using the stateless driver (no session required):

```php
public function redirectToGoogle()
{
    return Socialite::driver('google')->stateless()->redirect();
}
```

### handleGoogleCallback()

Handles the return from Google:

```php
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
        // Link google_id to existing account if not already set
        if (! $user->google_id) {
            $user->forceFill(['google_id' => $googleUser->getId()])->save();
        }
    } else {
        // Create new user
        $name = $googleUser->getName();
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
            $user->assignRole('user');
        } catch (\Throwable $e) {
            return redirect('/login?error=google_failed');
        }
    }

    $token = $user->createToken('api-token')->plainTextToken;

    return redirect('/login?token=' . urlencode($token));
}
```

---

## User Account Scenarios

| Scenario | Behaviour |
|----------|-----------|
| New Google user, email not in DB | Create user, assign `user` role, email pre-verified |
| Existing user matched by email | Link `google_id` to account, issue token |
| Existing user matched by `google_id` | Issue token directly |
| Name already taken | Append last 4 digits of Google ID to name |
| Google OAuth error | Redirect to `/login?error=google_failed` |

> Google-authenticated users have `password = null` and skip email verification — Google guarantees the email belongs to the user.

---

## Database Migration

File: `database/migrations/2026_03_24_000001_add_google_id_to_users_table.php`

```php
Schema::table('users', function (Blueprint $table) {
    $table->string('google_id')->nullable()->unique()->after('email');
    $table->string('password')->nullable()->change();
});
```

**Changes:**

| Column | Change |
|--------|--------|
| `google_id` | New column — stores Google account ID, nullable, unique |
| `password` | Made nullable — Google users have no password |

Run the migration:

```bash
php artisan migrate
```

---

## Frontend

### Login Button

`resources/js/pages/LoginPage.vue` — a plain anchor tag that triggers a full browser redirect:

```html
<a href="/auth/google/redirect" class="...">
    Continue with Google
</a>
```

> This is NOT an Axios call. It must be a full page navigation so the browser can follow the redirect chain to Google and back.

### Token Handling on Callback

After a successful Google login, Laravel redirects to `/login?token={token}`. The login page detects this on load and stores the token:

```js
if (route.query.token) {
    localStorage.setItem('token', route.query.token)
    window.location.href = '/'
}
```

### Error Banner

If Google auth fails, the user is redirected to `/login?error=google_failed`:

```html
<div v-if="googleFailed">
    Google sign-in failed. Please try again or use email and password.
</div>
```

---

## Files Reference

| File | Description |
|------|-------------|
| `app/Http/Controllers/Auth/SocialAuthController.php` | Handles Google redirect and callback |
| `routes/web.php` | Registers `/auth/google/redirect` and `/auth/google/callback` |
| `config/services.php` | Google OAuth credentials config |
| `database/migrations/2026_03_24_000001_add_google_id_to_users_table.php` | Adds `google_id`, makes `password` nullable |
| `resources/js/pages/auth/LoginPage.vue` | Google button and callback token handling |
| `.env` | `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI` |

---

## Troubleshooting

| Problem | Cause | Fix |
|---------|-------|-----|
| `Class "Laravel\Socialite\Facades\Socialite" not found` | Package not installed | Run `composer require laravel/socialite` |
| `redirect_uri_mismatch` | Callback URI in Google Console doesn't match `.env` | Add exact URI to Google Console authorized redirect URIs |
| `invalid_client` | Wrong client ID or secret | Double-check `.env` values against Google Console |
| `403 Access denied` | OAuth consent screen not configured or user not in test list | Configure consent screen and add test users |
| Token not stored after callback | Vue not detecting `?token=` query param | Ensure `route.query.token` check runs before `reactive()` |