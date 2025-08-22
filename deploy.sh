#!/bin/bash

# Deployment script for Xtrapay Business
echo "Starting deployment..."

# Navigate to project directory
cd /path/to/your/xtrapay-business

# Pull latest changes
echo "Pulling latest changes from git..."
git pull origin main

# Install dependencies
echo "Installing dependencies..."
composer install --no-dev --optimize-autoloader

# Clear all caches
echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Run migrations
echo "Running database migrations..."
php artisan migrate --force

# Set permissions
echo "Setting permissions..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Restart services (if needed)
echo "Restarting services..."
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm

echo "Deployment completed successfully!"
