#!/bin/bash

# Production Deployment Script
# Run this script after pulling latest code to production

set -e  # Exit on error

echo "🚀 Starting deployment..."
echo ""

# 1. Pull latest code
echo "📥 Pulling latest code..."
git pull origin main
echo "✓ Code updated"
echo ""

# 2. Install/update dependencies
echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader
npm ci --production
echo "✓ Dependencies installed"
echo ""

# 3. Build assets
echo "🎨 Building assets..."
npm run build
echo "✓ Assets built"
echo ""

# 4. Run migrations
echo "🗄️  Running migrations..."
php artisan migrate --force
echo "✓ Migrations completed"
echo ""

# 5. Clear all caches
echo "🧹 Clearing caches..."
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan clear-compiled
echo "✓ Caches cleared"
echo ""

# 6. Optimize for production
echo "⚡ Optimizing..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:optimize
echo "✓ Optimization completed"
echo ""

# 7. Set permissions
echo "🔐 Setting permissions..."
chmod -R 755 storage bootstrap/cache
echo "✓ Permissions set"
echo ""

echo "✅ Deployment completed successfully!"
echo ""
echo "⚠️  Don't forget to:"
echo "  1. Restart PHP-FPM: sudo systemctl restart php8.4-fpm"
echo "  2. Test the application"
echo ""
