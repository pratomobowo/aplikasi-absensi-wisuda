# Deployment Guide

## Production Deployment Checklist

### 1. Verify Production Setup
```bash
bash scripts/verify-production.sh
```

### 2. Clear All Caches
Setelah deploy ke production, **WAJIB** jalankan script ini:

```bash
bash scripts/clear-production-cache.sh
```

Atau manual:
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan clear-compiled

# Kemudian optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. Restart PHP-FPM (jika menggunakan OPcache)
```bash
sudo systemctl restart php8.4-fpm
```

### 4. Environment Variables
Pastikan `.env` di production sudah benar:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://wisuda.usbypkp.ac.id

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Session (PENTING untuk Cloudflare)
SESSION_DRIVER=database
SESSION_ENCRYPT=false
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
SESSION_DOMAIN=null
```

**PENTING:** 
- `SESSION_ENCRYPT=false` - Jangan encrypt session karena bisa menyebabkan masalah dengan Livewire
- `SESSION_SECURE_COOKIE=true` - Wajib untuk HTTPS
- `SESSION_DOMAIN=null` - Biarkan null atau set ke domain tanpa subdomain
- `APP_URL` harus sama persis dengan domain production (termasuk https://)

### 5. File Permissions
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## Troubleshooting

### Issue: CSRF Token Mismatch

**Solusi:**
1. Pastikan `APP_URL` di `.env` sesuai dengan domain production
2. Clear session:
   ```bash
   php artisan session:clear
   ```
3. Pastikan cookie domain dikonfigurasi dengan benar di `config/session.php`

### Issue: 500 Internal Server Error

**Solusi:**
1. Check error logs:
   ```bash
   tail -f storage/logs/laravel.log
   ```
2. Pastikan storage writable:
   ```bash
   chmod -R 755 storage
   ```

## Cloudflare Configuration

Jika menggunakan Cloudflare:

1. **SSL/TLS Mode**: Full (strict) atau Full
2. **Always Use HTTPS**: Enabled
3. **Automatic HTTPS Rewrites**: Enabled
4. **Page Rules**: Pastikan tidak ada rule yang block POST requests

## Post-Deployment Verification

Setelah deploy, test:
- [ ] Homepage loading
- [ ] Admin login (GET /admin/login)
- [ ] Admin login submit (POST /admin/login)
- [ ] Scanner login
- [ ] QR code scanning
- [ ] PDF generation
- [ ] Database connectivity

## Rollback Procedure

Jika terjadi masalah:

```bash
# 1. Rollback ke commit sebelumnya
git reset --hard HEAD~1

# 2. Clear caches
bash scripts/clear-production-cache.sh

# 3. Restart services
sudo systemctl restart php8.4-fpm
```
