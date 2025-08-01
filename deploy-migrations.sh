#!/bin/bash

# XtraPay Business - Migration Deployment Script
# Run this script on your shared host after uploading your Laravel application

echo "=== XtraPay Business Migration Deployment ==="
echo ""

# Check if we're in the Laravel project directory
if [ ! -f "artisan" ]; then
    echo "Error: artisan file not found. Please run this script from your Laravel project root directory."
    exit 1
fi

# Check if .env file exists
if [ ! -f ".env" ]; then
    echo "Error: .env file not found. Please create your .env file with production settings first."
    exit 1
fi

echo "1. Clearing application cache..."
php artisan cache:clear

echo "2. Clearing config cache..."
php artisan config:clear

echo "3. Clearing route cache..."
php artisan route:clear

echo "4. Clearing view cache..."
php artisan view:clear

echo "5. Running database migrations..."
php artisan migrate --force

echo "6. Checking migration status..."
php artisan migrate:status

echo ""
echo "=== Migration deployment completed! ==="
echo ""
echo "If you see any errors above, please check:"
echo "- Your database credentials in .env file"
echo "- Database connection"
echo "- PHP version compatibility"
echo ""
echo "To check if everything is working, visit your website." 