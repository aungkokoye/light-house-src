# Initial Setup

## Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 11 / PHP 8.4 |
| Frontend | Vue 3 SPA + Tailwind CSS 4 |
| Bundler | Vite 8 |
| Database | MySQL 8.0 |
| Runtime | Docker (PHP-Apache + Node + MySQL + Mailcatcher) |

---

## Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed and running

```bash
mkdir LightHouse
cd LightHouse
git clone https://github.com/aungkokoye/light-house-docker.git
git clone https://github.com/aungkokoye/light-house-src.git
```

---

## 1. Start Docker Containers

```bash
cd light-house-docker
docker compose up --build -d
```

| Service | Container | Port |
|---------|-----------|------|
| PHP 8.4 + Apache | `light_house_app` | http://localhost:8375 |
| Node 22 + Vite | `light_house_node` | http://localhost:5173 |
| MySQL 8.0 | `light_house_db` | `localhost:3550` |
| Mailcatcher | `light_house_mailer` | SMTP: `localhost:2025` · UI: http://localhost:2080 |

---

## 2. Configure Environment

Inside the app container:

```bash
cp .env.example .env
```

Update `.env`:

```env
APP_NAME=LightHouse
APP_URL=http://localhost:8375

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=light_house
DB_USERNAME=light_house
DB_PASSWORD=password
```

For Google OAuth, add:

```env
GOOGLE_CLIENT_ID=your-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI=http://localhost:8375/auth/google/callback
```

---

## 3. Install Dependencies & Migrate

Enter the app container:

```bash
docker exec -it light_house_app bash
cd /var/www/html
mkdir -p storage/framework/{cache,sessions,views} storage/logs
chmod -R 775 storage bootstrap/cache
composer install
php artisan key:generate
php artisan migrate --seed
```

---

## 4. Seeded Users

| Name | Email | Password | Role | Permissions |
|------|-------|----------|------|-------------|
| Admin User | admin@lighthouse.com | Password1 | admin | all (including `super`) |
| Second Admin | admin2@lighthouse.com | Password1 | admin | view, create, edit |

> Password must be `Password1` (capital P + number 1) to pass the new password rules (min 8, uppercase, lowercase, number).

---

## 5. Database Connection (External Editor)

| Field | Value |
|-------|-------|
| Host | `127.0.0.1` |
| Port | `3550` |
| Database | `light_house` |
| Username | `light_house` |
| Password | `password` |

Root: username `root`, password `root`.

---

## 6. Local Email (Mailcatcher)

All outgoing emails are caught by Mailcatcher — nothing is delivered to real inboxes.

- Web inbox: http://localhost:2080
- SMTP (Docker internal): `mailer:1025`

`.env` settings (already in `.env.example`):

```env
MAIL_MAILER=smtp
MAIL_HOST=mailer
MAIL_PORT=1025
MAIL_FROM_ADDRESS="noreply@lighthouse.com"
```

---

## 7. Creating Users via CLI

The interactive command supports all user types:

```bash
php artisan app:user-create
```

- Prompts: name, email, password, role, optional permissions
- For `customer` role: collects company profile (name, role/title, address, phone, description)
- For `staff`/`admin` role: collects staff profile + initial role assignment (position, site, salary, dates)
- Wrapped in DB transaction; verification email sent after commit

---

## 8. Fresh Database

```bash
php artisan migrate:fresh --seed
```

---

## Frontend Structure

```
resources/js/
├── app.js               # Vue entry + global Axios response interceptor
├── App.vue              # Root component
├── bootstrap.js         # Axios request interceptor (attaches Bearer token)
├── router/
│   └── index.js         # All route definitions — add new pages here
└── pages/
    ├── auth/            # Login, Register, ForgotPassword, ResetPassword, EmailVerification, CompleteProfile
    ├── admin/
    │   ├── users/       # User CRUD + staff roles sub-pages
    │   ├── roles/       # Role CRUD
    │   ├── permissions/ # Permission CRUD
    │   ├── sites/       # Site CRUD
    │   └── staff-positions/ # Staff position CRUD
    ├── errors/          # 401, 403, 404, 500
    ├── IndexPage.vue
    ├── DashboardPage.vue
    └── ProfilePage.vue
```

To add a new page: create a `.vue` file and register the route in `resources/js/router/index.js`.

---

## Xdebug

Pre-installed in the container, listens on port **9003**.
