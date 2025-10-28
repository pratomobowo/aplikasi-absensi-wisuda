#!/bin/bash

# Fix Livewire Assets Issue
# This fixes the issue where livewire.js returns 0 bytes

set -e

echo "🔧 Fixing Livewire assets issue..."
echo ""

# 1. Clear all caches first
echo "Step 1: Clearing all caches..."
php artisan optimize:clear
echo "✓ All caches cleared"
echo ""

# 2. Reinstall Livewire
echo "Step 2: Reinstalling Livewire..."
composer require livewire/livewire --no-interaction
echo "✓ Livewire reinstalled"
echo ""

# 3. Clear vendor cache
echo "Step 3: Clearing Composer cache..."
composer dump-autoload -o
echo "✓ Autoload optimized"
echo ""

# 4. Rebuild all caches
echo "Step 4: Rebuilding caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✓ Caches rebuilt"
echo ""

# 5. Test Livewire JS
echo "Step 5: Testing Livewire JavaScript..."
php artisan tinker --execute="
\$request = Request::create('/livewire/livewire.js', 'GET');
\$response = app()->handle(\$request);
\$size = strlen(\$response->getContent());
echo 'Status: ' . \$response->getStatusCode() . PHP_EOL;
echo 'Content-Length: ' . \$size . ' bytes' . PHP_EOL;
if (\$size > 0) {
    echo '✓ Livewire JS is working!' . PHP_EOL;
} else {
    echo '✗ Livewire JS is still empty!' . PHP_EOL;
}
"
echo ""

echo "✅ Fix completed!"
echo ""
echo "⚠️  Next steps:"
echo "  1. Restart PHP-FPM: sudo systemctl restart php8.4-fpm"
echo "  2. Clear browser cache"
echo "  3. Test login again"
echo ""
