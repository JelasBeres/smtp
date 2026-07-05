# MailFlow

MailFlow adalah aplikasi Laravel untuk mengelola kontak, list, segmentasi, template, campaign email, provider SMTP, sending domain, webhook event, import data, unsubscribe token, audit log, queue, cache, dan scheduler.

Gunakan aplikasi ini hanya untuk email yang sah, berbasis izin penerima, dan mengikuti aturan provider email, hukum anti-spam, serta kebijakan domain. Jangan gunakan untuk scraping, spam, spoofing pengirim, bypass filter, probing SMTP, enumerasi mailbox, atau rotasi domain untuk menghindari enforcement provider.

## Daftar Isi

- [Spesifikasi Teknis](#spesifikasi-teknis)
- [Struktur Penting Proyek](#struktur-penting-proyek)
- [Kebutuhan Server](#kebutuhan-server)
- [Instalasi Local Development](#instalasi-local-development)
- [Konfigurasi Environment](#konfigurasi-environment)
- [Database dan Migration](#database-dan-migration)
- [Build Asset Frontend](#build-asset-frontend)
- [Menjalankan Queue Worker](#menjalankan-queue-worker)
- [Menjalankan Scheduler](#menjalankan-scheduler)
- [Instalasi di Shared Hosting / Domain Hosting](#instalasi-di-shared-hosting--domain-hosting)
- [Instalasi di VPS Ubuntu dengan Nginx](#instalasi-di-vps-ubuntu-dengan-nginx)
- [Instalasi di VPS Ubuntu dengan Apache](#instalasi-di-vps-ubuntu-dengan-apache)
- [Konfigurasi SSL HTTPS](#konfigurasi-ssl-https)
- [Konfigurasi Domain dan DNS](#konfigurasi-domain-dan-dns)
- [Konfigurasi SMTP dan Email Provider](#konfigurasi-smtp-dan-email-provider)
- [Konfigurasi Webhook](#konfigurasi-webhook)
- [Optimasi Production](#optimasi-production)
- [Maintenance dan Update](#maintenance-dan-update)
- [Backup dan Restore](#backup-dan-restore)
- [Troubleshooting](#troubleshooting)
- [Checklist Go Live](#checklist-go-live)

## Spesifikasi Teknis

- Framework: Laravel 13.x.
- PHP: minimal PHP 8.3.
- Dependency PHP: Composer.
- Frontend bundler: Vite 8.
- CSS: Tailwind CSS 4.
- Node.js: disarankan Node.js 22 LTS atau versi LTS terbaru yang kompatibel.
- Database: MySQL 8.x atau MariaDB yang kompatibel.
- Queue: Redis.
- Cache: Redis.
- Session default: database.
- Web server production: Nginx atau Apache.
- Process manager production: Supervisor atau systemd untuk queue worker.
- Scheduler production: cron setiap menit.

## Struktur Penting Proyek

```text
app/                 Kode utama aplikasi Laravel
bootstrap/           Bootstrap Laravel
config/              Konfigurasi framework dan service
database/            Migration, seeder, factory
public/              Document root web server
resources/           Blade, CSS, JS sumber
routes/              Definisi route web dan console
storage/             Log, cache runtime, upload private
tests/               Test aplikasi
vendor/              Dependency PHP hasil composer install
node_modules/        Dependency Node hasil npm install
.env.example         Template environment
docker-compose.yml   MySQL dan Redis untuk local development
composer.json        Script dan dependency PHP
package.json         Script dan dependency frontend
```

Document root wajib diarahkan ke folder `public`, bukan ke root proyek.

## Kebutuhan Server

### Minimal Local

- PHP 8.3 atau lebih baru.
- Composer 2.
- Node.js dan npm.
- Docker Desktop atau Docker Engine, jika memakai MySQL/Redis dari `docker-compose.yml`.
- Ekstensi PHP umum Laravel: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `hash`, `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `session`, `tokenizer`, `xml`, `zip`.
- Ekstensi Redis PHP: `phpredis`, karena `.env.example` memakai `REDIS_CLIENT=phpredis`.

### Minimal Production VPS

- Ubuntu 22.04/24.04 LTS atau distro Linux setara.
- CPU 1 core untuk traffic kecil, 2 core atau lebih untuk queue/campaign lebih besar.
- RAM minimal 1 GB, disarankan 2 GB atau lebih.
- Disk SSD minimal 20 GB.
- PHP-FPM 8.3+.
- MySQL 8.x.
- Redis 7.x.
- Nginx atau Apache.
- Supervisor.
- Certbot untuk SSL Lets Encrypt.
- Akses SSH dengan user non-root yang punya sudo.

## Instalasi Local Development

### 1. Clone atau masuk ke folder proyek

```bash
cd /path/ke/MailFlow
```

Jika proyek baru diambil dari Git:

```bash
git clone <url-repository> mailflow
cd mailflow
```

### 2. Salin file environment

```bash
cp .env.example .env
```

### 3. Install dependency PHP

```bash
composer install
```

### 4. Install dependency Node

```bash
npm install
```

### 5. Jalankan MySQL dan Redis local

File `docker-compose.yml` sudah menyediakan MySQL 8.4 dan Redis 7.

```bash
docker compose up -d
```

Default database local:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mailflow
DB_USERNAME=mailflow
DB_PASSWORD=mailflow
```

Default Redis local:

```dotenv
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 6. Generate application key

```bash
php artisan key:generate
```

### 7. Jalankan migration dan seeder

```bash
php artisan migrate --seed
```

Jika tidak ingin seed data:

```bash
php artisan migrate
```

### 8. Build asset atau jalankan Vite

Untuk development dengan hot reload:

```bash
npm run dev
```

Untuk build asset siap production:

```bash
npm run build
```

### 9. Jalankan server local

Mode lengkap sesuai script Composer:

```bash
composer run dev
```

Script tersebut menjalankan server Laravel, queue listener, log watcher, dan Vite secara paralel.

Mode manual:

```bash
php artisan serve
```

Buka aplikasi:

```text
http://127.0.0.1:8000
```

### 10. Login development

Default dari `.env.example`:

```text
Email: admin@example.com
Password: password
```

Ganti password ini segera di luar local development.

## Konfigurasi Environment

File utama konfigurasi adalah `.env`. Jangan commit `.env` production ke repository.

Contoh konfigurasi local:

```dotenv
APP_NAME=MailFlow
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mailflow
DB_USERNAME=mailflow
DB_PASSWORD=mailflow

SESSION_DRIVER=database
QUEUE_CONNECTION=redis
CACHE_STORE=redis

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS=admin@example.com
MAIL_FROM_NAME="${APP_NAME}"

MAILFLOW_DEFAULT_ADMIN_EMAIL=admin@example.com
MAILFLOW_DEFAULT_ADMIN_PASSWORD=password
MAILFLOW_IMPORT_MAX_KB=10240
MAILFLOW_WEBHOOK_SECRET=
```

Contoh konfigurasi production:

```dotenv
APP_NAME=MailFlow
APP_ENV=production
APP_DEBUG=false
APP_URL=https://mail.example.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mailflow_prod
DB_USERNAME=mailflow_user
DB_PASSWORD=password-kuat

SESSION_DRIVER=database
QUEUE_CONNECTION=redis
CACHE_STORE=redis

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=smtp.provider-resmi.com
MAIL_PORT=587
MAIL_USERNAME=username-provider
MAIL_PASSWORD=password-provider
MAIL_FROM_ADDRESS=no-reply@example.com
MAIL_FROM_NAME="MailFlow"

MAILFLOW_DEFAULT_ADMIN_EMAIL=admin@example.com
MAILFLOW_DEFAULT_ADMIN_PASSWORD=password-awal-yang-kuat
MAILFLOW_IMPORT_MAX_KB=10240
MAILFLOW_WEBHOOK_SECRET=isi-random-panjang
```

Nilai yang wajib diperhatikan:

- `APP_ENV=production` untuk server live.
- `APP_DEBUG=false` untuk production.
- `APP_URL` harus sama dengan URL final domain HTTPS.
- `APP_KEY` wajib ada dan jangan berubah setelah aplikasi berisi data terenkripsi.
- `DB_*` harus sesuai database server.
- `QUEUE_CONNECTION=redis` agar job campaign/import/webhook berjalan asynchronous.
- `CACHE_STORE=redis` agar cache production stabil.
- `MAILFLOW_WEBHOOK_SECRET` wajib unik jika webhook provider digunakan.

## Database dan Migration

Jalankan migration:

```bash
php artisan migrate
```

Jalankan migration beserta seeder:

```bash
php artisan migrate --seed
```

Production deploy biasanya memakai:

```bash
php artisan migrate --force
```

Reset local database dari awal:

```bash
php artisan migrate:fresh --seed
```

Jangan jalankan `migrate:fresh` di production karena akan menghapus tabel.

Tabel inti aplikasi mencakup user, session, cache, jobs, contacts, contact lists, segments, templates, campaigns, recipients, suppression, provider settings, sending domains, webhook events, imports, unsubscribe tokens, dan audit logs.

## Build Asset Frontend

Untuk development:

```bash
npm run dev
```

Untuk production:

```bash
npm run build
```

Hasil build akan masuk ke `public/build`. Pastikan folder ini ikut terdeploy ke server production jika build dilakukan di local/CI.

## Menjalankan Queue Worker

Queue dipakai untuk pekerjaan asynchronous seperti import CSV, validasi, pengiriman campaign, pengiriman email, dan pemrosesan webhook.

Command manual:

```bash
php artisan queue:work redis --queue=imports,validation,campaigns,emails,webhooks --tries=3 --timeout=120
```

Untuk production, jangan menjalankan queue manual di terminal biasa. Gunakan Supervisor atau systemd agar queue otomatis restart jika mati.

Contoh Supervisor:

```ini
[program:mailflow-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/mailflow/artisan queue:work redis --queue=imports,validation,campaigns,emails,webhooks --tries=3 --timeout=120 --sleep=3
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/mailflow/storage/logs/worker.log
stopwaitsecs=3600
```

Aktifkan Supervisor:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start mailflow-worker:*
sudo supervisorctl status
```

Restart queue setelah deploy:

```bash
php artisan queue:restart
```

## Menjalankan Scheduler

Laravel scheduler wajib dipanggil setiap menit dengan cron.

Edit crontab user web server atau user deploy:

```bash
crontab -e
```

Tambahkan:

```cron
* * * * * cd /var/www/mailflow && php artisan schedule:run >> /dev/null 2>&1
```

Pastikan path `/var/www/mailflow` disesuaikan dengan lokasi proyek di server.

## Instalasi di Shared Hosting / Domain Hosting

Gunakan cara ini jika hosting memakai cPanel, DirectAdmin, Plesk, atau panel sejenis.

### Syarat Shared Hosting

- PHP 8.3+ tersedia.
- Composer tersedia via SSH, atau dependency bisa diupload dari local.
- Node.js tersedia, atau asset dibuild di local lalu diupload.
- MySQL tersedia.
- Cron job tersedia.
- Redis tersedia jika ingin queue/cache sesuai konfigurasi default. Jika Redis tidak tersedia, gunakan database queue/cache dengan konsekuensi performa lebih rendah.
- Document root domain/subdomain bisa diarahkan ke folder `public`.

### Struktur Upload yang Disarankan

Contoh:

```text
/home/username/mailflow/          Root aplikasi Laravel
/home/username/public_html/       Public web default hosting
```

Idealnya domain atau subdomain diarahkan langsung ke:

```text
/home/username/mailflow/public
```

Jika panel tidak mengizinkan document root di luar `public_html`, gunakan subdomain yang document root-nya bisa diubah. Jangan menaruh seluruh root Laravel di `public_html` tanpa proteksi karena file `.env` dan source code bisa terekspos.

### Langkah Shared Hosting

1. Upload seluruh proyek ke folder non-public, misalnya `/home/username/mailflow`.
2. Pastikan folder `public` menjadi document root domain/subdomain.
3. Buat database MySQL, user database, dan password dari panel hosting.
4. Buat file `.env` dari `.env.example`.
5. Sesuaikan `APP_URL`, `DB_*`, `MAIL_*`, `APP_ENV`, dan `APP_DEBUG`.
6. Jalankan `composer install --no-dev --optimize-autoloader` via SSH jika Composer tersedia.
7. Jika Composer tidak tersedia, jalankan Composer di local dengan platform PHP yang cocok lalu upload folder `vendor`.
8. Jalankan `npm run build` di local atau server, lalu pastikan `public/build` terupload.
9. Generate key jika belum ada: `php artisan key:generate`.
10. Jalankan migration: `php artisan migrate --force`.
11. Jalankan optimasi: `php artisan optimize`.
12. Tambahkan cron scheduler setiap menit.
13. Jika hosting mendukung queue process persistent, jalankan queue worker. Jika tidak, gunakan cron untuk queue dengan limit waktu pendek.

Contoh cron queue fallback untuk shared hosting tanpa Supervisor:

```cron
* * * * * cd /home/username/mailflow && php artisan queue:work --stop-when-empty --queue=imports,validation,campaigns,emails,webhooks --tries=3 --timeout=120 >> /dev/null 2>&1
```

Fallback ini tidak sekuat Supervisor, tetapi cukup untuk hosting terbatas.

### Jika Redis Tidak Tersedia di Shared Hosting

Ubah `.env`:

```dotenv
QUEUE_CONNECTION=database
CACHE_STORE=database
```

Lalu jalankan:

```bash
php artisan migrate --force
php artisan config:clear
php artisan cache:clear
```

Catatan: untuk campaign besar, Redis tetap lebih disarankan.

## Instalasi di VPS Ubuntu dengan Nginx

Contoh ini memakai domain `mail.example.com` dan folder aplikasi `/var/www/mailflow`.

### 1. Update server

```bash
sudo apt update
sudo apt upgrade -y
```

### 2. Install paket dasar

```bash
sudo apt install -y software-properties-common curl unzip git supervisor cron ca-certificates gnupg
```

### 3. Install Nginx, MySQL, Redis

```bash
sudo apt install -y nginx mysql-server redis-server
```

### 4. Install PHP dan ekstensi

Sesuaikan repository PHP dengan distro yang dipakai. Untuk Ubuntu yang sudah menyediakan PHP 8.3:

```bash
sudo apt install -y php8.3-fpm php8.3-cli php8.3-common php8.3-mysql php8.3-redis php8.3-curl php8.3-mbstring php8.3-xml php8.3-zip php8.3-bcmath php8.3-intl
```

Cek versi:

```bash
php -v
php -m
```

### 5. Install Composer

```bash
curl -sS https://getcomposer.org/installer -o composer-setup.php
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
composer --version
rm composer-setup.php
```

### 6. Install Node.js

Gunakan Node.js LTS. Contoh dengan NodeSource:

```bash
curl -fsSL https://deb.nodesource.com/setup_lts.x | sudo -E bash -
sudo apt install -y nodejs
node -v
npm -v
```

### 7. Buat database production

Masuk MySQL:

```bash
sudo mysql
```

SQL contoh:

```sql
CREATE DATABASE mailflow_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'mailflow_user'@'localhost' IDENTIFIED BY 'password-kuat-yang-unik';
GRANT ALL PRIVILEGES ON mailflow_prod.* TO 'mailflow_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 8. Deploy source code

```bash
sudo mkdir -p /var/www/mailflow
sudo chown -R $USER:www-data /var/www/mailflow
git clone <url-repository> /var/www/mailflow
cd /var/www/mailflow
```

Jika upload manual, pastikan semua file proyek berada di `/var/www/mailflow`.

### 9. Install dependency production

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

Jika tidak ada `package-lock.json`, gunakan:

```bash
npm install
npm run build
```

### 10. Konfigurasi `.env`

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:

```bash
nano .env
```

Pastikan:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://mail.example.com
DB_DATABASE=mailflow_prod
DB_USERNAME=mailflow_user
DB_PASSWORD=password-kuat-yang-unik
QUEUE_CONNECTION=redis
CACHE_STORE=redis
```

### 11. Permission storage dan cache

```bash
sudo chown -R www-data:www-data /var/www/mailflow/storage /var/www/mailflow/bootstrap/cache
sudo chmod -R ug+rwX /var/www/mailflow/storage /var/www/mailflow/bootstrap/cache
```

### 12. Migration dan optimasi

```bash
php artisan migrate --force
php artisan storage:link
php artisan optimize
```

### 13. Konfigurasi Nginx

Buat file:

```bash
sudo nano /etc/nginx/sites-available/mailflow
```

Isi contoh:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name mail.example.com;
    root /var/www/mailflow/public;

    index index.php index.html;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    client_max_body_size 20M;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Aktifkan site:

```bash
sudo ln -s /etc/nginx/sites-available/mailflow /etc/nginx/sites-enabled/mailflow
sudo nginx -t
sudo systemctl reload nginx
```

### 14. Konfigurasi Supervisor queue

Buat file:

```bash
sudo nano /etc/supervisor/conf.d/mailflow-worker.conf
```

Isi:

```ini
[program:mailflow-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/mailflow/artisan queue:work redis --queue=imports,validation,campaigns,emails,webhooks --tries=3 --timeout=120 --sleep=3
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/mailflow/storage/logs/worker.log
stopwaitsecs=3600
```

Aktifkan:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl status
```

### 15. Konfigurasi cron scheduler

```bash
sudo crontab -u www-data -e
```

Tambahkan:

```cron
* * * * * cd /var/www/mailflow && php artisan schedule:run >> /dev/null 2>&1
```

## Instalasi di VPS Ubuntu dengan Apache

Install Apache dan PHP module/FPM:

```bash
sudo apt install -y apache2 libapache2-mod-fcgid php8.3-fpm
sudo a2enmod rewrite proxy_fcgi setenvif
sudo a2enconf php8.3-fpm
```

Buat virtual host:

```bash
sudo nano /etc/apache2/sites-available/mailflow.conf
```

Isi contoh:

```apache
<VirtualHost *:80>
    ServerName mail.example.com
    DocumentRoot /var/www/mailflow/public

    <Directory /var/www/mailflow/public>
        AllowOverride All
        Require all granted
        Options FollowSymLinks
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/mailflow-error.log
    CustomLog ${APACHE_LOG_DIR}/mailflow-access.log combined
</VirtualHost>
```

Aktifkan site:

```bash
sudo a2ensite mailflow.conf
sudo a2dissite 000-default.conf
sudo apache2ctl configtest
sudo systemctl reload apache2
```

Langkah Composer, Node, `.env`, migration, permission, queue, dan cron sama seperti instalasi Nginx.

## Konfigurasi SSL HTTPS

### Nginx

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d mail.example.com
```

### Apache

```bash
sudo apt install -y certbot python3-certbot-apache
sudo certbot --apache -d mail.example.com
```

Cek auto-renew:

```bash
sudo certbot renew --dry-run
```

Setelah HTTPS aktif, pastikan `.env`:

```dotenv
APP_URL=https://mail.example.com
```

Lalu refresh config:

```bash
php artisan optimize:clear
php artisan optimize
```

## Konfigurasi Domain dan DNS

Untuk domain aplikasi web:

```text
mail.example.com A     203.0.113.10
mail.example.com AAAA  2001:db8::10
```

Gunakan `AAAA` hanya jika VPS punya IPv6 yang benar.

Untuk domain pengirim email, ikuti instruksi provider SMTP/email resmi. Umumnya butuh:

```text
example.com      TXT   v=spf1 include:provider.example ~all
selector._domainkey.example.com CNAME/TXT sesuai DKIM provider
_dmarc.example.com TXT v=DMARC1; p=quarantine; rua=mailto:dmarc@example.com
```

Catatan penting:

- SPF harus sesuai provider pengirim.
- DKIM harus diverifikasi dari panel provider.
- DMARC sebaiknya mulai dari `p=none` untuk observasi, lalu naik ke `quarantine` atau `reject` jika sudah yakin.
- Jangan memakai domain orang lain atau spoofing alamat pengirim.
- Gunakan alamat unsubscribe dan identitas pengirim yang jelas.

## Konfigurasi SMTP dan Email Provider

Untuk development, gunakan log mailer:

```dotenv
MAIL_MAILER=log
```

Untuk production SMTP:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.provider-resmi.com
MAIL_PORT=587
MAIL_USERNAME=username-provider
MAIL_PASSWORD=password-provider
MAIL_FROM_ADDRESS=no-reply@example.com
MAIL_FROM_NAME="MailFlow"
```

Port umum:

- `587` untuk STARTTLS, rekomendasi utama.
- `465` untuk TLS eksplisit.
- `25` hanya jika provider dan VPS mengizinkan.

Jika memakai Amazon SES, install SDK terlebih dahulu jika adapter SES diaktifkan:

```bash
composer require aws/aws-sdk-php
```

Simpan credential provider melalui `.env`, secret manager, atau panel server yang aman. Jangan commit username, password, API key, secret key, private key, token webhook, atau credential SMTP.

## Konfigurasi Webhook

Endpoint generic:

```text
POST /webhooks/email/{provider}
```

Contoh URL:

```text
https://mail.example.com/webhooks/email/provider-name
```

Event yang biasanya diproses meliputi sent, delivered, delayed, soft bounce, hard bounce, rejected, complaint, dan unsubscribe.

Pastikan:

- Provider diarahkan ke URL HTTPS.
- Secret webhook diset pada `MAILFLOW_WEBHOOK_SECRET` jika diperlukan.
- Queue worker aktif karena webhook diproses asynchronous.
- Log dicek setelah test event dikirim.

## Optimasi Production

Jalankan setelah `.env`, dependency, dan migration selesai:

```bash
php artisan optimize
```

Jika ada perubahan `.env`, route, config, view, atau deploy baru:

```bash
php artisan optimize:clear
php artisan optimize
php artisan queue:restart
```

Permission wajib:

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R ug+rwX storage bootstrap/cache
```

PHP production yang disarankan:

```ini
memory_limit=256M
upload_max_filesize=20M
post_max_size=20M
max_execution_time=120
opcache.enable=1
opcache.enable_cli=0
opcache.memory_consumption=128
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
```

Jika sering deploy manual dan memakai `opcache.validate_timestamps=0`, reload PHP-FPM setelah deploy:

```bash
sudo systemctl reload php8.3-fpm
```

## Maintenance dan Update

Alur deploy update production:

```bash
cd /var/www/mailflow
php artisan down
git pull
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan optimize:clear
php artisan optimize
php artisan queue:restart
php artisan up
```

Jika tidak memakai Git, upload file versi baru lalu jalankan command dari `composer install` sampai `php artisan up`.

Cek status service:

```bash
sudo systemctl status nginx
sudo systemctl status php8.3-fpm
sudo systemctl status mysql
sudo systemctl status redis-server
sudo supervisorctl status
```

Cek log aplikasi:

```bash
tail -f storage/logs/laravel.log
```

## Backup dan Restore

### Backup Database

```bash
mysqldump -u mailflow_user -p mailflow_prod > mailflow_prod_$(date +%F_%H-%M-%S).sql
```

### Restore Database

```bash
mysql -u mailflow_user -p mailflow_prod < backup.sql
```

### Backup File Penting

Backup minimal:

- `.env`.
- `storage/app`.
- Database dump.
- File custom upload jika ada.
- Konfigurasi Nginx/Apache.
- Konfigurasi Supervisor.

Contoh arsip storage:

```bash
tar -czf storage_backup_$(date +%F).tar.gz storage/app
```

Simpan backup di lokasi berbeda dari VPS utama.

## Troubleshooting

### Halaman 500

Langkah cek:

```bash
tail -f storage/logs/laravel.log
php artisan about
php artisan config:clear
```

Penyebab umum:

- `.env` salah.
- `APP_KEY` belum dibuat.
- Permission `storage` atau `bootstrap/cache` salah.
- Database tidak bisa terkoneksi.
- Dependency Composer belum lengkap.

### Halaman 403 atau file `.env` terekspos

Penyebab umum:

- Document root salah.
- Web server diarahkan ke root proyek, bukan `public`.

Solusi:

- Arahkan domain ke `public`.
- Pastikan rule deny dotfile aktif di Nginx/Apache.

### Vite asset tidak muncul

Jalankan:

```bash
npm install
npm run build
```

Pastikan `public/build` ada dan terbaca oleh web server.

### Database connection refused

Cek `.env`:

```dotenv
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mailflow_prod
DB_USERNAME=mailflow_user
DB_PASSWORD=password
```

Cek service:

```bash
sudo systemctl status mysql
```

Test login:

```bash
mysql -u mailflow_user -p mailflow_prod
```

### Redis connection refused

Cek service:

```bash
sudo systemctl status redis-server
redis-cli ping
```

Jika muncul `PONG`, Redis aktif.

Jika extension Redis PHP belum ada:

```bash
php -m | grep redis
```

Install jika belum tersedia:

```bash
sudo apt install -y php8.3-redis
sudo systemctl restart php8.3-fpm
```

### Queue tidak memproses job

Cek worker:

```bash
sudo supervisorctl status
```

Restart:

```bash
php artisan queue:restart
sudo supervisorctl restart mailflow-worker:*
```

Cek failed jobs:

```bash
php artisan queue:failed
```

Retry failed jobs:

```bash
php artisan queue:retry all
```

### Scheduler tidak berjalan

Cek cron:

```bash
crontab -l
sudo crontab -u www-data -l
```

Test manual:

```bash
php artisan schedule:run -vvv
```

### Email tidak terkirim

Cek:

- `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`.
- Port SMTP diblokir VPS atau tidak.
- Domain sender sudah verifikasi SPF, DKIM, DMARC.
- Queue worker berjalan.
- Provider tidak menolak karena reputasi domain/IP.
- Log Laravel dan log provider.

Untuk test local tanpa kirim email nyata, gunakan:

```dotenv
MAIL_MAILER=log
```

### Upload/import gagal

Cek batas `.env`:

```dotenv
MAILFLOW_IMPORT_MAX_KB=10240
```

Cek PHP:

```ini
upload_max_filesize=20M
post_max_size=20M
memory_limit=256M
```

Cek web server:

```nginx
client_max_body_size 20M;
```

### Error permission storage

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R ug+rwX storage bootstrap/cache
```

### APP_KEY berubah dan data terenkripsi tidak bisa dibaca

`APP_KEY` dipakai Laravel untuk enkripsi. Jangan mengganti `APP_KEY` production setelah aplikasi berjalan. Jika hilang dari backup, data terenkripsi seperti credential provider bisa tidak bisa didekripsi.

## Testing

Jalankan test:

```bash
composer test
```

Atau langsung:

```bash
php artisan test
```

Format kode Laravel Pint:

```bash
./vendor/bin/pint
```

## Checklist Go Live

- `APP_ENV=production`.
- `APP_DEBUG=false`.
- `APP_URL` memakai HTTPS domain final.
- `APP_KEY` sudah dibuat dan dibackup aman.
- Database production sudah dibuat.
- `php artisan migrate --force` sukses.
- `npm run build` sukses dan `public/build` ada.
- Document root mengarah ke `public`.
- SSL aktif dan auto-renew valid.
- Queue worker aktif via Supervisor.
- Cron scheduler aktif setiap menit.
- Redis aktif dan bisa diakses PHP.
- Permission `storage` dan `bootstrap/cache` benar.
- SMTP provider resmi sudah dikonfigurasi.
- SPF, DKIM, dan DMARC domain pengirim sudah benar.
- Webhook provider sudah diarahkan ke endpoint HTTPS.
- Default admin password sudah diganti.
- Backup database dan `.env` sudah dibuat.
- Log tidak menunjukkan error kritis.

## Perintah Cepat

Local setup dari nol:

```bash
cp .env.example .env
composer install
npm install
docker compose up -d
php artisan key:generate
php artisan migrate --seed
npm run build
composer run dev
```

Production deploy ringkas:

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan optimize
php artisan queue:restart
```

Clear cache saat debugging:

```bash
php artisan optimize:clear
```
# smtp
