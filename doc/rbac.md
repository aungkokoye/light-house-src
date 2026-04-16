# RBAC (Role-Based Access Control)

Roles and permissions are handled using [spatie/laravel-permission](https://spatie.be/docs/laravel-permission).

---

## Roles

| Role | Description |
|------|-------------|
| `admin` | Access to admin panel. Actions gated by permissions. |
| `staff` | Internal staff. Has a staff profile and role history. |
| `customer` | External customer. Has a company profile. |

Roles carry **no permissions**. Permissions are assigned directly to individual users.

---

## Permissions (assigned per user)

| Permission | Description |
|------------|-------------|
| `view` | View individual records |
| `create` | Create new records |
| `edit` | Edit records |
| `delete` | Delete records |
| `super` | Elevated admin — manage roles/permissions, change passwords, reassign roles, assign any permission including `super` |

---

## The `super` Permission

`super` unlocks elevated capabilities beyond the standard admin:

- **User edit page** — only `super` can change a user's password, role, or permissions
- **Assigning `super`** — only a `super` admin can grant the `super` permission to another user
- **Role & permission CRUD** — create, edit, delete of roles and permissions requires `super`
- **Frontend guards** — buttons hidden for non-`super` admins; edit pages redirect to `/403` if accessed directly

---

## Database Tables

| Table | Description |
|-------|-------------|
| `roles` | Roles with `created_by` audit column |
| `permissions` | Permissions with `created_by` audit column |
| `model_has_roles` | User → role assignments (polymorphic) |
| `model_has_permissions` | User → direct permission assignments (polymorphic) |
| `role_has_permissions` | Role → permission (unused — we assign permissions directly to users) |

**Important:** `model_has_roles` and `model_has_permissions` are polymorphic (`model_id`, `model_type`) — no FK cascade. Always call `syncRoles([])` and `syncPermissions([])` in the service layer before deleting a user.

The `created_by` column is `null` when records are created via seeders or the CLI (no authenticated user).

---

## Assigning Roles & Permissions

```php
$user->assignRole('admin');
$user->syncRoles(['staff']);
$user->syncPermissions(['view', 'create', 'edit']);
$user->hasRole('admin');
$user->hasPermissionTo('super');
```

---

## Policies

Authorization logic lives in `app/Policies/`, not controllers. Spatie models (`Role`, `Permission`) are outside `App\Models\` so their policies are registered manually in `AppServiceProvider`:

```php
Gate::policy(Role::class, RolePolicy::class);
Gate::policy(Permission::class, PermissionPolicy::class);
```

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

---

## Route Protection

```
HTTP Request
    └── auth:sanctum          (logged in?)
        └── role:admin        (has admin role?)
            └── ->can()       (passes policy check?)
                └── Controller
```

```php
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/roles',          [AdminRoleController::class, 'index'])  ->can('viewAny', Role::class);
    Route::post('/roles',         [AdminRoleController::class, 'store'])  ->can('create',  Role::class);
    Route::get('/roles/{role}',   [AdminRoleController::class, 'show'])   ->can('view',    'role');
    Route::put('/roles/{role}',   [AdminRoleController::class, 'update']) ->can('update',  'role');
    Route::delete('/roles/{role}',[AdminRoleController::class, 'destroy'])->can('delete',  'role');
});
```

---

## Frontend Guards

Vue reads the authenticated user from `GET /api/me` which returns `roles` and `permissions` arrays.

### Page-level (onMounted redirect)

```js
if (!me.roles?.some(r => r.name === 'admin')) { router.replace('/403'); return }
if (!me.permissions?.some(p => p.name === 'super')) { router.replace('/403'); return }
```

### UI-level (v-if)

Buttons conditionally rendered based on `hasSuper` or specific permission checks.

---

## CLI Context

When running Artisan commands or seeders, `Auth::user()` is `null`. `UserManager::filterPermissions()` allows all permissions through when there is no authenticated caller — so `app:user-create` can assign any permission including `super`.

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
| `app/Providers/AppServiceProvider.php` | Registers Spatie policies via `Gate::policy()` |
| `app/Services/UserManager.php` | Permission filtering (blocks non-super from assigning `super`) |
| `database/seeders/UserSeeder.php` | Seeds roles, permissions, and test users |
| `routes/api.php` | Route-level `->can()` authorization |
