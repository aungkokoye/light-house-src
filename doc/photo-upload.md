# Staff Photo Upload

Staff profile photos can be uploaded by admins via the user create and edit forms.

---

## Database

Column added to `staff_profiles` by migration `2026_05_11_000001_add_columns_to_staff_profiles_table.php`:

| Column | Type | Nullable | Description |
|--------|------|----------|-------------|
| `photo` | `string` | yes | Relative path inside the `public` disk, e.g. `staff-photos/uuid.jpg` |

The model appends a computed `photo_url` attribute that resolves the full public URL:

```php
// app/Models/StaffProfile.php
protected function photoUrl(): Attribute
{
    return Attribute::make(
        get: fn() => $this->photo ? Storage::disk('public')->url($this->photo) : null,
    );
}
```

`photo_url` is always included in every API response that returns a `StaffProfile`.

---

## Storage

Photos are stored on the `public` disk under `staff-photos/`:

```
storage/app/public/staff-photos/{uuid}.jpg
```

Publicly accessible at:

```
/storage/staff-photos/{uuid}.jpg
```

> **Requirement:** `php artisan storage:link` must be run once on each environment to create the `public/storage` symlink.

---

## API Endpoint

| Method | Endpoint | Middleware | Description |
|--------|----------|------------|-------------|
| POST | `/api/admin/users/{user}/photo` | `auth:sanctum`, `role:admin`, `can:update,user` | Upload or replace a staff photo |

### Request

`multipart/form-data`

| Field | Rules |
|-------|-------|
| `photo` | required, image, mimes: jpg / jpeg / png / webp, max 20 MB |

### Response `200`

```json
{
  "photo_url": "http://example.com/storage/staff-photos/uuid.jpg"
}
```

### Error responses

| Status | Cause |
|--------|-------|
| `404` | User has no staff profile |
| `422` | Validation failure (wrong type, too large) |

---

## Image Processing

Handled by `StaffProfileManager::uploadPhoto()` using PHP's built-in GD extension — no additional packages required.

Processing steps:

1. **Decode** — reads the uploaded file as a GD image resource; supports JPEG, PNG, and WebP input.
2. **Resize** — if the original width exceeds 1920 px, scales down proportionally; smaller images are left untouched.
3. **Encode** — converts to JPEG at **85 % quality**.
4. **Size check** — if the encoded file still exceeds **10 MB**, re-encodes at 60 %, then 40 % as a fallback.
5. **Save** — stores the final JPEG at `staff-photos/{uuid}.jpg` on the `public` disk.
6. **Cleanup** — deletes the previous photo file (if any) before saving the new one.

> **Requirement:** The `gd` PHP extension must be enabled (standard in all official PHP Docker images).

---

## Backend Files

| File | Change |
|------|--------|
| `app/Models/StaffProfile.php` | `photo` in `$fillable`; `photoUrl` accessor; `$appends` |
| `app/Services/StaffProfileManager.php` | `uploadPhoto(StaffProfile, UploadedFile): string` |
| `app/Http/Controllers/StaffPhotoController.php` | `uploadForUser(Request, User)` |
| `routes/api.php` | `POST /admin/users/{user}/photo` route |
| `database/migrations/2026_05_11_000001_...php` | `photo` nullable string column |

---

## Frontend

Photo upload is available in two admin pages:

### Edit User (`/admin/users/{id}/edit`)

- Displays the current photo (or initials placeholder) at the top of the Staff Profile section.
- Clicking the circle opens a file picker.
- On file select the photo is uploaded immediately to `POST /api/admin/users/{id}/photo`.
- The circle updates in place on success; shows an inline error on failure.
- Accepts: `image/jpeg`, `image/jpg`, `image/png`, `image/webp` — max 20 MB.

### Create User (`/admin/users/create`)

- Same 100 px circle in the Staff Profile section.
- File is held in memory until the user form is submitted.
- After the user is created successfully, the photo is uploaded to `POST /api/admin/users/{newId}/photo` as a second request.
- If the photo upload fails after a successful user creation, the user is still redirected to the view page; the photo can be added later via the edit form.

### Display only (no upload)

| Page | Behaviour |
|------|-----------|
| Admin View User | Shows 100 px circle; photo or initials |
| Staff Profile Page | Shows 100 px circle; photo or initials |

---

## Placeholder

When no photo is set, all display locations show the user's initials (up to 3 words) in an indigo circle:

```
bg-indigo-100 · border-indigo-200 · text-indigo-600 · text-2xl font-bold
```
