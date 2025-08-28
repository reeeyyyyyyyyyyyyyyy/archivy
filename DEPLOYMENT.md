# 🚀 ARSIPIN - Deployment Guide Ubuntu Server + PostgreSQL

## 📋 Overview
Dokumentasi lengkap untuk deployment aplikasi ARSIPIN

## 🌐 Environment Configuration (.env)
```bash
# Database Configuration
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=arsipin_db
DB_USERNAME=arsipin_user
DB_PASSWORD=your_secure_password

# Telegram Bot Configuration (Gunakan Dibawah INI)
TELEGRAM_BOT_TOKEN=8227818847:AAHdbjOePkBHM3VKkoTvoYPTZqNBurBbZfU
TELEGRAM_WEBHOOK_URL= "http://domain"/api/telegram/webhook ("http://domain" ganti ini dengan Domain)
TELEGRAM_CHAT_ID=1251337229


# App Configuration
APP_NAME=ARSIPIN
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-domain.com

# Cache & Session
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

---

## 🔧 Phase 1: Server Preparation

### Update System & Install Dependencies
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y nginx postgresql postgresql-contrib php8.2-fpm php8.2-pgsql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath php8.2-intl composer git unzip

# Install Node.js & NPM
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt-get install -y nodejs

# Verify installations
php -v
composer --version
node --version
npm --version
```

---

## 🗄️ Phase 2: PostgreSQL Setup

### Create Database & User
```bash
# Switch to postgres user
sudo -u postgres psql

# Create database and user
CREATE DATABASE arsipin_db;
CREATE USER arsipin_user WITH ENCRYPTED PASSWORD 'your_secure_password';
GRANT ALL PRIVILEGES ON DATABASE arsipin_db TO arsipin_user;
ALTER USER arsipin_user CREATEDB;
\q

# Test connection
psql -h localhost -U arsipin_user -d arsipin_db
```

---

## 📁 Phase 3: Project Deployment

### Upload & Setup Project
```bash
# Navigate to web directory
cd /var/www

# Clone project (if using git) atau upload via SFTP
sudo git clone https://github.com/reeeyyyyyyyyyyyyyyy/archivy.git
# ATAU
sudo mkdir arsipin && cd arsipin
# Upload project files via SFTP/SCP

# Set ownership
sudo chown -R www-data:www-data /var/www/arsipin
sudo chmod -R 755 /var/www/arsipin

# Navigate to project
cd /var/www/arsipin
```

---

## 📦 Phase 4: Dependencies & Environment

### Install Dependencies & Configure Environment
```bash
# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node dependencies
npm install

# Copy environment file
cp .env.example .env

# Edit .env file (gunakan konfigurasi di atas)
sudo nano .env

# Generate application key
php artisan key:generate

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## 🗃️ Phase 5: Database Setup

### Import Database & Run Seeders
```bash
# Import database SQL (gunakan file yang sudah ada)
psql -h localhost -U arsipin_user -d arsipin_db < category.sql
psql -h localhost -U arsipin_user -d arsipin_db < klasifikasi.sql

# Run migrations 
php artisan migrate --seed

# Run seeder yang diperlukan
php artisan db:seed --class=LainnyaCategorySeeder
```

---

## 🏗️ Phase 6: Build Assets & Permissions

### Build Production Assets
```bash
# Build production assets
npm run build

# Set proper permissions
sudo chown -R www-data:www-data /var/www/arsipin
sudo chmod -R 755 /var/www/arsipin
sudo chmod -R 775 storage/
sudo chmod -R 775 bootstrap/cache/

# Create storage symlink
php artisan storage:link
```

---

## 🤖 Phase 7: Telegram Webhook Setup

### Configure Telegram Bot
```bash
# Set webhook Telegram (ganti dengan domain Anda)
php artisan telegram:set-webhook http://your-domain.com

# Test Telegram bot
php artisan telegram:test 1251337229

# Verify webhook
curl -X GET "https://api.telegram.org/bot8227818847:AAHdbjOePkBHM3VKkoTvoYPTZqNBurBbZfU/getWebhookInfo"
```

## 🎯 **UNTUK DEPLOYER:**

1. **Ganti `your-domain.com`** dengan domain server Anda
2. **Token dan Chat ID** Isi dengan Sama seperti diatas line 16-19
3. **Format domain**: Hanya `http://domain.com` (tanpa path)

## 🔍 **Verifikasi Webhook:**

### Setelah set webhook, cek dengan:
```bash
curl -X GET "https://api.telegram.org/bot8227818847:AAHdbjOePkBHM3VKkoTvoYPTZqNBurBbZfU/getWebhookInfo"


Response harus menunjukkan URL yang benar: `http://your-domain.com/api/telegram/webhook`


```

---

## ⏰ Phase 8: Cron Job Setup

### Setup Laravel Scheduler
```bash
# Open crontab
sudo crontab -u www-data -e

# Add Laravel Scheduler (jalankan setiap menit)
* * * * * cd /var/www/arsipin && php artisan schedule:run >> /dev/null 2>&1

# Verify cron job
sudo crontab -u www-data -l

# Start cron service
sudo systemctl start cron
sudo systemctl enable cron

# Test scheduler manual
cd /var/www/arsipin
php artisan schedule:run
```

---

## 🌐 Phase 9: Nginx Configuration

### Configure Web Server
```bash
# Create Nginx site configuration
sudo nano /etc/nginx/sites-available/arsipin

# Add configuration
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/arsipin/public;
    index index.php index.html index.htm;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}

# Enable site
sudo ln -s /etc/nginx/sites-available/arsipin /etc/nginx/sites-enabled/

# Test Nginx configuration
sudo nginx -t

# Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
```

---

## 🧪 Testing & Verification

### Test Commands
```bash
# Test scheduler
php artisan schedule:run

# Test specific commands
php artisan telegram:retention-alert
php artisan telegram:maintenance-notification
php artisan archives:sync-related

# Test Telegram bot
php artisan telegram:test your_chat_id

# Check cron status
sudo systemctl status cron

# Check logs
tail -f storage/logs/laravel.log
sudo tail -f /var/log/nginx/error.log
```

### Check Cron Job
```bash
# Verify cron job exists
sudo crontab -u www-data -l

# Check cron service status
sudo systemctl status cron

# Check cron logs
sudo tail -f /var/log/cron
```

---

## 📝 Deployment Checklist

- [ ] Server packages terinstall
- [ ] PostgreSQL database & user terbuat
- [ ] Project files terupload
- [ ] Dependencies terinstall
- [ ] Environment file terkonfigurasi
- [ ] Database terimport
- [ ] Migrations & seeders berjalan
- [ ] Assets terbuild
- [ ] Permissions terset
- [ ] Telegram webhook terset
- [ ] Cron job aktif
- [ ] Nginx configuration aktif
- [ ] Services running

---

## ⚠️ Important Notes

1. **Ganti `your-domain.com`** dengan domain server Anda
2. **Ganti `your_secure_password`** dengan password yang aman
3. **Ganti `your_chat_id`** dengan chat ID Telegram Anda (sudah ada)

---

## 🔧 Troubleshooting

### Cron Job Issues
```bash
# Check cron service
sudo systemctl status cron

# Check permissions
sudo chown -R www-data:www-data /var/www/arsipin
sudo chmod -R 755 /var/www/arsipin

# Test command manual
sudo -u www-data php /var/www/arsipin/artisan schedule:run
```

### Database Connection Issues
```bash
# Check PostgreSQL service
sudo systemctl status postgresql

# Check connection
psql -h localhost -U arsipin_user -d arsipin_db

# Verify .env configuration
cat .env | grep DB_
```

### Nginx Issues
```bash
# Check Nginx status
sudo systemctl status nginx

# Check configuration
sudo nginx -t

# Check error logs
sudo tail -f /var/log/nginx/error.log
```

---



## 🔄 Update & Maintenance

### Regular Maintenance Commands
```bash
# Update dependencies
composer update --no-dev
npm update

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Backup Commands
```bash
# Backup database
pg_dump -h localhost -U arsipin_user arsipin_db > backup_$(date +%Y%m%d_%H%M%S).sql

# Backup project files
tar -czf arsipin_backup_$(date +%Y%m%d_%H%M%S).tar.gz /var/www/arsipin
```
