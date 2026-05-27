# Setup Supervisor untuk Laravel Queue Worker

## 1. Install Supervisor

```bash
sudo apt-get update
sudo apt-get install -y supervisor
```

## 2. Copy Config File

```bash
sudo cp /www/wwwroot/wisuda.usbypkp.ac.id/aplikasi-absensi-wisuda/supervisor/laravel-worker.conf /etc/supervisor/conf.d/
```

## 3. Update User (Sesuaikan dengan user web server)

Edit file config:
```bash
sudo nano /etc/supervisor/conf.d/laravel-worker.conf
```

Ganti `user=www-data` dengan user yang menjalankan web server (contoh: `www`, `nginx`, `apache`, atau root).

Cek user web server:
```bash
ps aux | grep php-fpm | head -2
```

## 4. Reload Supervisor

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

## 5. Verifikasi

```bash
# Cek status
sudo supervisorctl status

# Output harusnya:
# laravel-worker:laravel-worker_00   RUNNING   pid 12345, uptime 0:00:05
```

## 6. Monitor Log

```bash
tail -f /www/wwwroot/wisuda.usbypkp.ac.id/aplikasi-absensi-wisuda/storage/logs/worker.log
```

## Troubleshooting

### Kalau worker tidak jalan:
```bash
# Cek error
sudo supervisorctl tail laravel-worker:laravel-worker_00

# Restart
sudo supervisorctl restart laravel-worker:*
```

### Kalau permission denied:
```bash
# Fix permission
sudo chown -R www-data:www-data /www/wwwroot/wisuda.usbypkp.ac.id/aplikasi-absensi-wisuda/storage/
sudo chmod -R 775 /www/wwwroot/wisuda.usbypkp.ac.id/aplikasi-absensi-wisuda/storage/
```

### Restart setelah deploy:
```bash
sudo supervisorctl restart laravel-worker:*
```

## Keuntungan Supervisor:
- ✅ Auto start saat server boot
- ✅ Auto restart kalau worker crash
- ✅ Jalan di background (tidak perlu terminal)
- ✅ Bisa monitor status dan log
