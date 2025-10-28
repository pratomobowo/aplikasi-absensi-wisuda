# Troubleshooting Guide - Login Issues

## Error: "Method Not Allowed" pada POST /admin/login

### Quick Fix (Jalankan di Production)

```bash
# 1. Run fix script
bash scripts/fix-login-issue.sh

# 2. Restart PHP-FPM
sudo systemctl restart php8.4-fpm

# 3. Test login dengan incognito mode
```

### Checklist Manual

- [ ] **Clear all caches**
  ```bash
  php artisan route:clear
  php artisan config:clear
  php artisan cache:clear
  php artisan view:clear
  ```

- [ ] **Verify .env settings**
  ```bash
  grep "APP_URL\|SESSION_" .env
  ```
  Harus ada:
  - `APP_URL=https://wisuda.usbypkp.ac.id`
  - `SESSION_DRIVER=database`
  - `SESSION_ENCRYPT=false`
  - `SESSION_SECURE_COOKIE=true`

- [ ] **Check sessions table exists**
  ```bash
  php artisan migrate --force
  ```

- [ ] **Verify Livewire routes**
  ```bash
  php artisan route:list --path=livewire
  ```
  Harus ada: `POST /livewire/update`

- [ ] **Check storage permissions**
  ```bash
  ls -la storage/logs
  ls -la bootstrap/cache
  ```
  Harus writable oleh web server

- [ ] **Rebuild caches**
  ```bash
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  ```

- [ ] **Restart PHP-FPM**
  ```bash
  sudo systemctl restart php8.4-fpm
  ```

- [ ] **Clear browser cache**
  - Atau test dengan incognito/private mode
  - Atau test dengan browser berbeda

- [ ] **Check error logs**
  ```bash
  tail -f storage/logs/laravel.log
  ```

### Root Cause Analysis

Filament v3 menggunakan **Livewire** untuk authentication. Login form seharusnya:
1. Load halaman GET `/admin/login`
2. Submit form via Livewire ke POST `/livewire/update`
3. Livewire menangani authentication

Jika error "Method Not Allowed" muncul, berarti:
- Form mencoba POST langsung ke `/admin/login` (salah)
- Livewire JavaScript tidak ter-load
- CSRF token tidak valid
- Session tidak tersimpan

### Common Issues

#### 1. Cached Routes
**Symptom:** Routes tidak update setelah deploy
**Fix:** 
```bash
php artisan route:clear
php artisan route:cache
```

#### 2. Session Issues with Cloudflare
**Symptom:** Login form tidak submit dengan benar
**Fix:** Set di `.env`:
```env
SESSION_ENCRYPT=false
SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=null
```

#### 3. CSRF Token Mismatch
**Symptom:** 419 error atau form tidak submit
**Fix:**
- Pastikan `APP_URL` benar di `.env`
- Clear browser cookies
- Verify TrustProxies middleware aktif

#### 4. Livewire Assets Not Loading
**Symptom:** Form tidak interactive, no JavaScript errors
**Fix:**
```bash
npm run build
php artisan filament:optimize
php artisan view:clear
```

#### 5. OPcache Issues
**Symptom:** Changes tidak apply setelah deploy
**Fix:**
```bash
sudo systemctl restart php8.4-fpm
```

### Testing Steps

1. **Test dengan curl:**
   ```bash
   # Test GET login page
   curl -I https://wisuda.usbypkp.ac.id/admin/login
   
   # Should return 200 OK
   ```

2. **Check Livewire endpoint:**
   ```bash
   curl -X POST https://wisuda.usbypkp.ac.id/livewire/update \
     -H "Content-Type: application/json" \
     -d '{"components":[]}'
   
   # Should not return 404
   ```

3. **Verify routes in production:**
   ```bash
   php artisan route:list | grep -E "admin/login|livewire"
   ```

### Still Not Working?

1. Check web server error logs:
   ```bash
   # Nginx
   tail -f /var/log/nginx/error.log
   
   # Apache
   tail -f /var/log/apache2/error.log
   ```

2. Enable debug mode temporarily:
   ```env
   APP_DEBUG=true
   ```
   (Don't forget to disable after troubleshooting!)

3. Check PHP-FPM logs:
   ```bash
   tail -f /var/log/php8.4-fpm.log
   ```

4. Verify Cloudflare settings:
   - SSL/TLS: Full (strict)
   - No page rules blocking POST
   - No firewall rules blocking requests

### Contact Support

If issue persists, provide:
- Laravel version: `php artisan --version`
- PHP version: `php -v`
- Error from `storage/logs/laravel.log`
- Browser console errors (F12)
- Network tab showing failed request
