#!/bin/bash

# Fix Production .env Configuration
# This script updates critical .env settings for production

echo "🔧 Fixing production .env configuration..."
echo ""

# Backup current .env
echo "Step 1: Backing up current .env..."
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
echo "✓ Backup created"
echo ""

# Check current values
echo "Step 2: Current configuration:"
echo "APP_ENV: $(grep "^APP_ENV=" .env | cut -d '=' -f2)"
echo "APP_DEBUG: $(grep "^APP_DEBUG=" .env | cut -d '=' -f2)"
echo "SESSION_ENCRYPT: $(grep "^SESSION_ENCRYPT=" .env | cut -d '=' -f2)"
echo ""

# Update .env
echo "Step 3: Updating .env..."

# Update APP_ENV
if grep -q "^APP_ENV=" .env; then
    sed -i 's/^APP_ENV=.*/APP_ENV=production/' .env
    echo "✓ APP_ENV set to production"
else
    echo "APP_ENV=production" >> .env
    echo "✓ APP_ENV added"
fi

# Update APP_DEBUG
if grep -q "^APP_DEBUG=" .env; then
    sed -i 's/^APP_DEBUG=.*/APP_DEBUG=false/' .env
    echo "✓ APP_DEBUG set to false"
else
    echo "APP_DEBUG=false" >> .env
    echo "✓ APP_DEBUG added"
fi

# Update SESSION_ENCRYPT
if grep -q "^SESSION_ENCRYPT=" .env; then
    sed -i 's/^SESSION_ENCRYPT=.*/SESSION_ENCRYPT=false/' .env
    echo "✓ SESSION_ENCRYPT set to false"
else
    echo "SESSION_ENCRYPT=false" >> .env
    echo "✓ SESSION_ENCRYPT added"
fi

echo ""

# Verify changes
echo "Step 4: New configuration:"
echo "APP_ENV: $(grep "^APP_ENV=" .env | cut -d '=' -f2)"
echo "APP_DEBUG: $(grep "^APP_DEBUG=" .env | cut -d '=' -f2)"
echo "SESSION_ENCRYPT: $(grep "^SESSION_ENCRYPT=" .env | cut -d '=' -f2)"
echo ""

# Clear and rebuild caches
echo "Step 5: Clearing and rebuilding caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan config:cache
echo "✓ Caches rebuilt"
echo ""

echo "✅ Configuration fixed!"
echo ""
echo "⚠️  IMPORTANT: You must now:"
echo "  1. Restart PHP-FPM: sudo systemctl restart php8.4-fpm"
echo "  2. Clear browser cache"
echo "  3. Test login again"
echo ""
echo "If you need to rollback, restore from backup:"
echo "  cp .env.backup.* .env"
echo ""
