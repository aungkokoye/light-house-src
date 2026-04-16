# Admin API

All admin endpoints are protected by `auth:sanctum` + `role:admin` middleware and further gated by Laravel Policies.

Base prefix: `/api/admin`

---

## Users

| Method | Endpoint | Permission | Description |
|--------|----------|------------|-------------|
| GET | `/users` | viewAny | Paginated list with filters |
| POST | `/users` | create | Create user + profile |
| GET | `/users/{user}` | view | User detail with profile |
| PUT | `/users/{user}` | edit | Update user + profile |
| DELETE | `/users/{user}` | delete | Delete user |
| POST | `/users/{user}/resend-verification` | view | Resend verification email |

### Query params for GET /users

| Param | Description |
|-------|-------------|
| `search` | Name, email, or company name |
| `role` | Filter by role name |
| `position` | Filter by staff position name |
| `activated` | `1` or `0` |
| `email_verified` | `1` or `0` |
| `updated_from` | Date (YYYY-MM-DD) |
| `updated_to` | Date (YYYY-MM-DD) |
| `sort_by` | `id`, `name`, `email`, `activated`, `email_verified_at`, `updated_at` |
| `sort_dir` | `asc` or `desc` |
| `per_page` | `10`, `20`, `30`, `40`, or `50` |

### POST /users — Request body

```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "Secret1234",
    "password_confirmation": "Secret1234",
    "role": "customer",
    "activated": true,
    "email_verified": false,
    "permissions": ["view", "create"],

    "company_profile": {
        "name": "Acme Co.",
        "role": "CEO",
        "description": "...",
        "address": "123 Main St",
        "phone": "+95 9 111 222 333"
    }
}
```

For staff/admin role, use `staff_profile` and `staff_role` instead of `company_profile`:

```json
{
    "role": "staff",
    "staff_profile": {
        "full_name": "Jane Smith",
        "nrc_no": "12/OKN(N)123456",
        "dob": "1990-01-15",
        "address": "456 Side St",
        "phone": "+95 9 444 555 666",
        "start_date": "2024-01-01"
    },
    "staff_role": {
        "staff_position_id": 1,
        "site_id": 2,
        "salary": 500000,
        "start_date": "2024-01-01"
    }
}
```

---

## Roles

| Method | Endpoint | Permission | Description |
|--------|----------|------------|-------------|
| GET | `/roles/all` | admin | All roles (for dropdowns) |
| GET | `/roles` | viewAny | Paginated list |
| POST | `/roles` | create (super) | Create role |
| GET | `/roles/{role}` | view | Role detail + user count |
| PUT | `/roles/{role}` | edit (super) | Update role |
| DELETE | `/roles/{role}` | delete (super) | Delete role + clean pivots |

### Query params for GET /roles

| Param | Description |
|-------|-------------|
| `search` | Name search |
| `sort_by` | `id`, `name`, `created_at`, `updated_at` |
| `sort_dir` | `asc` or `desc` |
| `per_page` | `10`–`50` |

---

## Permissions

| Method | Endpoint | Permission | Description |
|--------|----------|------------|-------------|
| GET | `/permissions` | viewAny | Full list (no pagination) |
| POST | `/permissions` | create (super) | Create permission |
| GET | `/permissions/{permission}` | view | Permission detail + user count |
| PUT | `/permissions/{permission}` | edit (super) | Update permission |
| DELETE | `/permissions/{permission}` | delete (super) | Delete permission + clean pivots |

---

## Sites

| Method | Endpoint | Permission | Description |
|--------|----------|------------|-------------|
| GET | `/sites/all` | admin | All sites (for dropdowns) |
| GET | `/sites` | viewAny | Paginated list |
| POST | `/sites` | create | Create site |
| GET | `/sites/{site}` | view | Site detail |
| PUT | `/sites/{site}` | edit | Update site |
| DELETE | `/sites/{site}` | delete | Delete site |

### POST/PUT /sites — Request body

```json
{
    "name": "Main Branch",
    "description": "Head office location",
    "address": "123 Main St, Yangon",
    "phone": "+95 9 xxx xxx xxx"
}
```

---

## Staff Positions

| Method | Endpoint | Permission | Description |
|--------|----------|------------|-------------|
| GET | `/staff-positions/all` | admin | All positions (for dropdowns) |
| GET | `/staff-positions` | viewAny | Paginated list |
| POST | `/staff-positions` | create | Create position |
| GET | `/staff-positions/{staffPosition}` | view | Position detail |
| PUT | `/staff-positions/{staffPosition}` | edit | Update position |
| DELETE | `/staff-positions/{staffPosition}` | delete | Delete position |

---

## Staff Roles (nested under user)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/users/{user}/staff-roles` | Paginated role history for user |
| POST | `/users/{user}/staff-roles` | Assign new role (auto-closes active) |
| GET | `/users/{user}/staff-roles/{staffRole}` | Role detail |
| PUT | `/users/{user}/staff-roles/{staffRole}` | Update role |
| DELETE | `/users/{user}/staff-roles/{staffRole}` | Delete role (not if active) |

### POST /users/{user}/staff-roles — Request body

```json
{
    "staff_position_id": 1,
    "site_id": 2,
    "salary": 500000,
    "start_date": "2025-01-01"
}
```

Creating a new role automatically sets `end_date` on the current active role (no end_date) to `start_date - 1 day`.

### PUT /users/{user}/staff-roles/{staffRole} — Request body

```json
{
    "staff_position_id": 1,
    "site_id": 2,
    "salary": 600000,
    "start_date": "2025-01-01",
    "end_date": "2025-12-31"
}
```

`end_date` is nullable (null = currently active role). Validated as `after_or_equal:start_date`.

### GET /users/{user}/staff-roles — Query params

| Param | Description |
|-------|-------------|
| `sort_by` | `id`, `position_name`, `start_date`, `end_date` |
| `sort_dir` | `asc` or `desc` |
| `per_page` | `10`–`50` |

Default sort: active role first (null end_date), then by start_date descending.

---

## Common Response Patterns

### Paginated list (200)
```json
{
    "data": [...],
    "current_page": 1,
    "last_page": 5,
    "per_page": 20,
    "total": 98
}
```

### Validation error (422)
```json
{
    "message": "The name has already been taken.",
    "errors": {
        "name": ["The name has already been taken."]
    }
}
```

### Delete success (200)
```json
{ "message": "Resource deleted successfully." }
```

### Forbidden (403)
```json
{ "message": "This action is unauthorized." }
```
