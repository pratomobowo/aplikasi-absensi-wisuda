#!/bin/bash

# Complete Fix for Production Issues
# This script fixes all known issues with login and Livewire

set -e

echo "🚀 Complete Production Fix"
echo "=========================="
echo ""

# Step 1: Fix .env
echo "📝 Step 1: Fixing .env configuration..."
if grep -q "APP_ENV=local" .env; then
    sed -i 's/^APP_ENV=.*/APP_ENV=production/' .env
    echo "✓ APP_ENV → production"
fi

if grep -q "APP_DEBUG=true" .env; then
    sed -i 's/^APP_DEBUG=.*/APP_DEBUG=false/' .env
    echo "✓ APP_DEBUG → false"
fi

if grep -q "SESSION_ENCRYPT=true" .env; then
    sed -i 's/^SESSION_ENCRYPT=.*/SESSION_ENCRYPT=false/' .env
    echo "✓ SESSION_ENCRYPT → false"
fi
echo ""

# Step 2: Clear everything
echo "🧹 Step 2: Clearing all caches..."
php artisan optimize:clear 2>/dev/null || {
    php artisan config:clear
    php artisan route:clear
    php artisan cache:clear
    php artisan view:clear
    php artisan clear-compiled
}
echo "✓ All caches cleared"
echo ""

# Step 3: Composer
echo "📦 Step 3: Optimizing Composer..."
composer dump-autoload -o --no-interaction
echo "✓ Composer optimized"
echo ""

# Step 4: Rebuild caches
echo "⚡ Step 4: Rebuilding caches..."
php artisan config:cache
php artisan view:cache
echo "✓ Caches rebuilt"
echo ""

# Step 5: Verify Livewire
echo "🔍 Step 5: Verifying Livewire..."
php artisan tinker --execute="
\$request = Request::create('/livewire/livewire.js', 'GET');
\$response = app()->handle(\$request);
\$size = strlen(\$response->getContent());
echo 'Livewire JS Size: ' . \$size . ' bytes' . PHP_EOL;
if (\$size > 10000) {
    echo '✓ Livewire is working!' . PHP_EOL;
} else {
    echo '✗ Livewire JS is still broken (size: ' . \$size . ')' . PHP_EOL;
    echo 'Try: composer require livewire/livewire --no-interaction' . PHP_EOL;
}
"
echo ""

# Step 6: Check sessions table
echo "🗄️  Step 6: Checking database..."
php artisan migrate --force 2>/dev/null && echo "✓ Migrations up to date" || echo "⚠️  Migration check skipped"
echo ""

# Step 7: Fix permissions
echo "🔐 Step 7: Fixing permissions..."
chmod -R 755 storage bootstrap/cache 2>/dev/null && echo "✓ Permissions fixed" || echo "⚠️  Permission fix skipped (may need sudo)"
echo ""

echo "✅ Complete fix finished!"
echo ""
echo "⚠️  CRITICAL NEXT STEPS:"
echo "  1. Restart PHP-FPM:"
echo "     sudo systemctl restart php8.4-fpm"
echo ""
echo "  2. Test Livewire JS:"
echo "     curl -I https://wisuda.usbypkp.ac.id/livewire/livewire.js"
echo "     (Should return HTTP/2 200 with large content-length)"
echo ""
echo "  3. Clear browser cache and test login"
echo ""
echo "If Livewire JS is still broken, run:"
echo "  composer require livewire/livewire --no-interaction"
echo "  php artisan config:clear"
echo "  sudo systemctl restart php8.4-fpm"
echo ""
