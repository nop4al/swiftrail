#!/bin/bash
set -e

echo "Deploying Laravel app..."

# Clean up any stale cache
rm -rf bootstrap/cache/routes-v7.php bootstrap/cache/packages.php bootstrap/cache/services.php 2>/dev/null || true

# Install dependencies
composer install --no-dev --optimize-autoloader --no-scripts

# Generate APP_KEY if not exists
if [ -z "$APP_KEY" ]; then
  echo "Generating APP_KEY..."
  php artisan key:generate
fi

# Clear old caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Generate new caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Build frontend (if using Vite)
npm install
npm run build

echo "Deployment completed!"
