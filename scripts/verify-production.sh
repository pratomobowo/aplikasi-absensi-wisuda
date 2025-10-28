#!/bin/bash

# Production Verification Script
# Run this to verify your production setup is correct

echo "🔍 Verifying production setup..."
echo ""

# Check PHP version
echo "📌 PHP Version:"
php -v | head -1
echo ""

# Check Laravel version
echo "📌 Laravel Version:"
php artisan --version
echo ""

# Check environment
echo "📌 Environment:"
php artisan env
echo ""

# Check if APP_KEY is set
echo "📌 APP_KEY:"
if php artisan tinker --execute="echo config('app.key') ? '✓ Set' : '✗ Not set';" 2>/dev/null; then
    echo ""
else
    echo "✗ Error checking APP_KEY"
fi

# Check database connection
echo "📌 Database Connection:"
if php artisan db:show 2>/dev/null | head -5; then
    echo "✓ Database connected"
else
    echo "✗ Database connection failed"
fi
echo ""

# Check if sessions table exists
echo "📌 Sessions Table:"
if php artisan tinker --execute="echo Schema::hasTable('sessions') ? '✓ Exists' : '✗ Not found';" 2>/dev/null; then
    echo ""
else
    echo "✗ Error checking sessions table"
fi

# Check storage permissions
echo "📌 Storage Permissions:"
if [ -w "storage/logs" ]; then
    echo "✓ storage/logs is writable"
else
    echo "✗ storage/logs is not writable"
fi

if [ -w "bootstrap/cache" ]; then
    echo "✓ bootstrap/cache is writable"
else
    echo "✗ bootstrap/cache is not writable"
fi
echo ""

# Check important routes
echo "📌 Important Routes:"
echo "Checking /admin/login..."
php artisan route:list --path=admin/login 2>/dev/null | grep -q "admin/login" && echo "✓ Admin login route exists" || echo "✗ Admin login route not found"

echo "Checking /livewire/update..."
php artisan route:list --path=livewire/update 2>/dev/null | grep -q "livewire/update" && echo "✓ Livewire update route exists" || echo "✗ Livewire update route not found"
echo ""

# Check if caches are optimized
echo "📌 Cache Status:"
if [ -f "bootstrap/cache/config.php" ]; then
    echo "✓ Config cached"
else
    echo "⚠ Config not cached (run: php artisan config:cache)"
fi

if [ -f "bootstrap/cache/routes-v7.php" ]; then
    echo "✓ Routes cached"
else
    echo "⚠ Routes not cached (run: php artisan route:cache)"
fi

if [ -f "bootstrap/cache/packages.php" ]; then
    echo "✓ Packages cached"
else
    echo "⚠ Packages not cached (run: php artisan optimize)"
fi
echo ""

# Check .env critical settings
echo "📌 Critical .env Settings:"
echo "APP_ENV: $(grep "^APP_ENV=" .env | cut -d '=' -f2)"
echo "APP_DEBUG: $(grep "^APP_DEBUG=" .env | cut -d '=' -f2)"
echo "APP_URL: $(grep "^APP_URL=" .env | cut -d '=' -f2)"
echo "SESSION_DRIVER: $(grep "^SESSION_DRIVER=" .env | cut -d '=' -f2)"
echo "SESSION_SECURE_COOKIE: $(grep "^SESSION_SECURE_COOKIE=" .env | cut -d '=' -f2)"
echo ""

echo "✅ Verification complete!"
echo ""
echo "If you see any ✗ or ⚠ above, please fix those issues."
