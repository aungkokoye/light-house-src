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

## Default Roles & Permissions

| Role | Permissions |
|------|-------------|
| `admin` | view users, create users, edit users, delete users |
| `user` | _(none)_ |

Add or change roles/permissions in `database/seeders/RoleSeeder.php`.

---

## Database Tables

| Table | Description |
|-------|-------------|
| `roles` | Defined roles (admin, user, etc.) |
| `permissions` | Defined permissions |
| `model_has_roles` | Which users have which roles |
| `model_has_permissions` | Direct permissions assigned to users |
| `role_has_permissions` | Which permissions belong to which roles |

---

## Assigning Roles & Permissions

```php
// Assign a role
$user->assignRole('admin');

// Assign multiple roles
$user->assignRole(['admin', 'user']);

// Remove a role
$user->removeRole('admin');

// Assign a permission directly to a user
$user->givePermissionTo('edit users');

// Check role
$user->hasRole('admin');

// Check permission (includes permissions via role)
$user->can('edit users');
```

---

## Protecting Routes

```php
// By role
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', ...);
});

// By permission
Route::middleware(['auth:sanctum', 'permission:edit users'])->group(function () {
    Route::put('/users/{user}', ...);
});

// Multiple roles (user must have one of them)
Route::middleware(['auth:sanctum', 'role:admin|editor'])->group(function () {
    ...
});
```

---

## Checking in Controllers

```php
public function destroy(User $user): JsonResponse
{
    if (! auth()->user()->can('delete users')) {
        abort(403);
    }

    $user->delete();

    return response()->json(['message' => 'Deleted.']);
}
```

---

## Files

| File | Description |
|------|-------------|
| `app/Models/User.php` | Has `HasRoles` trait |
| `database/seeders/RoleSeeder.php` | Creates default roles and permissions |
| `database/seeders/DatabaseSeeder.php` | Calls RoleSeeder, assigns admin to test user |
| `routes/api.php` | Example protected route groups |
| `config/permission.php` | Spatie config (cache, table names, etc.) |