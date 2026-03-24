# Initial Setup

## Tech Stack

| Layer | Technology                                     |
|-------|------------------------------------------------|
| Backend | Laravel 13 / PHP 8.4                           |
| Frontend | Vue 3 (SinglePageApplication) + Tailwind CSS 4 |
| Bundler | Vite 8                                         |
| Database | MySQL 8.0                                      |
| Runtime | Docker (PHP-Apache + Node + MySQL)             |
| Local email | Mailcatcher (schickling/mailcatcher)       |

---

## Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed and running

---

## 1. Start Docker Containers

Navigate to the docker directory from the project root:

```bash
cd docker
docker compose up -d
```

To stop containers:

```bash
docker compose down
```

**Services started:**

| Service | Container | Port |
|---------|-----------|------|
| PHP 8.4 + Apache | `light_house_app` | http://localhost:8375 |
| Node 22 + Vite   | `light_house_node` | http://localhost:5173 |
| MySQL 8.0        | `light_house_db`   | `localhost:3550` |
| Mailcatcher      | `light_house_mailer` | SMTP: `localhost:2025` · UI: http://localhost:2080 |

---

## 2. Configure Environment

Copy the example environment file inside the `backend` directory:

```bash
cp .env.example .env
```

Update the following DB settings in `.env` (change from sqlite to mysql):

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

> **Note:** `DB_HOST=db` refers to the Docker service name, not `localhost`. Use `localhost` only when connecting from an external DB editor (see below).

---

## 3. Install Dependencies & Run Migrations

The Node container automatically runs `npm install && npm run dev` on startup.

For PHP dependencies and migrations, enter the app container:

```bash
docker exec -it light_house_app bash
cd /var/www/html
```

Then run:

```bash
composer install
php artisan key:generate
php artisan migrate
```

---

## 4. Connecting to the Database from an Editor

Use these credentials when connecting via TablePlus, DBeaver, or similar tools:

```
Host:     127.0.0.1
Port:     3550
Database: light_house
Username: light_house
Password: password
```

Root credentials (for admin access):

```
Username: root
Password: root
```

---

## 5. Local Email Server (Mailcatcher)

The project uses [schickling/mailcatcher](https://hub.docker.com/r/schickling/mailcatcher) as a local SMTP server during development. It catches all outgoing emails and displays them in a web UI — no emails are ever delivered to real inboxes.

### Access

| | URL |
|-|-----|
| Web inbox | http://localhost:2080 |
| SMTP (internal Docker) | `mailer:1025` |
| SMTP (host machine) | `localhost:2025` |

### Environment Config

These values are already set in `.env.example`:

```env
MAIL_MAILER=smtp
MAIL_HOST=mailer
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="noreply@lighthouse.com"
MAIL_FROM_NAME="${APP_NAME}"
```

> `MAIL_HOST=mailer` refers to the Docker service name. The PHP container reaches Mailcatcher over the internal Docker network on port `1025`.

### Usage

1. Start the containers with `docker compose up -d`
2. Trigger an email (e.g. register a new user)
3. Open http://localhost:2080 to see the caught email

All emails sent by the application (verification links, etc.) will appear here in real time.

---

## 6. Running the Dev Environment

Vite starts automatically via the `node` container when you run `docker compose up -d`.

For Laravel processes, inside the `app` container:

```bash
php artisan queue:listen   # Queue worker
php artisan pail           # Log viewer
```

---

## 7. Running Tests

```bash
composer test
```

---

## 8. Seeding the Database

```bash
php artisan db:seed
```

This creates a test user:

```
Name:  Test User
Email: test@example.com
```

---

## Frontend Structure

Vue 3 is set up as a SPA. All routes are handled by Vue — Laravel serves a single blade entry point.

```
resources/
├── js/
│   ├── app.js          # Vue app entry point (mounts #app)
│   ├── App.vue         # Root component
│   └── bootstrap.js    # Axios setup
├── css/
│   └── app.css         # Tailwind CSS entry
└── views/
    └── app.blade.php   # SPA shell (contains <div id="app">)
```

To add new pages, create `.vue` files in `resources/js/` and wire them up with Vue Router (install with `npm install vue-router`).

---

## Xdebug

Xdebug is pre-installed in the container and listens on port **9003**. Configure your IDE to connect to that port for step debugging.
