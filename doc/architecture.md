# Architecture

## Overview

Light House is a **Laravel 11 + Vue 3 SPA**. Laravel handles the API and server-side logic; Vue Router handles all client-side navigation. Laravel serves a single Blade shell (`app.blade.php`) for every non-API route.

---

## Backend Layer Structure

Every admin resource follows this strict layered pattern:

```
HTTP Request
    └── FormRequest (validation)
    └── Controller (one-liner methods — no logic)
        └── Manager/Service (business logic, orchestration)
            └── Repository (DB queries only)
            └── Filter (fluent query builder)
```

### Controllers (`app/Http/Controllers/Admin/`)

Thin. Every method is one line. Inject the Manager via constructor, delegate everything.

```php
public function store(StoreStaffPositionRequest $request): JsonResponse
{
    return response()->json($this->staffPositionManager->create($request->validated()), 201);
}
```

### Managers / Services (`app/Services/`)

All business logic lives here. Orchestrate repositories and filters. Handle transactions, authorization checks, business rules (e.g. closing active roles, rejecting delete of active record).

| Service | Responsibility |
|---------|----------------|
| `UserManager` | User CRUD, role/permission assignment, permission filtering |
| `EmailManager` | Verification tokens, email dispatch |
| `RoleManager` | Role CRUD, user count attachment |
| `PermissionManager` | Permission CRUD, user count |
| `SiteManager` | Site CRUD with audit |
| `StaffPositionManager` | Position CRUD with audit |
| `StaffRoleManager` | Role assignment history, close-active logic, active-role guard |
| `CompanyProfileManager` | Create/upsert company profile |
| `StaffProfileManager` | Create/upsert staff profile + initial role |

### Repositories (`app/Repositories/`)

Only DB operations. No business logic. Always use `Illuminate\Pagination\LengthAwarePaginator` (concrete class, not the interface — the interface lacks `pluck()` and `each()`).

### Filters (`app/Filters/`)

Fluent query builder wrapper. Pattern: static `for()` factory → chainable methods returning `$this` → terminal `query()`.

```php
StaffPositionFilter::for($this->repo->query())
    ->search($request->input('search'))
    ->createdFrom($request->input('created_from'))
    ->sort($request->input('sort_by', 'name'), $request->input('sort_dir', 'asc'))
    ->query();
```

### Form Requests (`app/Http/Requests/Admin/`)

- Store requests: plain `unique:table,column`
- Update requests: `Rule::unique('table', 'column')->ignore($id)` where `$id` comes from `$this->route('modelName')->id`
- Password: `Password::min(8)->mixedCase()->numbers()` (note: `PasswordResetController` uses full class path to avoid alias clash)
- Shared profile rules extracted to `Concerns/HasUserProfileRules` trait

---

## Frontend Layer Structure

```
resources/js/
├── app.js               # Vue entry, global Axios response interceptor
├── App.vue              # Root component
├── bootstrap.js         # Axios request interceptor (attaches Bearer token)
├── router/
│   └── index.js         # All route definitions
└── pages/
    ├── auth/            # Login, Register, ForgotPassword, ResetPassword, EmailVerification, CompleteProfile
    ├── admin/
    │   ├── users/       # AdminUsersPage + Create/View/Edit + Staff Roles sub-pages
    │   ├── roles/       # CRUD pages
    │   ├── permissions/ # CRUD pages
    │   ├── sites/       # CRUD pages
    │   └── staff-positions/ # CRUD pages
    ├── errors/          # 401, 403, 404, 500 pages
    ├── IndexPage.vue
    ├── DashboardPage.vue
    └── ProfilePage.vue
```

### Axios Interceptors

**Request** (`bootstrap.js`) — attaches Bearer token from localStorage to every request.

**Response** (`app.js`) — global error redirect:

| Status | Action |
|--------|--------|
| 401 | Clear token, redirect `/401` |
| 403 | Redirect `/403` |
| 404 | Redirect `/404` |
| 500+ | Redirect `/500` |

Note: 422 validation errors are **not** handled globally — each form handles them locally.

---

## Database

### Key Indexes

- `staff_roles`: composite index on `(staff_profile_id, end_date)` for active-role lookups
- `staff_roles`: index on `end_date` alone

### Audit Trail

All admin-managed entities have a `created_by` column (nullable FK to `users.id`). Null when created via seeder or CLI (no authenticated user).

### Spatie Pivot Tables

`model_has_roles` and `model_has_permissions` are polymorphic — no FK cascade on `model_id`. Always call `syncRoles([])` and `syncPermissions([])` in the service layer before deleting a user.

---

## CAPTCHA

Uses `gregwar/captcha` (server-rendered image, session-based).

- Image endpoint: `GET /captcha` (web route — starts session)
- Consuming routes must include `->middleware('web')` to share the session
- Server stores phrase in `Session::put('captcha', strtolower($phrase))`
- Validation: compare `strtolower($request->captcha)` against `Session::get('captcha')`, always `Session::forget('captcha')` after
- Applied to: `/api/register`, `/api/login`, `/api/profile/company`

### Frontend Pattern

```js
// On captcha error: reload image + clear input, but keep errors.value.captcha visible
if (errors.value.captcha) {
    form.captcha = ''
    captchaUrl.value = `/captcha?t=${Date.now()}`  // cache-bust
}
// Do NOT call refreshCaptcha() which deletes errors.value.captcha
```

---

## DB Transactions

All multi-table writes use `DB::transaction()`. Emails/notifications are always sent **after** the transaction closes.

```php
$user = DB::transaction(function () use ($request) {
    $user = $this->userManager->create(...);
    $this->staffProfileManager->create($user, ...);
    return $user;
});

$this->emailManager->sendVerificationEmail($user); // outside transaction
```

---

## N+1 Prevention

- Always load `['roles', 'permissions']` before calling `isStaff()` / `isCompany()` — these methods call Spatie's `hasRole()` which lazy-loads roles if not present
- Nested eager loading with constraints — put nested `with()` inside the constraint callback:

```php
$user->load([
    'staffProfile.staffRoles' => fn($q) => $q
        ->orderBy('start_date', 'desc')
        ->with(['position', 'site']),  // inside callback, not as separate key
]);
```
