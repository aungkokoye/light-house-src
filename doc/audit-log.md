# Audit Log

Tracks all significant system activity — CRUD operations, auth events, and password changes — asynchronously via Laravel's queue. Every record is immutable once written.

---

## Architecture

```
HTTP Request
    │
    ▼
Manager / Controller
    │  calls DispatchesAuditEvents trait
    ▼
Event dispatched
(AuditableActionPerformed | UserLoggedIn | UserLoggedOut)
    │
    ▼
DispatchAuditLog Listener
    │  implements AuditableEvent interface
    ▼
ProcessAuditLog Job  ──► queued (database driver)
    │
    ▼
AuditLog::create()   ──► audit_logs table
```

---

## Key Files

| File | Purpose |
|------|---------|
| `app/Models/AuditLog.php` | Model — event constants, `events()` helper |
| `app/Concerns/DispatchesAuditEvents.php` | Shared trait — `auditCreated`, `auditUpdated`, `auditDeleted` |
| `app/Concerns/AuditableCrud.php` | Re-exports `DispatchesAuditEvents` for controllers |
| `app/Events/Contracts/AuditableEvent.php` | Interface all audit events implement |
| `app/Events/AuditableActionPerformed.php` | Generic CRUD event |
| `app/Events/UserLoggedIn.php` | Login event |
| `app/Events/UserLoggedOut.php` | Logout event |
| `app/Listeners/DispatchAuditLog.php` | Pushes `ProcessAuditLog` job onto queue |
| `app/Jobs/ProcessAuditLog.php` | Queue job — writes the `AuditLog` record |
| `app/Services/AuditLogManager.php` | List + show logic for the admin UI |
| `app/Http/Controllers/Admin/AdminAuditLogController.php` | Admin API endpoints |
| `database/migrations/2026_04_21_000001_create_audit_logs_table.php` | Table + indexes |

---

## Event Constants

All event strings are defined as constants on `AuditLog` to avoid hardcoding:

```php
AuditLog::EVENT_CREATED          // 'created'
AuditLog::EVENT_UPDATED          // 'updated'
AuditLog::EVENT_DELETED          // 'deleted'
AuditLog::EVENT_LOGIN            // 'login'
AuditLog::EVENT_LOGOUT           // 'logout'
AuditLog::EVENT_PASSWORD_CHANGED // 'password_changed'
```

Get all events as an array (used by the API events endpoint):

```php
AuditLog::events(); // ['created', 'updated', 'deleted', 'login', 'logout', 'password_changed']
```

---

## How Auditing Works

### CRUD — via `DispatchesAuditEvents` trait

Managers use the `DispatchesAuditEvents` trait directly. Controllers use `AuditableCrud` which re-exports it.

```php
// In a Manager
use App\Concerns\DispatchesAuditEvents;

class SomeManager
{
    use DispatchesAuditEvents;

    public function create(...): Model
    {
        $model = ...; // create the record
        $this->auditCreated($model);
        return $model;
    }

    public function update(Model $model, ...): Model
    {
        $oldValues = $this->filterAuditValues($model->getAttributes());
        $model = ...; // update the record
        $this->auditUpdated($model, $oldValues);
        return $model;
    }

    public function delete(Model $model): void
    {
        $this->auditDeleted($model);
        $model->delete();
    }
}
```

Pass custom `$newValues` (e.g. to include relations in the snapshot):

```php
$this->auditUpdated($model, $oldValues, $newValues);
$this->auditCreated($model, $newValues);
$this->auditDeleted($model, $oldValues);
```

`filterAuditValues()` strips sensitive fields (`password`, `remember_token`, `email_verification_token`) before recording.

### Auth Events — dispatched directly in `AuthController`

```php
UserLoggedIn::dispatch($user, $request->ip(), $request->userAgent());
UserLoggedOut::dispatch($user, $request->ip(), $request->userAgent());

AuditableActionPerformed::dispatch(
    $user,
    AuditLog::EVENT_PASSWORD_CHANGED,
    $user,        // auditable model
    null,         // old values
    null,         // new values
    $request->ip(),
    $request->userAgent() ?? '',
);
```

### Adding Audit to a New Controller

```php
use App\Concerns\AuditableCrud;

class AdminFooController extends Controller
{
    use AuditableCrud;

    public function store(StoreFooRequest $request): JsonResponse
    {
        $foo = Foo::create($request->validated());
        $this->auditCreated($foo);
        return response()->json($foo, 201);
    }

    public function update(UpdateFooRequest $request, Foo $foo): JsonResponse
    {
        $oldValues = $this->filterAuditValues($foo->getAttributes());
        $foo->update($request->validated());
        $this->auditUpdated($foo, $oldValues);
        return response()->json($foo);
    }

    public function destroy(Foo $foo): JsonResponse
    {
        $this->auditDeleted($foo);
        $foo->delete();
        return response()->json(['message' => 'Deleted.']);
    }
}
```

---

## `audit_logs` Table

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | Primary key |
| `user_id` | bigint nullable | FK → `users`, nullOnDelete |
| `user_email` | string nullable | Snapshot of email at time of event |
| `event` | string | One of the `EVENT_*` constants |
| `auditable_type` | string nullable | Fully-qualified model class |
| `auditable_id` | bigint nullable | Model primary key |
| `old_values` | json nullable | State before change |
| `new_values` | json nullable | State after change |
| `ip_address` | string(45) nullable | IPv4 or IPv6 |
| `user_agent` | string nullable | Browser / client |
| `created_at` | timestamp | Immutable — no `updated_at` |

**Indexes:** `created_at`, `user_email`, `ip_address`, `event`, `[user_id, created_at]`, `[auditable_type, auditable_id]`

---

## `ProcessAuditLog` Job

```php
class ProcessAuditLog implements ShouldQueue, ShouldBeUnique
{
    public int $tries     = 3;
    public int $backoff   = 5;   // seconds between retries
    public int $uniqueFor = 10;  // dedup window in seconds

    public function uniqueId(): string
    {
        // Prevents duplicate records for the same user+event+IP within 10s
        return md5($this->userId . $this->event . $this->ipAddress . floor(time() / 10));
    }
}
```

- **`ShouldBeUnique`** — requires the `cache` driver to be set (default `file` is fine).
- **`uniqueFor = 10`** — if the same user triggers the same event twice within 10 seconds (e.g. double-click), only one record is written.
- **3 retries with 5s backoff** — handles transient DB failures gracefully.

---

## Laravel Queue — Database Driver

The project uses the **database** queue driver (configured in `.env`):

```env
QUEUE_CONNECTION=database
```

The `jobs` table must exist before running the audit log migration:

```bash
php artisan queue:table
php artisan migrate
```

### Running the Worker (Development)

```bash
php artisan queue:work
```

Or via `composer dev` which runs all services concurrently including the queue worker.

### Useful Queue Commands

```bash
# Process jobs once and exit
php artisan queue:work --once

# Restart worker after code changes (graceful — finishes current job first)
php artisan queue:restart

# View failed jobs
php artisan queue:failed

# Retry all failed jobs
php artisan queue:retry all

# Retry a specific failed job by ID
php artisan queue:retry 5

# Flush all failed jobs
php artisan queue:flush

# Monitor queue in real time
php artisan queue:monitor database:50
```

> **Important:** After deploying new code, always run `php artisan queue:restart` so the worker picks up changes. Workers cache code in memory and will use stale code otherwise.

---

## Supervisor — Production Server (Step-by-Step)

Supervisor keeps the queue worker running continuously, restarting it automatically if it crashes or exceeds memory.

### Step 1 — Install Supervisor

```bash
sudo apt install -y supervisor
sudo systemctl enable supervisor
sudo systemctl start supervisor
```

Verify it is running:

```bash
sudo systemctl status supervisor
```

---

### Step 2 — Deploy the Worker Config

Copy the config file from the repo to the Supervisor config directory:

```bash
sudo cp /var/www/light-house-src/light-house-docker/supervisor/lighthouse-worker.conf \
    /etc/supervisor/conf.d/lighthouse-worker.conf
```

The file contents:

```ini
[program:lighthouse-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/light-house-src/artisan queue:work --sleep=3 --tries=3 --timeout=60 --memory=256
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/light-house-src/storage/logs/worker.log
stopwaitsecs=60
```

| Option | Value | Meaning |
|--------|-------|---------|
| `--sleep=3` | 3s | Poll interval when queue is empty |
| `--tries=3` | 3 | Max attempts before marking a job as failed |
| `--timeout=60` | 60s | Kill job if it runs longer than this |
| `--memory=256` | 256MB | Restart worker if memory exceeds this |
| `numprocs=1` | 1 | Number of parallel worker processes |
| `stopwaitsecs=60` | 60s | Wait for current job to finish before force-killing |

---

### Step 3 — Make Sure the Log File is Writable

```bash
sudo touch /var/www/light-house-src/storage/logs/worker.log
sudo chown www-data:www-data /var/www/light-house-src/storage/logs/worker.log
```

---

### Step 4 — Load and Start the Worker

Tell Supervisor to read the new config and start the program:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start lighthouse-worker:*
```

---

### Step 5 — Verify It Is Running

```bash
sudo supervisorctl status
```

Expected output:
```
lighthouse-worker:lighthouse-worker_00   RUNNING   pid 12345, uptime 0:00:10
```

Check the worker log:

```bash
tail -f /var/www/light-house-src/storage/logs/worker.log
```

---

### Day-to-Day Commands

```bash
# Start / stop / restart
sudo supervisorctl start   lighthouse-worker:*
sudo supervisorctl stop    lighthouse-worker:*
sudo supervisorctl restart lighthouse-worker:*

# Check status
sudo supervisorctl status

# Tail live logs
sudo supervisorctl tail -f lighthouse-worker:lighthouse-worker_00
tail -f /var/www/light-house-src/storage/logs/worker.log
```

---

### After Deploying New Code

Workers cache application code in memory. After every deploy you must restart the worker so it picks up changes:

```bash
sudo -u www-data php /var/www/light-house-src/artisan queue:restart
sudo supervisorctl restart lighthouse-worker:*
```

`queue:restart` signals the worker to finish its current job then exit cleanly. Supervisor then starts a fresh process automatically. The deploy script (`deploy.sh`) already runs both commands.

---

### After Editing the Config File

If you change `lighthouse-worker.conf`:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl restart lighthouse-worker:*
```

---

## Supervisor — Docker

Inside Docker, Supervisor manages both Apache and the queue worker in a single container.

**Config:** `light-house-docker/supervisor/supervisord.conf`

```ini
[program:apache2]
command=/usr/sbin/apache2ctl -D FOREGROUND
autostart=true
autorestart=true

[program:queue-worker]
command=php /var/www/html/artisan queue:work --sleep=3 --tries=3 --timeout=60 --memory=256
autostart=true
autorestart=true
user=www-data
stdout_logfile=/var/www/html/storage/logs/worker.log
stdout_logfile_maxbytes=10MB
stdout_logfile_backups=3
```

The Supervisor web UI is exposed on port `9001` (credentials: `admin` / `secret`).  
Access at: `http://localhost:9001`

To exec into the container and manage the worker:

```bash
docker exec -it <container> supervisorctl status
docker exec -it <container> supervisorctl restart queue-worker
docker exec -it <container> tail -f /var/www/html/storage/logs/worker.log
```

---

## Admin API

All endpoints require `auth:sanctum` + `role:admin`.

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/admin/audit-logs/events` | List all valid event types |
| `GET` | `/api/admin/audit-logs` | Paginated list with filters |
| `GET` | `/api/admin/audit-logs/{id}` | Single audit log detail |

### Query Parameters — List Endpoint

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `search` | string | — | Filter by `user_email` (partial match) |
| `event` | string | — | Filter by event type |
| `sort_by` | string | `created_at` | One of: `id`, `user_email`, `ip_address`, `created_at` |
| `sort_dir` | string | `desc` | `asc` or `desc` |
| `per_page` | int | `20` | One of: 10, 20, 30, 40, 50 |
| `page` | int | `1` | Page number |

---

## Frontend Pages

| Route | Component | Description |
|-------|-----------|-------------|
| `/admin/audit-logs` | `AdminAuditLogsPage.vue` | List with search, event filter, sortable columns, pagination |
| `/admin/audit-logs/:id` | `AdminViewAuditLogPage.vue` | Detail view with field-level diff |

The detail view shows old and new values side by side in a recursive diff table:
- Nested objects (e.g. `staffProfile`) are expanded field by field
- Arrays of objects (e.g. `staff_roles`) are expanded item by item (`#1`, `#2`, ...)
- Changed fields are highlighted — red (before) / green (after)
- Timestamp fields (`created_at`, `updated_at`) inside nested objects are excluded from comparison
