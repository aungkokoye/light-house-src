# Access Control

This document describes how roles, permissions, and policies work together in Light House.

---

## Overview

Access control is layered across three levels:

```
HTTP Request
    └── auth:sanctum middleware       (is user logged in?)
        └── role:admin middleware     (does user have the admin role?)
            └── Laravel Policy        (does user have the required permission?)
                └── Controller logic
```

The frontend mirrors this by reading `me.roles` and `me.permissions` from `/api/me` and conditionally hiding buttons and redirecting unauthorized page visits.

---

## Roles

| Role    | Description                                         |
|---------|-----------------------------------------------------|
| `admin` | Can access the admin panel. Actions gated by permissions. |
| `user`  | Standard user. No admin panel access.               |

Roles are seeded in `database/seeders/RoleSeeder.php`. Roles themselves carry **no permissions** — permissions are assigned directly to users.

---

## Permissions

| Permission | Description                                                        |
|------------|--------------------------------------------------------------------|
| `view`     | Can view individual user records                                   |
| `create`   | Can create new users                                               |
| `edit`     | Can edit users (name, email, activated, email verified)            |
| `delete`   | Can delete users                                                   |
| `super`    | Elevated admin. Can manage roles, permissions, and assign any permission including `super` itself. Can also change passwords and reassign roles/permissions on user edit. |

Permissions are assigned **per user**, not per role.

---

## The `super` Permission

`super` is a special elevated permission with extra privileges:

- **User management** — only `super` admins can change a user's password, role, or permissions on the edit page
- **Assigning `super`** — only a `super` admin can assign the `super` permission to other users
- **Role & permission CRUD** — create, edit, and delete of roles and permissions is restricted to `super` admins
- **Frontend guards** — edit/delete/create buttons in the admin panel are hidden for non-`super` admins; the edit pages redirect to `/403` if accessed directly

---

## Policies

Policies live in `app/Policies/` and are registered in `AppServiceProvider`.

### UserPolicy (`app/Policies/UserPolicy.php`)

| Method     | Requirement                          |
|------------|--------------------------------------|
| `viewAny`  | `admin` role                         |
| `view`     | `admin` role + (`view` or `super`)   |
| `create`   | `admin` role + (`create` or `super`) |
| `update`   | `admin` role + (`edit` or `super`)   |
| `delete`   | `admin` role + (`delete` or `super`) |

### RolePolicy (`app/Policies/RolePolicy.php`)

| Method     | Requirement                  |
|------------|------------------------------|
| `viewAny`  | `admin` role                 |
| `view`     | `admin` role                 |
| `create`   | `admin` role + `super`       |
| `update`   | `admin` role + `super`       |
| `delete`   | `admin` role + `super`       |

### PermissionPolicy (`app/Policies/PermissionPolicy.php`)

Same structure as `RolePolicy`.

> **Note:** Spatie's `Role` and `Permission` models live outside `App\Models\`, so Laravel's auto-discovery cannot find their policies. They are registered manually via `Gate::policy()` in `AppServiceProvider::boot()`.

---

## Route Authorization

Routes use `->can()` middleware (not `authorizeResource` — removed in Laravel 11):

```php
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    // Roles
    Route::get('/roles',          [...'index'])  ->can('viewAny', Role::class);
    Route::post('/roles',         [...'store'])  ->can('create',  Role::class);
    Route::get('/roles/{role}',   [...'show'])   ->can('view',    'role');
    Route::put('/roles/{role}',   [...'update']) ->can('update',  'role');
    Route::delete('/roles/{role}',[...,'destroy'])->can('delete', 'role');

    // Permissions — same pattern
    // Users — same pattern
});
```

Unauthenticated requests → `401`. Missing role → `403`. Policy denial → `403`.

---

## Frontend Guards

The Vue frontend reads the current user from `/api/me` which returns `roles` and `permissions` arrays.

### Page-level guards (in `onMounted`)

```js
const { data: me } = await axios.get('/api/me')

// Redirect if not admin
if (!me.roles?.some(r => r.name === 'admin')) { router.replace('/403'); return }

// Redirect if not super (edit pages only)
if (!me.permissions?.some(p => p.name === 'super')) { router.replace('/403'); return }
```

Applied in:
- `AdminEditRolePage.vue` — redirects to `/403` if not `super`
- `AdminEditPermissionPage.vue` — redirects to `/403` if not `super`

### UI-level guards (`v-if="hasSuper"`)

Buttons and links are conditionally rendered based on `hasSuper`:

| Page | Guarded elements |
|------|-----------------|
| `AdminRolesPage` | New Role button, Edit icon, Delete icon |
| `AdminPermissionsPage` | New Permission button, Edit icon, Delete icon |
| `AdminViewRolePage` | Edit button |
| `AdminViewPermissionPage` | Edit button |
| `AdminUsersPage` | New User button, View/Edit/Delete icons (per-permission) |
| `AdminEditUserPage` | Password fields, Role select, Permission checkboxes |

---

## CLI & Seeder Context

When running artisan commands or seeders, there is no authenticated user (`Auth::user()` returns `null`). `UserManager` handles this gracefully:

- `filterPermissions()` — bypasses the `super` filter when called without an authenticated user, allowing all permissions to be assigned
- `update()` — uses `Auth::user()?->hasPermissionTo('super') ?? false` (null-safe)

This means `php artisan app:user-create` can assign any permission including `super` regardless of who runs it.

---

## Global Error Redirects

A global Axios interceptor in `resources/js/app.js` catches API error responses and redirects:

| HTTP Status | Redirects to |
|-------------|-------------|
| `401`       | `/401` (clears token) |
| `403`       | `/403` |
| `404`       | `/404` |
| `500+`      | `/500` |

---

## Files Reference

| File | Purpose |
|------|---------|
| `app/Policies/UserPolicy.php` | User CRUD authorization |
| `app/Policies/RolePolicy.php` | Role CRUD authorization |
| `app/Policies/PermissionPolicy.php` | Permission CRUD authorization |
| `app/Providers/AppServiceProvider.php` | Registers Spatie model policies via `Gate::policy()` |
| `app/Services/UserManager.php` | Business logic; enforces `super` filter on permission assignment |
| `database/seeders/RoleSeeder.php` | Seeds default roles and permissions |
| `routes/api.php` | Route-level `->can()` authorization |
| `resources/js/app.js` | Global Axios error interceptor |
| `resources/js/pages/admin/` | Vue pages with `onMounted` guards and `v-if="hasSuper"` |