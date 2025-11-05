# File Restoration Summary

## Date: 2025-11-05

## Issue
The file `app/Console/Commands/RegenerateQRTokens.php` was corrupted with garbled text, making it unusable.

## Resolution
The file has been successfully restored to its proper state with the following characteristics:

### File Details
- **Path**: `app/Console/Commands/RegenerateQRTokens.php`
- **Purpose**: Laravel Artisan command to regenerate QR tokens for graduation tickets
- **Command**: `php artisan tickets:regenerate-qr`

### Features
1. **Three Operation Modes**:
   - Interactive mode (default): Prompts user for confirmation
   - Specific ticket: `--ticket=ID` to regenerate single ticket
   - All tickets: `--all` to regenerate all tickets without prompt

2. **Functionality**:
   - Loads graduation tickets with relationships
   - Generates encrypted QR tokens for 3 roles: mahasiswa, pendamping1, pendamping2
   - Verifies encryption/decryption works correctly
   - Saves tokens to database
   - Shows progress bar during processing
   - Reports success/error counts with details

3. **Error Handling**:
   - Catches exceptions per ticket
   - Continues processing remaining tickets on error
   - Displays detailed error report at end

### Verification
✅ PHP syntax check passed
✅ Laravel command registration successful
✅ Command appears in `php artisan list`

### Usage Examples
```bash
# Interactive mode
php artisan tickets:regenerate-qr

# Regenerate specific ticket
php artisan tickets:regenerate-qr --ticket=123

# Regenerate all tickets
php artisan tickets:regenerate-qr --all
```

## Status
**RESOLVED** - File is fully functional and ready for use.
