#!/bin/bash

# Script to clear all Laravel caches in production
# Run this after deployment to ensure fresh routes and configs

echo "🧹 Clearing Laravel caches..."

# Clear route cache
php artisan route:clear
echo "✓ Route cache cleared"

# Clear config cache
php artisan config:clear
echo "✓ Config cache cleared"

# Clear application cache
php artisan cache:clear
echo "✓ Application cache cleared"

# Clear view cache
php artisan view:clear
echo "✓ View cache cleared"

# Clear compiled classes
php artisan clear-compiled
echo "✓ Compiled classes cleared"

# Optimize for production (recreate caches)
echo ""
echo "🚀 Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "✅ All caches cleared and optimized!"
echo ""
echo "Note: If you're using OPcache, you may need to restart PHP-FPM:"
echo "  sudo systemctl restart php8.4-fpm"
