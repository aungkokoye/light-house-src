# Production Setup

**Domain:** aungkokoye.cloud
**App path:** `/var/www/light-house-src`
**Run as:** root (use `sudo -u www-data` for app commands)

---

## 1. Install Server Dependencies

```bash
sudo apt update && sudo apt upgrade -y

# Apache
sudo apt install -y apache2
sudo a2enmod rewrite headers ssl
sudo systemctl enable apache2

# PHP 8.4
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install -y php8.4 php8.4-cli php8.4-mysql php8.4-mbstring \
    php8.4-zip php8.4-bcmath php8.4-xml php8.4-curl php8.4-intl \
    php8.4-gd php8.4-exif libapache2-mod-php8.4

# MySQL 8.0
sudo apt install -y mysql-server
sudo systemctl start mysql
sudo systemctl enable mysql

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Node.js 22 (for building assets)
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt install -y nodejs
```

---

## 2. Configure MySQL

```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE light_house;
CREATE USER 'light_house'@'localhost' IDENTIFIED BY 'your_strong_password';
GRANT ALL PRIVILEGES ON light_house.* TO 'light_house'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 3. Clone Repository

```bash
cd /var/www
sudo apt install -y git
git clone https://github.com/aungkokoye/light-house-src.git
sudo chown -R www-data:www-data /var/www/light-house-src
```

---

## 4. Git Deploy Key (pull as www-data)

```bash
# Generate SSH key for www-data
sudo mkdir -p /var/www/.ssh
sudo ssh-keygen -t ed25519 -C "deploy@lighthouse" -f /var/www/.ssh/id_ed25519 -N ""
sudo chown -R www-data:www-data /var/www/.ssh
sudo chmod 700 /var/www/.ssh
sudo chmod 600 /var/www/.ssh/id_ed25519

# Print public key → add to GitHub repo → Settings → Deploy keys
sudo cat /var/www/.ssh/id_ed25519.pub

# Switch remote to SSH
sudo -u www-data git -C /var/www/light-house-src remote set-url origin git@github.com:aungkokoye/light-house-src.git

# Trust GitHub host
sudo -u www-data ssh -o StrictHostKeyChecking=accept-new git@github.com

# Test
sudo -u www-data git -C /var/www/light-house-src pull
```

---

## 5. Configure Environment

```bash
sudo cp /var/www/light-house-src/.env.example /var/www/light-house-src/.env
sudo chown www-data:www-data /var/www/light-house-src/.env
sudo chmod 640 /var/www/light-house-src/.env
```

Edit `.env` with production values:

```env
APP_NAME=LightHouse
APP_ENV=production
APP_DEBUG=false
APP_URL=https://aungkokoye.cloud

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=light_house
DB_USERNAME=light_house
DB_PASSWORD=your_strong_password

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=aungkokoye@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_FROM_ADDRESS="noreply@lighthouse-print.com"
MAIL_FROM_NAME="${APP_NAME}"
CONTACT_INQUIRY_EMAIL="info@lighthouse-print.com"

GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
```

---

## 6. Install Dependencies & Build Assets

```bash
sudo -u www-data composer install --no-dev --optimize-autoloader --no-interaction --working-dir=/var/www/light-house-src
cd /var/www/light-house-src
npm ci
npm run build
rm -rf node_modules
```

---

## 7. Storage & Bootstrap Directories

```bash
cd /var/www/light-house-src
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/views
mkdir -p storage/framework/sessions
mkdir -p bootstrap/cache

sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

---

## 8. Run Migrations & Cache

```bash
sudo -u www-data php /var/www/light-house-src/artisan key:generate
sudo -u www-data php /var/www/light-house-src/artisan migrate --force
sudo -u www-data php /var/www/light-house-src/artisan optimize
```

---

## 9. File Permissions

```bash
sudo chown -R www-data:www-data /var/www/light-house-src
sudo chmod -R 775 /var/www/light-house-src/storage
sudo chmod -R 775 /var/www/light-house-src/bootstrap/cache
```

---

## 10. Configure Apache Virtual Host

```bash
sudo vim /etc/apache2/sites-available/lighthouse.conf
```

```apache
<VirtualHost *:80>
    ServerAdmin admin@aungkokoye.cloud
    DocumentRoot /var/www/light-house-src/public
    ServerName aungkokoye.cloud
    ServerAlias www.aungkokoye.cloud

    <Directory /var/www/light-house-src/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

```bash
sudo a2ensite lighthouse.conf
sudo a2dissite 000-default.conf
sudo systemctl reload apache2
```

---

## 11. SSL Certificate (HTTPS)

```bash
sudo apt install -y certbot python3-certbot-apache
sudo certbot --apache -d aungkokoye.cloud -d www.aungkokoye.cloud
```

Certificates expire every 90 days — auto-renewal runs twice daily and renews when <30 days left.

```bash
# Manual renewal
certbot renew
```

---

## 12. After Changing .env

```bash
sudo -u www-data php /var/www/light-house-src/artisan optimize:clear
sudo -u www-data php /var/www/light-house-src/artisan optimize
```

---

## 13. Supervisor (Queue Worker)

Supervisor keeps the queue worker running continuously and restarts it automatically on crash or memory limit.

### Install

```bash
sudo apt install -y supervisor
sudo systemctl enable supervisor
sudo systemctl start supervisor
```

### Deploy Config File

```bash
sudo cp /var/www/light-house-src/light-house-docker/supervisor/lighthouse-worker.conf \
    /etc/supervisor/conf.d/lighthouse-worker.conf
```

The config file (`lighthouse-worker.conf`):

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

### Start the Worker

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start lighthouse-worker:*
```

### Check Status

```bash
sudo supervisorctl status
```

Expected output:
```
lighthouse-worker:lighthouse-worker_00   RUNNING   pid 12345, uptime 0:00:10
```

### Manage Worker

```bash
# Start / stop / restart
sudo supervisorctl start   lighthouse-worker:*
sudo supervisorctl stop    lighthouse-worker:*
sudo supervisorctl restart lighthouse-worker:*

# Tail live worker logs
sudo supervisorctl tail -f lighthouse-worker:lighthouse-worker_00

# Or directly
tail -f /var/www/light-house-src/storage/logs/worker.log
```

### After Config Changes

If you edit `lighthouse-worker.conf`:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl restart lighthouse-worker:*
```

---

## 14. Deploying Updates

Use the deploy script:

```bash
bash /var/www/light-house-src/deploy.sh
```

```bash
#!/bin/bash

# command to run: bash /var/www/light-house-src/deploy.sh

set -e

APP_DIR="/var/www/light-house-src"

echo "==> Pulling latest code..."
sudo -u www-data git -C "$APP_DIR" pull --rebase=false

echo "==> Installing PHP dependencies..."
sudo -u www-data composer install --no-dev --optimize-autoloader --no-interaction --working-dir="$APP_DIR"

echo "==> Clearing cache and config..."
sudo -u www-data php "$APP_DIR/artisan" optimize:clear

echo "==> Building frontend assets..."
cd "$APP_DIR"
npm ci
npm run build
rm -rf node_modules

echo "==> Running database migrations..."
sudo -u www-data php "$APP_DIR/artisan" migrate --force

echo "==> Caching config, routes, views..."
sudo -u www-data php "$APP_DIR/artisan" optimize

echo "==> Restarting queue workers..."
sudo -u www-data php "$APP_DIR/artisan" queue:restart
sudo supervisorctl restart lighthouse-worker:*

echo "==> Fixing permissions..."
sudo chown -R www-data:www-data "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"
sudo chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"

echo "==> Reloading Apache..."
sudo systemctl reload apache2

echo "==> Deploy complete."
```

## Set it at the repo level instead:

```bash
sudo -u www-data git -C /var/www/light-house-src config user.email "admin@aungkokoye.cloud"                                                                                                                                                                                                                                                                                                                                               
sudo -u www-data git -C /var/www/light-house-src config user.name "LightHouse Deploy"
```

---

## Key Differences from Dev

| | Dev | Production |
|---|---|---|
| `APP_DEBUG` | `true` | `false` |
| `APP_ENV` | `local` | `production` |
| Assets | Vite dev server | `npm run build` static files |
| Node | Running container | Removed after build |
| Composer | with dev deps | `--no-dev` |
| Laravel cache | Off | `optimize` |
| Mail | Mailcatcher | Gmail SMTP |
