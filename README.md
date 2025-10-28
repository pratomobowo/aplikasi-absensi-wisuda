# Sistem Absensi Wisuda Digital

Aplikasi berbasis web untuk mengelola kehadiran mahasiswa pada acara wisuda menggunakan teknologi QR code.

## Fitur Utama

- **Admin Dashboard**: Kelola data mahasiswa dan acara wisuda menggunakan Filament
- **Excel Import**: Import data mahasiswa dari file Excel/CSV dengan validasi otomatis
- **Magic Link**: Generate link unik untuk setiap mahasiswa
- **QR Code**: 3 QR code per mahasiswa (mahasiswa, pendamping 1, pendamping 2)
- **Scanner App**: Aplikasi scanner berbasis browser untuk panitia
- **PDF Generation**: Download undangan dalam format PDF
- **Real-time Statistics**: Monitor kehadiran secara real-time

## Technology Stack

- Laravel 12
- Filament v3
- Livewire v3
- Tailwind CSS
- MySQL 8.0+
- SimpleSoftwareIO/simple-qrcode
- barryvdh/laravel-dompdf

## Deployment

See [DEPLOYMENT.md](DEPLOYMENT.md) for complete deployment guide.

**Quick Commands:**
```bash
# Complete fix (recommended for production issues)
bash scripts/complete-fix.sh

# Full deployment
bash scripts/deploy.sh

# Fix .env configuration
bash scripts/fix-env-production.sh

# Clear caches only
bash scripts/clear-production-cache.sh

# Debug Livewire issues
bash scripts/debug-livewire.sh
```

**Documentation:** 
- [COMMON_ISSUES.md](COMMON_ISSUES.md) - Quick reference for common production issues ⭐
- [PRODUCTION_CHECKLIST.md](PRODUCTION_CHECKLIST.md) - Complete production deployment checklist
- [QUICK_FIX.md](QUICK_FIX.md) - Quick fix for Livewire JavaScript 404 error
- [TROUBLESHOOTING.md](TROUBLESHOOTING.md) - Complete troubleshooting guide
- [DEPLOYMENT.md](DEPLOYMENT.md) - Deployment guide
- [docs/CLOUDFLARE_SETUP.md](docs/CLOUDFLARE_SETUP.md) - Cloudflare configuration guide

## Security Features

✅ **Encryption**: AES-256-CBC encryption for QR codes and sessions  
✅ **CSRF Protection**: Automatic CSRF protection on all forms  
✅ **Input Validation**: Comprehensive validation using Form Requests  
✅ **SQL Injection Prevention**: Eloquent ORM with parameter binding  
✅ **XSS Prevention**: Automatic HTML entity encoding  
✅ **Rate Limiting**: Configurable rate limits on all endpoints  
✅ **Security Headers**: X-Frame-Options, CSP, and more  
✅ **Secure Sessions**: Encrypted, HTTP-only, secure cookies  

See [SECURITY.md](SECURITY.md) for detailed security documentation.

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer 2.x
- MySQL 8.0 or higher
- Node.js 18+ and npm

### Setup Steps

1. Clone the repository
```bash
git clone <repository-url>
cd wisuda-app
```

2. Install dependencies
```bash
composer install
npm install
```

3. Configure environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Generate QR encryption key
```bash
openssl rand -base64 32
```
Add the output to `.env` as `QR_ENCRYPTION_KEY=base64:YOUR_KEY_HERE`

5. Configure database in `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wisuda
DB_USERNAME=root
DB_PASSWORD=your_password
```

6. Run migrations
```bash
php artisan migrate
```

7. Seed database (optional)
```bash
php artisan db:seed
```

8. Build assets
```bash
npm run build
```

9. Start development server
```bash
php artisan serve
```

## Security Configuration

### Required Environment Variables

```env
# Application
APP_KEY=base64:...                    # Generate with: php artisan key:generate
APP_ENV=production
APP_DEBUG=false

# QR Code Encryption
QR_ENCRYPTION_KEY=base64:...          # Generate with: openssl rand -base64 32
QR_ENCRYPTION_CIPHER=AES-256-CBC

# Session Security
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

### Run Security Audit

```bash
./scripts/security-audit.sh
```

## Usage

### Admin Panel

Access the admin panel at `/admin`

Default credentials (after seeding):
- Email: admin@example.com
- Password: password

### Importing Student Data

1. Navigate to the Mahasiswa (Students) page in the admin panel
2. Click "Download Template" to get the CSV template
3. Fill in the template with student data:
   - **npm**: Student ID (required, max 20 characters)
   - **nama**: Full name (required, max 255 characters)
   - **prodi**: Study program (required, max 255 characters)
   - **fakultas**: Faculty (required, max 255 characters)
   - **ipk**: GPA (required, 0-4 scale)
   - **yudisium**: Honors (optional: Cum Laude, Sangat Memuaskan, Memuaskan)
   - **email**: Email address (optional)
   - **phone**: Phone number (optional)
4. Click "Import Excel" and upload your file
5. The system will validate and import the data, showing a summary of:
   - Successfully imported records
   - Updated duplicate records (based on NPM)
   - Failed records with error details

### Scanner Application

Access the scanner at `/scanner` (requires authentication)

### Magic Link

Students receive a unique magic link via WhatsApp:
```
https://your-domain.com/invitation/{token}
```

## Development

### Code Style

Follow PSR-12 coding standards:
```bash
./vendor/bin/pint
```

### Testing

Run tests:
```bash
php artisan test
```

## Deployment

### Production Checklist

- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure HTTPS with valid SSL certificate
- [ ] Set `SESSION_SECURE_COOKIE=true`
- [ ] Configure proper file permissions
- [ ] Enable database backups
- [ ] Set up monitoring and alerting
- [ ] Run security audit: `./scripts/security-audit.sh`

### Server Requirements

- PHP 8.2+
- MySQL 8.0+
- SSL Certificate (required for camera access)
- Composer 2.x
- Node.js 18+

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
