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

| Role | Description |
|------|-------------|
| `admin` | Can access the admin panel. Actions gated by permissions. |
| `staff` | Internal staff. Has a staff profile and role history. |
| `customer` | External customer. Has a company profile. |

Roles carry **no permissions** — permissions are assigned directly to users.

---

## Permissions

| Permission | Description |
|------------|-------------|
| `view` | View individual records |
| `create` | Create new records |
| `edit` | Edit records |
| `delete` | Delete records |
| `super` | Elevated admin — manage roles/permissions, change passwords, reassign roles, assign any permission |

---

## The `super` Permission

`super` is a special elevated permission:

- **User management** — only `super` admins can change a user's password, role, or permissions on the edit page
- **Assigning `super`** — only a `super` admin can grant `super` to another user
- **Role & permission CRUD** — create, edit, delete requires `super`
- **Frontend guards** — edit/delete/create buttons hidden for non-`super` admins; protected pages redirect to `/403` if accessed directly

---

## Policy Matrix

### UserPolicy

| Method | Requirement |
|--------|-------------|
| `viewAny` | admin role |
| `view` | admin + (view or super) |
| `create` | admin + (create or super) |
| `update` | admin + (edit or super) |
| `delete` | admin + (delete or super) |

### RolePolicy / PermissionPolicy / SitePolicy / StaffPositionPolicy / StaffRolePolicy

| Method | Requirement |
|--------|-------------|
| `viewAny` | admin role |
| `view` | admin role |
| `create` | admin + super |
| `update` | admin + super |
| `delete` | admin + super |

> **Note:** Spatie's `Role` and `Permission` models live outside `App\Models\`, so Laravel cannot auto-discover their policies. Registered manually in `AppServiceProvider::boot()` via `Gate::policy()`.

---

## Route Authorization

```php
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/roles',           [AdminRoleController::class, 'index'])  ->can('viewAny', Role::class);
    Route::post('/roles',          [AdminRoleController::class, 'store'])  ->can('create',  Role::class);
    Route::get('/roles/{role}',    [AdminRoleController::class, 'show'])   ->can('view',    'role');
    Route::put('/roles/{role}',    [AdminRoleController::class, 'update']) ->can('update',  'role');
    Route::delete('/roles/{role}', [AdminRoleController::class, 'destroy'])->can('delete',  'role');
    // ...same pattern for users, permissions, sites, staff-positions, staff-roles
});
```

Unauthenticated → `401`. Missing role → `403`. Policy denial → `403`.

---

## Frontend Guards

### Page-level (onMounted redirect)

```js
const { data: me } = await axios.get('/api/me')

// Redirect non-admins
if (!me.roles?.some(r => r.name === 'admin')) { router.replace('/403'); return }

// Redirect non-super (for role/permission edit pages)
if (!me.permissions?.some(p => p.name === 'super')) { router.replace('/403'); return }
```

### UI-level (v-if)

| Page | Guarded elements |
|------|-----------------|
| `AdminRolesPage` | New Role button, Edit/Delete icons |
| `AdminPermissionsPage` | New Permission button, Edit/Delete icons |
| `AdminViewRolePage` | Edit button |
| `AdminViewPermissionPage` | Edit button |
| `AdminUsersPage` | New User button, View/Edit/Delete icons |
| `AdminEditUserPage` | Password fields, Role select, Permissions checkboxes |

---

## CLI & Seeder Context

When running Artisan commands or seeders, `Auth::user()` returns `null`. `UserManager::filterPermissions()` bypasses the `super` filter when there is no authenticated caller — allowing all permissions to be assigned via `php artisan app:user-create` or database seeders.

---

## Global Error Redirects

A global Axios response interceptor in `resources/js/app.js`:

| HTTP Status | Action |
|-------------|--------|
| `401` | Clear localStorage token, redirect `/401` |
| `403` | Redirect `/403` |
| `404` | Redirect `/404` |
| `500+` | Redirect `/500` |

Note: `422` is **not** handled globally — each form handles validation errors locally.

---

## Files Reference

| File | Purpose |
|------|---------|
| `app/Policies/UserPolicy.php` | User CRUD authorization |
| `app/Policies/RolePolicy.php` | Role CRUD authorization |
| `app/Policies/PermissionPolicy.php` | Permission CRUD authorization |
| `app/Policies/SitePolicy.php` | Site CRUD authorization |
| `app/Policies/StaffPositionPolicy.php` | Position CRUD authorization |
| `app/Policies/StaffRolePolicy.php` | Staff role authorization |
| `app/Providers/AppServiceProvider.php` | Registers Spatie model policies via `Gate::policy()` |
| `app/Services/UserManager.php` | Enforces `super` filter on permission assignment |
| `database/seeders/UserSeeder.php` | Seeds roles, permissions, and test users |
| `routes/api.php` | Route-level `->can()` authorization |
| `resources/js/app.js` | Global Axios error interceptor |
