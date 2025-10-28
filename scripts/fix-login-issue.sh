#!/bin/bash

# Quick Fix for Login Issue in Production
# This script addresses the "Method Not Allowed" error on admin login

set -e

echo "🔧 Fixing login issue..."
echo ""

# 1. Clear all caches
echo "Step 1: Clearing all caches..."
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan clear-compiled
echo "✓ Caches cleared"
echo ""

# 1.5. Publish Livewire assets
echo "Step 1.5: Publishing Livewire assets..."
php artisan livewire:publish --force
echo "✓ Livewire assets published"
echo ""

# 2. Ensure sessions table exists
echo "Step 2: Checking sessions table..."
php artisan session:table 2>/dev/null || echo "Sessions migration already exists"
php artisan migrate --force
echo "✓ Sessions table ready"
echo ""

# 3. Rebuild caches
echo "Step 3: Rebuilding caches..."
php artisan config:cache
php artisan view:cache
php artisan route:cache
echo "✓ Caches rebuilt"
echo ""

# 4. Fix permissions
echo "Step 4: Fixing permissions..."
chmod -R 755 storage bootstrap/cache
echo "✓ Permissions fixed"
echo ""

# 5. Verify routes
echo "Step 5: Verifying routes..."
echo ""
echo "Admin login route:"
php artisan route:list --path=admin/login
echo ""
echo "Livewire update route:"
php artisan route:list --path=livewire/update
echo ""

echo "✅ Fix completed!"
echo ""
echo "⚠️  IMPORTANT: You must now:"
echo "  1. Restart PHP-FPM: sudo systemctl restart php8.4-fpm"
echo "  2. Clear browser cache or test in incognito mode"
echo "  3. Verify .env has correct settings:"
echo "     - APP_URL=https://wisuda.usbypkp.ac.id"
echo "     - SESSION_SECURE_COOKIE=true"
echo "     - SESSION_ENCRYPT=false"
echo ""
