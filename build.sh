#!/bin/bash
set -e

echo "Deploying Laravel app to Render..."

# Clean up any stale cache
rm -rf bootstrap/cache/routes-v7.php bootstrap/cache/packages.php bootstrap/cache/services.php 2>/dev/null || true

# Install dependencies
composer install --no-dev --optimize-autoloader --no-scripts

# Clear cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Build frontend (if using Vite)
npm install
npm run build

echo "Deployment completed!"
