# Security Documentation

## Overview

This document outlines the security measures implemented in the Sistem Absensi Wisuda Digital application.

## Security Features

### 1. Encryption

#### Application Encryption
- **APP_KEY**: Laravel's application encryption key (AES-256-CBC)
- Used for encrypting cookies, sessions, and other sensitive data
- Generated using: `php artisan key:generate`

#### Session Encryption
- **SESSION_ENCRYPT**: Enabled (true)
- All session data is encrypted before storage
- Prevents session data tampering

### 2. CSRF Protection

- **Automatic Protection**: Laravel's CSRF middleware is enabled by default
- **Livewire**: Automatically includes CSRF tokens in all requests
- **Filament**: Built-in CSRF protection for all forms
- **API Endpoints**: Protected via middleware

### 3. Input Validation

#### Form Request Validation
All user inputs are validated using Laravel Form Request classes:

- `StoreMahasiswaRequest`: Validates student creation
- `UpdateMahasiswaRequest`: Validates student updates
- `StoreGraduationEventRequest`: Validates event creation
- `UpdateGraduationEventRequest`: Validates event updates

#### Validation Rules
- **NIM**: Alphanumeric only, unique, max 20 characters
- **Name**: Letters and spaces only, max 255 characters
- **Email**: Valid email format
- **Phone**: Numbers and basic phone characters only
- **Coordinates**: Numeric, within valid latitude/longitude ranges

#### Input Sanitization
- Token parameters: Alphanumeric and basic URL-safe characters only
- QR data: Length validation, XSS prevention via strip_tags
- All inputs: HTML entity encoding in views

### 4. SQL Injection Prevention

- **Eloquent ORM**: All database queries use Eloquent ORM
- **No Raw Queries**: No `DB::raw()`, `DB::select()`, or `DB::statement()` usage
- **Parameter Binding**: All queries use parameter binding automatically
- **Verified**: Code audit confirms no raw SQL queries

### 5. Session Security

#### Session Configuration
```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

#### Security Features
- **Encrypted Sessions**: All session data encrypted
- **Secure Cookies**: Only transmitted over HTTPS (production)
- **HTTP Only**: Prevents JavaScript access to session cookies
- **SameSite**: Protects against CSRF attacks
- **Database Storage**: Sessions stored in database, not files

### 6. Security Headers

The `SecurityHeaders` middleware adds the following headers to all responses:

- **X-Content-Type-Options**: `nosniff` - Prevents MIME type sniffing
- **X-Frame-Options**: `SAMEORIGIN` - Prevents clickjacking
- **X-XSS-Protection**: `1; mode=block` - Enables XSS filter
- **Referrer-Policy**: `strict-origin-when-cross-origin` - Controls referrer information
- **Permissions-Policy**: Restricts camera, geolocation, microphone access
- **Content-Security-Policy**: Restricts resource loading to trusted sources

### 7. Rate Limiting

#### Configured Rate Limits

| Endpoint | Limit | Scope |
|----------|-------|-------|
| Magic Link Access | 10 req/min | Per IP |
| Scanner API | 30 req/min | Per User |
| PDF Download | 5 req/min | Per Token |

#### Implementation
- Rate limiters defined in `AppServiceProvider`
- Applied via middleware in routes
- Returns 429 status code when exceeded

### 8. Authentication & Authorization

#### User Roles
- **Admin**: Full access to admin panel
- **Scanner**: Access to scanner application only

#### Authentication
- **Filament**: Built-in authentication for admin panel
- **Scanner**: Laravel authentication middleware
- **Password Hashing**: Bcrypt with 12 rounds

#### Authorization
- Role-based access control
- Filament policies for resource access
- Middleware protection on routes

### 9. Token Security

#### Magic Link Tokens
- Generated using Laravel's encryption
- Include expiration timestamp
- Validated before use
- Single-use per event
- Expire after 30 days

#### QR Code Tokens
- Plain JSON format for easy scanning
- Include timestamp for reference
- Role-based validation
- Expire after graduation event

### 10. Logging & Monitoring

#### Security Logging
All security-relevant events are logged:

- Magic link access attempts
- QR code scan attempts (success and failure)
- Failed validations
- Rate limit violations

#### Log Channels
- **stack**: General application logs
- **attendance**: Attendance-specific logs

### 11. XSS Prevention

- **Blade Templates**: Automatic HTML entity encoding via `{{ }}`
- **Input Sanitization**: strip_tags() on user inputs
- **CSP Headers**: Restrict inline scripts and styles
- **Livewire**: Automatic XSS protection

### 12. File Upload Security

- **PDF Generation**: Server-side only, no user uploads
- **QR Codes**: Generated server-side, base64 encoded
- **No File Uploads**: Application doesn't accept file uploads

## Security Best Practices

### For Developers

1. **Never use raw SQL queries** - Always use Eloquent ORM
2. **Validate all inputs** - Use Form Requests for validation
3. **Sanitize user data** - Use appropriate sanitization functions
4. **Use HTTPS in production** - Required for secure cookies and camera access
5. **Keep dependencies updated** - Regularly update Laravel and packages
6. **Review logs regularly** - Monitor for suspicious activity
7. **Use environment variables** - Never hardcode sensitive data

### For Deployment

1. **Set APP_ENV to production**
2. **Set APP_DEBUG to false**
3. **Use HTTPS with valid SSL certificate**
4. **Set SESSION_SECURE_COOKIE to true**
5. **Configure proper file permissions**
6. **Enable database backups**
7. **Set up monitoring and alerting**
8. **Use strong database passwords**
9. **Restrict database access to application server only**
10. **Keep server software updated**

## Environment Variables

### Required Security Variables

```env
# Application
APP_KEY=base64:...                    # Generate with: php artisan key:generate
APP_ENV=production
APP_DEBUG=false

# Session Security
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

# Database (use strong password)
DB_PASSWORD=...
```

## Vulnerability Reporting

If you discover a security vulnerability, please email: [security@example.com]

**Do not** create public GitHub issues for security vulnerabilities.

## Security Checklist

- [x] Encryption keys configured
- [x] CSRF protection enabled
- [x] Input validation implemented
- [x] SQL injection prevention verified
- [x] Session security configured
- [x] Security headers added
- [x] Rate limiting configured
- [x] Authentication implemented
- [x] Token security implemented
- [x] Logging configured
- [x] XSS prevention implemented
- [x] Security documentation created

## Compliance

This application implements security measures in accordance with:

- OWASP Top 10 security risks
- Laravel security best practices
- PHP security guidelines

## Updates

This security documentation should be reviewed and updated:
- When new features are added
- When security vulnerabilities are discovered
- At least quarterly

Last Updated: October 27, 2025
