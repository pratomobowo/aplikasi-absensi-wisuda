#!/bin/bash

# Debug Livewire Issue Script
# Run this on production to diagnose why livewire.js returns 404

echo "🔍 Debugging Livewire Issue..."
echo ""

# 1. Check APP_URL
echo "📌 Step 1: Check APP_URL"
echo "APP_URL from .env:"
grep "^APP_URL=" .env || echo "⚠️  APP_URL not set!"
echo ""

# 2. Check if Livewire is installed
echo "📌 Step 2: Check Livewire installation"
if [ -d "vendor/livewire/livewire" ]; then
    echo "✓ Livewire package installed"
    composer show livewire/livewire | grep "versions"
else
    echo "✗ Livewire package NOT installed!"
fi
echo ""

# 3. Check Livewire routes
echo "📌 Step 3: Check Livewire routes"
php artisan route:list --path=livewire
echo ""

# 4. Test Livewire route directly
echo "📌 Step 4: Test Livewire JavaScript route"
php artisan tinker --execute="
\$request = Request::create('/livewire/livewire.js', 'GET');
try {
    \$response = app()->handle(\$request);
    echo 'Status: ' . \$response->getStatusCode() . PHP_EOL;
    echo 'Content-Type: ' . \$response->headers->get('Content-Type') . PHP_EOL;
    echo 'Content-Length: ' . strlen(\$response->getContent()) . ' bytes' . PHP_EOL;
} catch (Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
}
"
echo ""

# 5. Check if route cache exists
echo "📌 Step 5: Check route cache"
if [ -f "bootstrap/cache/routes-v7.php" ]; then
    echo "✓ Routes cached"
    echo "Checking if Livewire routes are in cache..."
    grep -c "livewire" bootstrap/cache/routes-v7.php && echo "✓ Livewire routes found in cache" || echo "✗ Livewire routes NOT in cache!"
else
    echo "⚠️  Routes not cached"
fi
echo ""

# 6. Check .htaccess
echo "📌 Step 6: Check .htaccess"
if [ -f "public/.htaccess" ]; then
    echo "✓ .htaccess exists"
    echo "Checking for RewriteEngine..."
    grep "RewriteEngine On" public/.htaccess && echo "✓ RewriteEngine is ON" || echo "✗ RewriteEngine is OFF!"
else
    echo "✗ .htaccess NOT found!"
fi
echo ""

# 7. Check public directory structure
echo "📌 Step 7: Check public directory"
ls -la public/ | grep -E "index.php|.htaccess"
echo ""

# 8. Check web server
echo "📌 Step 8: Detect web server"
if command -v nginx &> /dev/null; then
    echo "✓ Nginx detected"
    echo "Nginx config location: /etc/nginx/sites-available/"
elif command -v apache2 &> /dev/null; then
    echo "✓ Apache detected"
    echo "Apache config location: /etc/apache2/sites-available/"
else
    echo "⚠️  Web server not detected"
fi
echo ""

# 9. Test actual HTTP request
echo "📌 Step 9: Test HTTP request to Livewire JS"
APP_URL=$(grep "^APP_URL=" .env | cut -d '=' -f2)
if [ ! -z "$APP_URL" ]; then
    echo "Testing: $APP_URL/livewire/livewire.js"
    curl -I "$APP_URL/livewire/livewire.js" 2>/dev/null | head -5
else
    echo "⚠️  Cannot test - APP_URL not set"
fi
echo ""

# 10. Check Livewire config
echo "📌 Step 10: Check Livewire configuration"
php artisan tinker --execute="
echo 'Asset URL: ' . config('livewire.asset_url') . PHP_EOL;
echo 'Update Route: ' . config('livewire.update_route') . PHP_EOL;
echo 'Inject Assets: ' . (config('livewire.inject_assets') ? 'true' : 'false') . PHP_EOL;
"
echo ""

echo "✅ Debug complete!"
echo ""
echo "Common issues:"
echo "  1. APP_URL mismatch - Check if APP_URL matches your domain"
echo "  2. Route cache outdated - Run: php artisan route:clear && php artisan route:cache"
echo "  3. Web server config - Check nginx/apache config for /livewire/* path"
echo "  4. Cloudflare caching - Ensure /livewire/* bypasses cache"
echo ""
