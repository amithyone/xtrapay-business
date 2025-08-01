# XtraPay Business - Shared Hosting Deployment Guide

This guide will help you deploy your Laravel application to shared hosting and run the necessary migrations.

## Prerequisites

- Shared hosting account with PHP 8.1+ support
- MySQL database access
- FTP/SFTP access to your hosting account
- Composer installed locally (for preparation)

## Step 1: Prepare Your Application Locally

### 1.1 Install Dependencies
```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

### 1.2 Generate Application Key (if not already done)
```bash
php artisan key:generate
```

## Step 2: Upload Files to Shared Hosting

### 2.1 Files to Upload
Upload the following directories and files to your shared hosting:
- `app/`
- `bootstrap/`
- `config/`
- `database/`
- `lang/`
- `public/`
- `resources/`
- `routes/`
- `storage/`
- `vendor/`
- `artisan`
- `composer.json`
- `composer.lock`

### 2.2 Set Permissions
Set the following permissions on your shared hosting:
```bash
chmod 755 storage/
chmod 755 bootstrap/cache/
chmod 644 storage/logs/
chmod 644 storage/framework/cache/
chmod 644 storage/framework/sessions/
chmod 644 storage/framework/views/
```

## Step 3: Configure Environment

### 3.1 Create .env File
Create a `.env` file on your shared hosting with the following content (update with your actual values):

```env
APP_NAME="XtraPay Business"
APP_ENV=production
APP_KEY=base64:SbidDDN+vJBkusXg/2vwvR6BQFHLL0rgvhZGnYYuXiI=
APP_DEBUG=false
APP_URL=https://yourdomain.com

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=your_mail_host
MAIL_PORT=587
MAIL_USERNAME=your_mail_username
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"

VITE_APP_NAME="${APP_NAME}"
```

## Step 4: Run Migrations

### Option A: Using SSH (Recommended)
If your shared hosting provides SSH access:

1. Connect to your hosting via SSH
2. Navigate to your Laravel project directory
3. Run the deployment script:
```bash
./deploy-migrations.sh
```

### Option B: Using Web Browser
If you don't have SSH access:

1. Upload the `run-migrations.php` file to your Laravel project root
2. Visit `https://yourdomain.com/run-migrations.php` in your browser
3. Follow the on-screen instructions
4. **IMPORTANT**: Delete the `run-migrations.php` file after successful migration

### Option C: Manual Commands
If you have access to run PHP commands:

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan migrate --force
php artisan migrate:status
```

## Step 5: Configure Web Server

### 5.1 Apache Configuration
Create a `.htaccess` file in your project root (if not already present):

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

### 5.2 Point Document Root
Configure your web server to point to the `public/` directory of your Laravel application.

## Step 6: Verify Installation

1. Visit your website to ensure it's working
2. Check for any error messages
3. Test the main functionality of your application

## Step 7: Security Checklist

- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Delete `run-migrations.php` file (if used)
- [ ] Ensure `.env` file is not publicly accessible
- [ ] Set proper file permissions
- [ ] Configure SSL certificate
- [ ] Set up regular backups

## Troubleshooting

### Common Issues:

1. **Database Connection Error**
   - Verify database credentials in `.env`
   - Check if database exists
   - Ensure database user has proper permissions

2. **Permission Errors**
   - Set proper permissions on `storage/` and `bootstrap/cache/` directories
   - Ensure web server can write to these directories

3. **Migration Errors**
   - Check if all required PHP extensions are installed
   - Verify database schema compatibility
   - Check for conflicting migrations

4. **500 Internal Server Error**
   - Check error logs in `storage/logs/`
   - Verify `.env` file exists and is properly configured
   - Ensure all required files are uploaded

### Getting Help:
- Check Laravel logs: `storage/logs/laravel.log`
- Enable debug mode temporarily: `APP_DEBUG=true`
- Contact your hosting provider for PHP/MySQL support

## Migration Files Summary

Your application includes the following migrations:
- Users table and authentication
- Admin codes and permissions
- Sites management
- Transactions and transfers
- Business profiles
- Tickets system
- Beneficiaries management
- Various field modifications and optimizations

All migrations are designed to be safe and can be run multiple times without issues. 