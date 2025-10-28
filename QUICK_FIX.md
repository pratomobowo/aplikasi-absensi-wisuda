# Quick Fix - Livewire JavaScript 404 Error

## Problem
Livewire JavaScript file returns 0 bytes, causing login form to not work.

## Root Cause
Based on debug output:
- Internal test: Status 200, Content-Length: **0 bytes** ← Livewire JS is empty!
- External test: HTTP 404, content-length: 347518 ← Error page

**CRITICAL:** The issue is caused by incorrect `.env` configuration:
- `APP_ENV=local` (should be `production`)
- `APP_DEBUG=true` (should be `false`)
- `SESSION_ENCRYPT=true` (should be `false`) ← **THIS BREAKS LIVEWIRE!**

## Solution

### STEP 1: Fix .env Configuration (MOST IMPORTANT!)

```bash
cd /www/wwwroot/wisuda.usbypkp.ac.id/aplikasi-absensi-wisuda

# Use script to fix .env automatically
bash scripts/fix-env-production.sh

# OR manually edit .env
nano .env
# Change these lines:
# APP_ENV=production
# APP_DEBUG=false
# SESSION_ENCRYPT=false
```

### STEP 2: Clear and Rebuild Caches

```bash
# Clear everything
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Rebuild
php artisan config:cache
php artisan view:cache

# Restart PHP-FPM
sudo systemctl restart php8.4-fpm
```

### STEP 3: Test

```bash
# Test Livewire JS
curl -I https://wisuda.usbypkp.ac.id/livewire/livewire.js
# Should return: HTTP/2 200 with content-length > 100000
```

## Verify Fix

```bash
# Test internal
php artisan tinker --execute="
\$request = Request::create('/livewire/livewire.js', 'GET');
\$response = app()->handle(\$request);
echo 'Size: ' . strlen(\$response->getContent()) . ' bytes' . PHP_EOL;
"

# Should show: Size: > 100000 bytes (not 0!)
```

## Alternative: Use Script

```bash
bash scripts/fix-livewire-assets.sh
sudo systemctl restart php8.4-fpm
```

## If Still Not Working

### Check 1: Livewire Version
```bash
composer show livewire/livewire
# Should be v3.6.x or higher
```

### Check 2: PHP Version
```bash
php -v
# Should be PHP 8.2 or higher
```

### Check 3: File Permissions
```bash
ls -la vendor/livewire/livewire/src/Mechanisms/FrontendAssets/
# Should be readable by www user
```

### Check 4: Nginx Config
Ensure nginx is passing requests to PHP-FPM correctly:

```nginx
location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    include fastcgi_params;
}
```

### Check 5: Route Cache Issue
If you cached routes, clear it:
```bash
php artisan route:clear
# DO NOT run route:cache with Livewire!
```

## Why This Happens

**SESSION_ENCRYPT=true breaks Livewire** because:
1. Livewire stores component state in session
2. When session is encrypted, Livewire can't properly serialize/deserialize component data
3. This causes Livewire to fail generating JavaScript assets
4. Result: livewire.js returns 0 bytes

Other contributing factors:
- `APP_ENV=local` in production causes Laravel to use development configurations
- `APP_DEBUG=true` exposes sensitive information and affects performance
- Composer autoload outdated
- PHP-FPM cache is stale

## Prevention

After every deployment:
1. Run `composer dump-autoload -o`
2. Run `php artisan optimize:clear`
3. Restart PHP-FPM
4. **DO NOT** cache routes if using Livewire with closures

## Test Login After Fix

1. Clear browser cache (Ctrl+Shift+Delete)
2. Open incognito window
3. Go to https://wisuda.usbypkp.ac.id/admin/login
4. Open browser console (F12)
5. Check Network tab - livewire.js should return 200 OK with content
6. Try to login

## Contact

If issue persists after all steps, provide:
- Output of `bash scripts/debug-livewire.sh`
- Output of `composer show livewire/livewire`
- Output of `php -v`
- Screenshot of browser console errors
