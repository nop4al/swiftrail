#!/bin/bash
set -e

echo "Deploying Laravel app to Render..."

# Install dependencies
composer install --no-dev --optimize-autoloader

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
