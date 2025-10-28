# Cloudflare Configuration for Laravel Application

## Overview

Aplikasi ini berjalan di belakang Cloudflare CDN. Beberapa konfigurasi khusus diperlukan agar Laravel dan Filament bekerja dengan baik.

## Required Cloudflare Settings

### 1. SSL/TLS Configuration

**Path:** SSL/TLS → Overview

- **SSL/TLS encryption mode:** Full (strict) atau Full
- **Always Use HTTPS:** ON
- **Automatic HTTPS Rewrites:** ON

### 2. Page Rules

**Path:** Rules → Page Rules

Pastikan tidak ada page rules yang:
- Block POST requests
- Block `/admin/*` paths
- Block `/livewire/*` paths

### 3. Firewall Rules

**Path:** Security → WAF

Jika ada custom firewall rules, pastikan:
- Allow POST requests ke `/livewire/update`
- Allow POST requests ke `/admin/*`
- Don't block legitimate traffic from Indonesia

### 4. Speed Settings

**Path:** Speed → Optimization

Recommended settings:
- **Auto Minify:** HTML, CSS, JS (ON)
- **Brotli:** ON
- **Rocket Loader:** OFF (dapat mengganggu Livewire)

### 5. Caching

**Path:** Caching → Configuration

- **Caching Level:** Standard
- **Browser Cache TTL:** Respect Existing Headers

**Important:** Add page rules to bypass cache for admin:
```
URL: wisuda.usbypkp.ac.id/admin/*
Cache Level: Bypass
```

```
URL: wisuda.usbypkp.ac.id/livewire/*
Cache Level: Bypass
```

## Laravel Configuration for Cloudflare

### 1. Trust Proxies

File: `bootstrap/app.php`

```php
->withMiddleware(function (Middleware $middleware): void {
    // Trust Cloudflare proxies
    $middleware->trustProxies(at: '*');
    // ... other middleware
})
```

### 2. Environment Variables

File: `.env`

```env
# Application
APP_URL=https://wisuda.usbypkp.ac.id

# Session (Important for Cloudflare)
SESSION_DRIVER=database
SESSION_ENCRYPT=false
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
SESSION_DOMAIN=null
```

**Why these settings?**
- `SESSION_ENCRYPT=false`: Prevents issues with Livewire session handling
- `SESSION_SECURE_COOKIE=true`: Required for HTTPS
- `SESSION_SAME_SITE=lax`: Allows cookies to work with Cloudflare
- `SESSION_DOMAIN=null`: Let Laravel auto-detect domain

## Troubleshooting Cloudflare Issues

### Issue: Login Not Working

**Symptoms:**
- Login form doesn't submit
- "Method Not Allowed" error
- CSRF token mismatch

**Solutions:**

1. **Check Cloudflare Page Rules:**
   - Ensure `/admin/*` and `/livewire/*` bypass cache
   - No firewall rules blocking POST requests

2. **Verify SSL/TLS Mode:**
   - Must be "Full" or "Full (strict)"
   - Not "Flexible" (causes redirect loops)

3. **Check Browser:**
   - Clear cookies and cache
   - Test in incognito mode
   - Check browser console for errors

### Issue: Assets Not Loading

**Symptoms:**
- CSS/JS files return 404
- Livewire not working
- No JavaScript functionality

**Solutions:**

1. **Build assets:**
   ```bash
   npm run build
   ```

2. **Clear Cloudflare cache:**
   - Go to Cloudflare Dashboard
   - Caching → Configuration
   - Purge Everything

3. **Verify asset URLs:**
   - Check `APP_URL` in `.env`
   - Ensure `mix-manifest.json` or `vite-manifest.json` exists

### Issue: Slow Admin Panel

**Symptoms:**
- Admin panel loads slowly
- Multiple requests to Cloudflare

**Solutions:**

1. **Bypass cache for admin:**
   ```
   Page Rule: wisuda.usbypkp.ac.id/admin/*
   Cache Level: Bypass
   ```

2. **Disable Rocket Loader:**
   - Speed → Optimization
   - Rocket Loader: OFF

### Issue: CSRF Token Mismatch

**Symptoms:**
- 419 error on form submit
- "CSRF token mismatch" error

**Solutions:**

1. **Verify APP_URL:**
   ```bash
   grep APP_URL .env
   # Should be: APP_URL=https://wisuda.usbypkp.ac.id
   ```

2. **Clear application cache:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

3. **Check session configuration:**
   ```bash
   grep SESSION_ .env
   ```

## Cloudflare Headers

Cloudflare adds these headers to requests:

- `CF-Connecting-IP`: Real visitor IP
- `CF-Ray`: Request ID for debugging
- `CF-Visitor`: Original protocol (http/https)
- `CF-IPCountry`: Visitor country code

Laravel's TrustProxies middleware handles these automatically.

## Testing Cloudflare Setup

### 1. Test SSL/TLS

```bash
curl -I https://wisuda.usbypkp.ac.id
```

Should return:
- `HTTP/2 200`
- `cf-ray: ...` (Cloudflare header)
- `strict-transport-security: ...` (HSTS header)

### 2. Test Admin Login

```bash
# Get login page
curl -I https://wisuda.usbypkp.ac.id/admin/login

# Should return 200 OK
```

### 3. Test Livewire Endpoint

```bash
curl -I https://wisuda.usbypkp.ac.id/livewire/update

# Should return 405 (Method Not Allowed) for GET
# This is correct - it only accepts POST
```

### 4. Check Real IP

In Laravel logs, verify that real IP is logged (not Cloudflare IP):

```bash
tail -f storage/logs/laravel.log
```

Should show visitor's real IP, not Cloudflare's IP range.

## Performance Optimization

### 1. Enable Cloudflare Caching for Static Assets

Page Rule:
```
URL: wisuda.usbypkp.ac.id/build/*
Cache Level: Cache Everything
Edge Cache TTL: 1 month
```

### 2. Enable Argo Smart Routing (Optional, Paid)

Improves routing between Cloudflare and origin server.

### 3. Enable Cloudflare Workers (Optional)

For advanced caching and edge computing.

## Security Recommendations

### 1. Enable Bot Fight Mode

**Path:** Security → Bots

Protects against automated attacks.

### 2. Enable DDoS Protection

**Path:** Security → DDoS

Cloudflare provides automatic DDoS protection.

### 3. Configure Rate Limiting

**Path:** Security → WAF → Rate limiting rules

Example rule:
```
If: (http.request.uri.path contains "/admin/login")
Then: Rate limit (10 requests per minute)
```

### 4. Enable Security Level

**Path:** Security → Settings

Set to "Medium" or "High" for better protection.

## Monitoring

### 1. Cloudflare Analytics

**Path:** Analytics & Logs

Monitor:
- Traffic patterns
- Threats blocked
- Cache hit ratio
- Response times

### 2. Laravel Logs

```bash
tail -f storage/logs/laravel.log
```

### 3. Web Server Logs

```bash
tail -f /var/log/nginx/access.log
tail -f /var/log/nginx/error.log
```

## Support

If issues persist:

1. Check Cloudflare Status: https://www.cloudflarestatus.com/
2. Review Cloudflare Firewall Events
3. Check Laravel logs: `storage/logs/laravel.log`
4. Contact Cloudflare Support with CF-Ray ID from error page
