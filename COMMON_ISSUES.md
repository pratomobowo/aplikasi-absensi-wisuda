# Common Production Issues - Quick Reference

## 🚨 Issue: Login Not Working (404 on livewire.js)

**Symptoms:**
- Browser console shows: `GET /livewire/livewire.js 404`
- Login form doesn't submit
- No JavaScript errors except 404

**Root Cause:**
- `SESSION_ENCRYPT=true` in `.env` (breaks Livewire)
- `APP_ENV=local` instead of `production`
- Stale cache

**Quick Fix:**
```bash
bash scripts/complete-fix.sh
sudo systemctl restart php8.4-fpm
```

**Manual Fix:**
```bash
# Edit .env
nano .env
# Change: SESSION_ENCRYPT=false, APP_ENV=production, APP_DEBUG=false

# Clear cache
php artisan optimize:clear
php artisan config:cache

# Restart
sudo systemctl restart php8.4-fpm
```

---

## 🚨 Issue: npm Permission Denied

**Symptoms:**
```
sh: line 1: node_modules/.bin/vite: Permission denied
```

**Root Cause:**
- Ran `npm install` with sudo
- Wrong file ownership

**Quick Fix:**
```bash
bash scripts/fix-permissions.sh
npm run build
```

**Manual Fix:**
```bash
sudo chown -R $USER:$USER node_modules
chmod -R 755 node_modules/.bin
npm run build  # WITHOUT sudo!
```

**Prevention:**
- Never use `sudo npm install`
- Never use `sudo npm run build`

---

## 🚨 Issue: CSP Blocking Fonts

**Symptoms:**
```
Refused to load stylesheet 'https://fonts.bunny.net/...' 
because it violates Content Security Policy
```

**Root Cause:**
- CSP too restrictive in `SecurityHeaders.php`

**Fix:**
Already fixed in latest code. Pull and restart:
```bash
git pull origin main
php artisan config:clear
sudo systemctl restart php8.4-fpm
```

---

## 🚨 Issue: 500 Internal Server Error

**Symptoms:**
- White page with "500 Internal Server Error"
- No details shown

**Debug:**
```bash
# Check Laravel logs
tail -50 storage/logs/laravel.log

# Check PHP-FPM logs
sudo tail -50 /var/log/php8.4-fpm.log

# Check Nginx logs
sudo tail -50 /var/log/nginx/error.log
```

**Common Causes:**
1. **APP_KEY not set:**
   ```bash
   php artisan key:generate
   ```

2. **Storage not writable:**
   ```bash
   chmod -R 755 storage bootstrap/cache
   sudo chown -R www:www storage bootstrap/cache
   ```

3. **Database connection failed:**
   ```bash
   # Check .env database settings
   php artisan db:show
   ```

---

## 🚨 Issue: CSRF Token Mismatch (419)

**Symptoms:**
- Form submit returns 419 error
- "CSRF token mismatch" in logs

**Fix:**
```bash
# 1. Check APP_URL matches domain
grep APP_URL .env
# Must be: APP_URL=https://wisuda.usbypkp.ac.id

# 2. Clear config
php artisan config:clear
php artisan config:cache

# 3. Clear browser cookies
# 4. Test in incognito mode
```

---

## 🚨 Issue: Assets Not Loading (404)

**Symptoms:**
- CSS/JS files return 404
- Page looks broken

**Fix:**
```bash
# 1. Build assets
npm run build

# 2. Check public/build exists
ls -la public/build/

# 3. Clear cache
php artisan view:clear

# 4. Check APP_URL
grep APP_URL .env
```

---

## 🚨 Issue: Database Migration Failed

**Symptoms:**
```
SQLSTATE[HY000] [2002] Connection refused
```

**Fix:**
```bash
# 1. Check database is running
sudo systemctl status mysql

# 2. Check .env credentials
grep DB_ .env

# 3. Test connection
php artisan db:show

# 4. If correct, run migrations
php artisan migrate --force
```

---

## 🚨 Issue: Composer Install Failed

**Symptoms:**
```
Your requirements could not be resolved...
```

**Fix:**
```bash
# 1. Clear Composer cache
composer clear-cache

# 2. Remove vendor and lock
rm -rf vendor composer.lock

# 3. Reinstall
composer install --no-dev --optimize-autoloader
```

---

## 🚨 Issue: PHP Version Mismatch

**Symptoms:**
```
Parse error: syntax error, unexpected '?'...
```

**Fix:**
```bash
# Check PHP version
php -v
# Must be PHP 8.2 or higher

# If wrong version, update or use specific version
php8.4 artisan optimize
```

---

## 🚨 Issue: Storage Link Broken

**Symptoms:**
- Uploaded files return 404
- Images not showing

**Fix:**
```bash
# Create storage link
php artisan storage:link

# Check link exists
ls -la public/storage
```

---

## 📞 Getting Help

If none of these fixes work:

1. **Run debug script:**
   ```bash
   bash scripts/debug-livewire.sh > debug-output.txt
   ```

2. **Collect information:**
   - Output of debug script
   - Last 50 lines of `storage/logs/laravel.log`
   - Browser console screenshot (F12)
   - Network tab showing failed requests

3. **Check documentation:**
   - [PRODUCTION_CHECKLIST.md](PRODUCTION_CHECKLIST.md)
   - [TROUBLESHOOTING.md](TROUBLESHOOTING.md)
   - [QUICK_FIX.md](QUICK_FIX.md)

---

## 🔄 Preventive Maintenance

Run after every deployment:
```bash
bash scripts/complete-fix.sh
sudo systemctl restart php8.4-fpm
```

Or use full deployment:
```bash
bash scripts/deploy.sh
```

---

## ✅ Health Check Commands

```bash
# Check application status
php artisan about

# Check routes
php artisan route:list

# Check database
php artisan db:show

# Check queue
php artisan queue:work --once

# Check storage
df -h storage/

# Check permissions
ls -la storage/ bootstrap/cache/
```
