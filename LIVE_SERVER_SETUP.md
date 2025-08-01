# Live Server Setup Commands

## Fix 403 Forbidden Error

Run these commands on your live server in order:

### 1. Set Proper File Permissions
```bash
# Set directory permissions
chmod -R 755 /path/to/your/laravel/project
chmod -R 644 /path/to/your/laravel/project/storage
chmod -R 644 /path/to/your/laravel/project/bootstrap/cache

# Make storage and cache writable
chmod -R 775 /path/to/your/laravel/project/storage
chmod -R 775 /path/to/your/laravel/project/bootstrap/cache
```

### 2. Clear All Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 3. Cache for Production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4. Set Storage Permissions
```bash
# Create storage links
php artisan storage:link

# Set proper ownership (replace www-data with your web server user)
chown -R www-data:www-data /path/to/your/laravel/project/storage
chown -R www-data:www-data /path/to/your/laravel/project/bootstrap/cache
```

### 5. Check .env Configuration
Make sure your `.env` file has:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

### 6. Run Migrations (if needed)
```bash
php artisan migrate --force
```

### 7. Restart Web Server
```bash
# For Apache
sudo systemctl restart apache2

# For Nginx
sudo systemctl restart nginx

# For both
sudo systemctl restart apache2 && sudo systemctl restart nginx
```

## Common Issues and Solutions

### If still getting 403:
1. Check web server configuration
2. Ensure mod_rewrite is enabled (Apache)
3. Check .htaccess file exists in public directory
4. Verify file ownership and permissions

### For Apache, ensure .htaccess exists in public/:
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### For Nginx, ensure proper configuration:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## Quick Fix Commands (Run in order):
```bash
# 1. Navigate to your project directory
cd /path/to/your/laravel/project

# 2. Set permissions
chmod -R 755 .
chmod -R 775 storage bootstrap/cache

# 3. Clear and cache
php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear
php artisan config:cache && php artisan route:cache && php artisan view:cache

# 4. Create storage link
php artisan storage:link

# 5. Set ownership (replace www-data with your web server user)
chown -R www-data:www-data storage bootstrap/cache

# 6. Restart web server
sudo systemctl restart apache2
# or
sudo systemctl restart nginx
``` 