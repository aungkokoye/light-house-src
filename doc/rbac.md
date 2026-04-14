# RBAC (Role-Based Access Control)

Roles and permissions are handled using [spatie/laravel-permission](https://spatie.be/docs/laravel-permission).

---

## Installation

Run these once inside the app container:

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
php artisan db:seed --class=RoleSeeder
```

**Why each command:**

- **`composer require spatie/laravel-permission`** — installs the package.
- **`vendor:publish`** — copies the config to `config/permission.php` and migrations to `database/migrations/`.
- **`migrate`** — creates the `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, and `role_has_permissions` tables.
- **`db:seed --class=RoleSeeder`** — seeds the default roles and permissions.

---

## Default Roles

| Role    | Description                                                |
|---------|------------------------------------------------------------|
| `admin` | Can access the admin panel. Actions gated by permissions.  |
| `user`  | Standard user. No admin panel access.                      |

> Roles carry **no permissions**. Permissions are assigned directly to individual users, not to roles.

---

## Default Permissions

| Permission | Description                                                                       |
|------------|-----------------------------------------------------------------------------------|
| `view`     | Can view individual user records                                                   |
| `create`   | Can create new users                                                               |
| `edit`     | Can edit users (name, email, status, email verified)                              |
| `delete`   | Can delete users                                                                   |
| `super`    | Elevated admin. Can manage roles/permissions, change passwords, reassign roles, and assign any permission including `super` itself. |

Defined and seeded in `database/seeders/RoleSeeder.php`.

---

## Database Tables

| Table | Description |
|-------|-------------|
| `roles` | Defined roles (`admin`, `user`). Has a `created_by` (user ID) column. |
| `permissions` | Defined permissions. Has a `created_by` (user ID) column. |
| `model_has_roles` | Which users have which roles |
| `model_has_permissions` | Direct permissions assigned to users |
| `role_has_permissions` | Which permissions belong to which roles (unused — we assign to users directly) |

The `created_by` column is `null` when created via seeders or CLI commands (no authenticated user).

---

## Assigning Roles & Permissions

```php
// Assign a role
$user->assignRole('admin');

// Sync roles (removes all others)
$user->syncRoles(['admin']);

// Sync permissions directly on a user
$user->syncPermissions(['view', 'create', 'edit']);

// Check role
$user->hasRole('admin');

// Check direct permission
$user->hasPermissionTo('super');
```

---

## Protecting Routes

Routes use `->can()` middleware backed by Laravel Policies (not inline checks):

```php
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/roles',          [...'index'])  ->can('viewAny', Role::class);
    Route::post('/roles',         [...'store'])  ->can('create',  Role::class);
    Route::get('/roles/{role}',   [...'show'])   ->can('view',    'role');
    Route::put('/roles/{role}',   [...'update']) ->can('update',  'role');
    Route::delete('/roles/{role}',[...,'destroy'])->can('delete', 'role');
});
```

See `doc/access-control.md` for the full policy matrix.

---

## Policies

Authorization logic lives in policies, not controllers. Spatie models are not in `App\Models\`, so their policies are registered manually:

```php
// app/Providers/AppServiceProvider.php
Gate::policy(Role::class, RolePolicy::class);
Gate::policy(Permission::class, PermissionPolicy::class);
```

| Policy | File |
|--------|------|
| `UserPolicy` | `app/Policies/UserPolicy.php` |
| `RolePolicy` | `app/Policies/RolePolicy.php` |
| `PermissionPolicy` | `app/Policies/PermissionPolicy.php` |

---

## Files

| File | Description |
|------|-------------|
| `app/Models/User.php` | Has `HasRoles` trait from Spatie |
| `app/Policies/UserPolicy.php` | User CRUD authorization rules |
| `app/Policies/RolePolicy.php` | Role CRUD authorization rules |
| `app/Policies/PermissionPolicy.php` | Permission CRUD authorization rules |
| `app/Providers/AppServiceProvider.php` | Registers Spatie model policies via `Gate::policy()` |
| `database/seeders/RoleSeeder.php` | Creates default roles and permissions |
| `database/seeders/UserSeeder.php` | Creates test users with roles and permissions assigned |
| `database/migrations/2026_03_23_115902_create_permission_tables.php` | Creates all Spatie tables (with `created_by` columns) |
| `routes/api.php` | Route-level `->can()` authorization |
| `config/permission.php` | Spatie config (cache, table names, etc.) |