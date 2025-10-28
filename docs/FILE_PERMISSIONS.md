# File Permissions and Ownership Guide

## Overview

Proper file permissions are critical for Laravel applications. Wrong permissions can cause:
- npm install/build failures
- Storage write errors
- Cache errors
- Security vulnerabilities

## Recommended Setup

### Directory Ownership

```
Project Root: deployment_user:deployment_user (e.g., bowo:bowo)
├── storage/: www:www (web server user)
└── bootstrap/cache/: www:www (web server user)
```

### Directory Permissions

```
Project Root: 755
├── storage/: 775
├── bootstrap/cache/: 775
├── node_modules/: 755
└── vendor/: 755
```

## Quick Setup

```bash
# Run this script to fix all permissions
bash scripts/fix-permissions.sh
```

## Manual Setup

### Step 1: Fix Project Ownership

```bash
# Replace 'bowo' with your deployment user
sudo chown -R bowo:bowo /www/wwwroot/wisuda.usbypkp.ac.id/aplikasi-absensi-wisuda
```

### Step 2: Fix Storage Ownership

```bash
# Replace 'www' with your web server user (www-data, nginx, etc.)
sudo chown -R www:www storage
sudo chown -R www:www bootstrap/cache
```

### Step 3: Set Correct Permissions

```bash
# Project files
chmod -R 755 .

# Storage and cache (need write access)
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## Web Server Users by Platform

| Platform | User | Group |
|----------|------|-------|
| Ubuntu/Debian | www-data | www-data |
| CentOS/RHEL | nginx or apache | nginx or apache |
| BT Panel | www | www |
| macOS | _www | _www |

## Checking Current Ownership

```bash
# Check project root
ls -la

# Check storage
ls -la storage/

# Check specific file
stat -c '%U:%G' storage/logs/laravel.log
```

## Common Permission Issues

### Issue 1: npm install fails with EACCES

**Cause:** Project owned by root or www user

**Fix:**
```bash
sudo chown -R $USER:$USER .
sudo chown -R www:www storage bootstrap/cache
```

### Issue 2: Laravel can't write to storage

**Cause:** storage/ not writable by web server

**Fix:**
```bash
sudo chown -R www:www storage
sudo chmod -R 775 storage
```

### Issue 3: Cache errors

**Cause:** bootstrap/cache/ not writable

**Fix:**
```bash
sudo chown -R www:www bootstrap/cache
sudo chmod -R 775 bootstrap/cache
```

### Issue 4: Uploaded files can't be accessed

**Cause:** Wrong permissions on storage/app/public

**Fix:**
```bash
sudo chown -R www:www storage/app/public
sudo chmod -R 775 storage/app/public
php artisan storage:link
```

## Security Best Practices

### DO:
- ✅ Use 755 for directories (rwxr-xr-x)
- ✅ Use 644 for files (rw-r--r--)
- ✅ Use 775 for storage/ and bootstrap/cache/ (rwxrwxr-x)
- ✅ Keep .env file 600 (rw-------)
- ✅ Use deployment user for project files
- ✅ Use web server user only for storage/ and cache/

### DON'T:
- ❌ Never use 777 permissions
- ❌ Never run npm with sudo
- ❌ Never make entire project owned by www user
- ❌ Never make .env world-readable
- ❌ Never commit vendor/ or node_modules/ to git

## Deployment Workflow

### Initial Setup

```bash
# 1. Clone repository as deployment user
git clone https://github.com/user/repo.git
cd repo

# 2. Install dependencies (no sudo!)
composer install --no-dev --optimize-autoloader
npm install

# 3. Build assets (no sudo!)
npm run build

# 4. Fix permissions
bash scripts/fix-permissions.sh

# 5. Setup Laravel
cp .env.example .env
php artisan key:generate
php artisan migrate --force
```

### Regular Deployment

```bash
# 1. Pull latest code
git pull origin main

# 2. Update dependencies
composer install --no-dev --optimize-autoloader
npm install

# 3. Build assets
npm run build

# 4. Fix permissions (if needed)
bash scripts/fix-permissions.sh

# 5. Clear caches
php artisan optimize:clear
php artisan config:cache
php artisan view:cache

# 6. Restart services
sudo systemctl restart php8.4-fpm
```

## Troubleshooting

### Check Web Server User

```bash
# Method 1: Check process
ps aux | grep -E 'nginx|apache|php-fpm' | head -1

# Method 2: Check config
# Nginx
grep "^user" /etc/nginx/nginx.conf

# Apache
grep "^User" /etc/apache2/apache2.conf

# PHP-FPM
grep "^user" /etc/php/8.4/fpm/pool.d/www.conf
```

### Verify Permissions

```bash
# Check if web server can write to storage
sudo -u www touch storage/logs/test.log
ls -la storage/logs/test.log
rm storage/logs/test.log

# Check if you can run npm
npm --version
npm run build
```

### Reset All Permissions

```bash
# Nuclear option - reset everything
sudo chown -R $USER:$USER .
sudo chown -R www:www storage bootstrap/cache
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
chmod -R 775 storage bootstrap/cache
chmod +x artisan
chmod +x scripts/*.sh
```

## ACL (Advanced)

For shared hosting or complex setups, use ACL:

```bash
# Install ACL tools
sudo apt-get install acl

# Set ACL for storage
sudo setfacl -R -m u:www:rwx storage
sudo setfacl -R -m u:bowo:rwx storage
sudo setfacl -dR -m u:www:rwx storage
sudo setfacl -dR -m u:bowo:rwx storage

# Same for bootstrap/cache
sudo setfacl -R -m u:www:rwx bootstrap/cache
sudo setfacl -R -m u:bowo:rwx bootstrap/cache
sudo setfacl -dR -m u:www:rwx bootstrap/cache
sudo setfacl -dR -m u:bowo:rwx bootstrap/cache
```

## Automated Fix Script

The `scripts/fix-permissions.sh` script automatically:
1. Detects current user
2. Detects web server user
3. Fixes project ownership
4. Fixes storage/cache ownership
5. Sets correct permissions
6. Verifies setup

Run it after every deployment or when encountering permission issues.

## References

- [Laravel Deployment Documentation](https://laravel.com/docs/deployment)
- [Linux File Permissions](https://www.linux.com/training-tutorials/understanding-linux-file-permissions/)
- [ACL Documentation](https://wiki.archlinux.org/title/Access_Control_Lists)
