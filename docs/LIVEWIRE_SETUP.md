# Livewire Setup Notes

## Symlink Solution

If Livewire JavaScript files return 404 in production, create a symlink:

```bash
ln -s ../vendor/livewire/livewire/dist ./public/livewire
```

This makes Livewire assets accessible via `/livewire/livewire.js` route.

## Important .env Settings

For Livewire to work properly in production:

```env
APP_ENV=production
APP_DEBUG=false
SESSION_ENCRYPT=false  # Important: Must be false for Livewire
SESSION_SECURE_COOKIE=true
```

## Verification

Test that Livewire is working:

```bash
# Check route exists
php artisan route:list --path=livewire

# Test endpoint
curl -I https://your-domain.com/livewire/livewire.js
# Should return: HTTP/2 200
```

## Deployment

The symlink should be created automatically during deployment. If not, add to your deployment script:

```bash
# In scripts/deploy.sh
if [ ! -L "public/livewire" ]; then
    ln -s ../vendor/livewire/livewire/dist ./public/livewire
fi
```
