#!/bin/sh
set -e

# Force config to be fresh
php artisan config:clear
php artisan config:cache

echo "=== Running Database Migrations ==="
php artisan migrate --force


# Create necessary directories
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/bootstrap/cache
mkdir -p /tmp

echo "=== Running Database Migrations ==="
php artisan migrate --force

echo "=== Storage Link (ignore if exists) ==="
php artisan storage:link || true

echo "=== Setting Permissions ==="
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "=== Starting Supervisor ==="
exec "$@"