# Production Setup

## Server Requirements

- Ubuntu 22.04+ (or any Debian-based Linux)
- PHP 8.3+
- Apache2
- MySQL 8.0
- Composer
- Node.js 22.x (build only, can be removed after)

---

## 1. Install Server Dependencies

```bash
sudo apt update && sudo apt upgrade -y

# PHP 8.4
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install -y php8.4 php8.4-cli php8.4-fpm php8.4-mysql php8.4-mbstring \
    php8.4-zip php8.4-bcmath php8.4-xml php8.4-curl php8.4-intl libapache2-mod-php8.4

# Apache
sudo apt install -y apache2
sudo a2enmod rewrite
sudo systemctl enable apache2

# MySQL
sudo apt install -y mysql-server
sudo systemctl enable mysql

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Node.js 22 (for building assets only)
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo bash -
sudo apt install -y nodejs
```

---

## 2. Configure MySQL

```bash
sudo mysql_secure_installation
```

Then create the database and user:

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

## 3. Deploy Application

```bash
# Clone the repository
sudo mkdir -p /var/www/light-house
sudo chown $USER:$USER /var/www/light-house
git clone <repository-url> /var/www/light-house
cd /var/www/light-house/light-house-src
```

Install dependencies and build frontend assets:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
rm -rf node_modules
```

---

## 4. Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with production values:

```env
APP_NAME=LightHouse
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=light_house
DB_USERNAME=light_house
DB_PASSWORD=your_strong_password

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
```

---

## 5. Run Migrations & Optimize Laravel

```bash
php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 6. Set File Permissions

```bash
sudo chown -R www-data:www-data /var/www/light-house/light-house-src
sudo chmod -R 755 /var/www/light-house/light-house-src
sudo chmod -R 775 /var/www/light-house/light-house-src/storage
sudo chmod -R 775 /var/www/light-house/light-house-src/bootstrap/cache
```

---

## 7. Configure Apache Virtual Host

Create a new virtual host config:

```bash
sudo nano /etc/apache2/sites-available/light-house.conf
```

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    DocumentRoot /var/www/light-house/light-house-src/public

    <Directory /var/www/light-house/light-house-src/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/light-house-error.log
    CustomLog ${APACHE_LOG_DIR}/light-house-access.log combined
</VirtualHost>
```

Enable the site:

```bash
sudo a2ensite light-house.conf
sudo a2dissite 000-default.conf
sudo systemctl restart apache2
```

---

## 8. SSL Certificate (HTTPS)

```bash
sudo apt install -y certbot python3-certbot-apache
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com
```

Certbot will automatically update your Apache config to redirect HTTP to HTTPS.

---

## 9. Queue Worker (optional)

If using queues, set up Supervisor to keep the worker running:

```bash
sudo apt install -y supervisor
sudo nano /etc/supervisor/conf.d/light-house-worker.conf
```

```ini
[program:light-house-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/light-house/light-house-src/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/light-house/light-house-src/storage/logs/worker.log
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start light-house-worker:*
```

---

## 10. Deploying Updates

Each time you deploy new code:

```bash
cd /var/www/light-house/light-house-src

git pull

composer install --no-dev --optimize-autoloader
npm install
npm run build
rm -rf node_modules

php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

sudo supervisorctl restart light-house-worker:*
```

---

## Key Differences from Dev

| | Dev | Production |
|---|---|---|
| `APP_DEBUG` | `true` | `false` |
| `APP_ENV` | `local` | `production` |
| Assets | Vite dev server | `npm run build` static files |
| Node container | Running | Not needed after build |
| Composer | with dev deps | `--no-dev` |
| Laravel cache | Off | `config:cache`, `route:cache`, `view:cache` |
