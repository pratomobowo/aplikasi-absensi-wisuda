# Production Deployment Checklist

## 🚨 CRITICAL: Before You Start

**Current Issues Identified:**
1. ❌ `APP_ENV=local` (must be `production`)
2. ❌ `APP_DEBUG=true` (must be `false`)
3. ❌ `SESSION_ENCRYPT=true` (must be `false` - breaks Livewire!)
4. ❌ Livewire JavaScript returns 404
5. ❌ CSP blocking fonts.bunny.net

## ✅ Quick Fix (Run This First!)

```bash
cd /www/wwwroot/wisuda.usbypkp.ac.id/aplikasi-absensi-wisuda

# Run complete fix script
bash scripts/complete-fix.sh

# Restart PHP-FPM
sudo systemctl restart php8.4-fpm

# Test Livewire
curl -I https://wisuda.usbypkp.ac.id/livewire/livewire.js
```

Expected result: `HTTP/2 200` with `content-length: 100000+`

## 📋 Manual Steps (If Script Fails)

### 1. Fix .env Configuration

Edit `.env`:
```bash
nano .env
```

Change these lines:
```env
APP_ENV=production          # NOT local!
APP_DEBUG=false             # NOT true!
SESSION_ENCRYPT=false       # NOT true! (This breaks Livewire)
SESSION_SECURE_COOKIE=true  # Must be true for HTTPS
APP_URL=https://wisuda.usbypkp.ac.id  # Must match domain exactly
```

### 2. Clear All Caches

```bash
php artisan optimize:clear
# OR manually:
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
php artisan clear-compiled
```

### 3. Optimize Composer

```bash
composer dump-autoload -o
```

### 4. Rebuild Caches

```bash
php artisan config:cache
php artisan view:cache
# DO NOT cache routes if you have closures in routes/web.php
```

### 5. Restart PHP-FPM

```bash
sudo systemctl restart php8.4-fpm
```

### 6. Test Livewire

```bash
# Test internal
php artisan tinker --execute="
\$request = Request::create('/livewire/livewire.js', 'GET');
\$response = app()->handle(\$request);
echo 'Size: ' . strlen(\$response->getContent()) . ' bytes' . PHP_EOL;
"

# Test external
curl -I https://wisuda.usbypkp.ac.id/livewire/livewire.js
```

Should return:
- Status: 200
- Content-Length: > 100000 bytes

## 🔍 Verification Checklist

After running fixes, verify:

- [ ] `.env` has `APP_ENV=production`
- [ ] `.env` has `APP_DEBUG=false`
- [ ] `.env` has `SESSION_ENCRYPT=false`
- [ ] `.env` has `APP_URL=https://wisuda.usbypkp.ac.id`
- [ ] Livewire JS returns 200 (not 404)
- [ ] Livewire JS size > 100KB
- [ ] PHP-FPM restarted
- [ ] Browser cache cleared
- [ ] Login page loads without console errors
- [ ] Login form submits successfully

## 🐛 If Still Not Working

### Issue: Livewire JS Still 404

```bash
# Reinstall Livewire
composer require livewire/livewire --no-interaction

# Clear everything
php artisan optimize:clear

# Rebuild
php artisan config:cache

# Restart
sudo systemctl restart php8.4-fpm
```

### Issue: Livewire JS Returns 0 Bytes

This means `SESSION_ENCRYPT=true` is still set. Fix:

```bash
# Check current value
grep SESSION_ENCRYPT .env

# If it shows "true", change to "false"
sed -i 's/^SESSION_ENCRYPT=.*/SESSION_ENCRYPT=false/' .env

# Clear config
php artisan config:clear
php artisan config:cache

# Restart
sudo systemctl restart php8.4-fpm
```

### Issue: CSP Errors in Browser Console

Already fixed in `app/Http/Middleware/SecurityHeaders.php` to allow:
- `fonts.bunny.net` for fonts
- `fonts.googleapis.com` for fonts

After updating code:
```bash
git pull origin main
php artisan config:clear
sudo systemctl restart php8.4-fpm
```

### Issue: CSRF Token Mismatch

```bash
# Ensure APP_URL matches domain
grep APP_URL .env
# Should be: APP_URL=https://wisuda.usbypkp.ac.id

# Clear sessions
php artisan session:clear

# Restart
sudo systemctl restart php8.4-fpm
```

### Issue: npm Permission Denied

If you get "Permission denied" when running `npm run build`:

```bash
# Fix permissions
bash scripts/fix-permissions.sh

# Or manually:
sudo chown -R $USER:$USER node_modules
chmod -R 755 node_modules/.bin

# Then run build WITHOUT sudo
npm run build
```

**Important:** Never use `sudo npm install` or `sudo npm run build`!

## 📊 Monitoring

After deployment, monitor:

```bash
# Watch Laravel logs
tail -f storage/logs/laravel.log

# Watch Nginx logs
tail -f /var/log/nginx/error.log

# Watch PHP-FPM logs
tail -f /var/log/php8.4-fpm.log
```

## 🎯 Success Criteria

Login is working when:
1. ✅ No 404 errors in browser console
2. ✅ No CSP errors in browser console
3. ✅ Livewire JS loads (check Network tab)
4. ✅ Login form is interactive
5. ✅ Can submit credentials
6. ✅ Redirects to dashboard after login

## 📞 Support

If all steps fail, provide:
1. Output of `bash scripts/debug-livewire.sh`
2. Output of `grep -E "APP_ENV|APP_DEBUG|SESSION_ENCRYPT|APP_URL" .env`
3. Output of `curl -I https://wisuda.usbypkp.ac.id/livewire/livewire.js`
4. Screenshot of browser console (F12)
5. Content of `storage/logs/laravel.log` (last 50 lines)

## 🔄 Regular Maintenance

After every deployment:
```bash
bash scripts/complete-fix.sh
sudo systemctl restart php8.4-fpm
```

Or use the full deployment script:
```bash
bash scripts/deploy.sh
```
