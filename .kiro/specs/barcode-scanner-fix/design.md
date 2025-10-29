# Design Document - Barcode Scanner Fix

## Overview

Desain ini bertujuan untuk memperbaiki sistem scan barcode yang mengalami looping dan kegagalan validasi. Pendekatan yang diambil adalah menyederhanakan state management, memperbaiki flow scanning dengan pause/resume mechanism, meningkatkan validasi data, dan menambahkan logging yang lebih detail untuk debugging.

### Key Improvements

1. **State Machine yang Jelas**: Implementasi state machine dengan 4 state yang terdefinisi dengan baik (ready, scanning, success, error)
2. **Pause/Resume Mechanism**: Menggunakan fitur pause() dari html5-qrcode untuk mencegah scan ganda
3. **Cooldown Period**: Implementasi cooldown 5 detik untuk mencegah scan berulang pada QR code yang sama
4. **Enhanced Validation**: Validasi bertahap dengan logging detail di setiap step
5. **Auto-Recovery**: Sistem otomatis kembali ke state ready setelah success/error
6. **Comprehensive Logging**: Logging detail untuk troubleshooting dan monitoring

## Architecture

### Component Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                     Scanner View (Blade)                     │
│  ┌────────────────────────────────────────────────────────┐ │
│  │  Html5-QRCode Library (JavaScript)                     │ │
│  │  - Camera Management                                   │ │
│  │  - QR Detection                                        │ │
│  │  - Pause/Resume Control                                │ │
│  └────────────────┬───────────────────────────────────────┘ │
│                   │ onScanSuccess()                          │
│                   ▼                                          │
│  ┌────────────────────────────────────────────────────────┐ │
│  │  Scanner Component (Livewire)                          │ │
│  │  - State Management (ready/scanning/success/error)     │ │
│  │  - Cooldown Tracking                                   │ │
│  │  - Auto-Reset Timer                                    │ │
│  └────────────────┬───────────────────────────────────────┘ │
└───────────────────┼──────────────────────────────────────────┘
                    │ scanQRCode($qrData)
                    ▼
┌─────────────────────────────────────────────────────────────┐
│              AttendanceService (PHP)                         │
│  ┌────────────────────────────────────────────────────────┐ │
│  │  Validation Pipeline:                                  │ │
│  │  1. Format Validation                                  │ │
│  │  2. QR Decryption (QRCodeService)                      │ │
│  │  3. Data Structure Validation                          │ │
│  │  4. Database Lookup                                    │ │
│  │  5. Duplicate Check                                    │ │
│  │  6. Record Attendance (Transaction)                    │ │
│  └────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

### State Flow Diagram

```
┌─────────┐
│  READY  │◄──────────────────────────────────┐
└────┬────┘                                    │
     │ QR Detected                             │
     │ (cooldown passed)                       │
     ▼                                         │
┌──────────┐                                   │
│ SCANNING │                                   │
│ (paused) │                                   │
└────┬─────┘                                   │
     │                                         │
     ├─────────────┬─────────────┐            │
     │ Valid       │ Invalid     │            │
     ▼             ▼             │            │
┌─────────┐   ┌─────────┐       │            │
│ SUCCESS │   │  ERROR  │       │            │
└────┬────┘   └────┬────┘       │            │
     │             │             │            │
     │ 3s timer    │ 3s timer    │            │
     └─────────────┴─────────────┴────────────┘
              Auto Reset
```

## Components and Interfaces

### 1. Scanner Livewire Component

**File**: `app/Livewire/Scanner.php`

**Properties**:
```php
public string $status = 'ready';        // State: ready|scanning|success|error
public ?array $scanResult = null;       // Data hasil scan sukses
public string $errorMessage = '';       // Pesan error
private array $scanHistory = [];        // History scan untuk debugging
```

**Public Methods**:
```php
// Dipanggil dari JavaScript saat QR terdeteksi
public function scanQRCode(string $qrData): void

// Reset manual oleh user
public function forceReset(): void

// Auto-reset setelah success/error
public function doReset(): void
```

**Events Dispatched**:
- `scanner-ready`: Dikirim saat scanner siap menerima scan baru
- `scanner-auto-reset`: Dikirim untuk trigger auto-reset dengan delay

**Validation Rules**:
- QR data tidak boleh kosong
- QR data maksimal 1000 karakter
- Sanitasi input dengan strip_tags()

### 2. AttendanceService

**File**: `app/Services/AttendanceService.php`

**Public Methods**:

```php
/**
 * Record attendance dengan validation pipeline
 * 
 * @param string $qrData Raw QR data (encrypted)
 * @param User|null $scanner User yang melakukan scan
 * @return array [
 *   'success' => bool,
 *   'message' => string,
 *   'data' => array|null,
 *   'debug' => array (hanya di development)
 * ]
 */
public function recordAttendance(string $qrData, ?User $scanner = null): array

/**
 * Validate QR code dengan detailed logging
 * 
 * @param string $qrData
 * @return array [
 *   'valid' => bool,
 *   'message' => string,
 *   'data' => array|null,
 *   'reason' => string,
 *   'step' => string (step terakhir yang dijalankan)
 * ]
 */
public function validateQRCode(string $qrData): array

/**
 * Check duplicate attendance
 */
public function checkDuplicate(int $ticketId, string $role): bool
```

**Validation Pipeline Steps**:

1. **Format Validation**
   - Check QR data tidak kosong
   - Check panjang data reasonable (< 1000 chars)
   - Log: "Validation Step 1: Format check"

2. **Decryption**
   - Decrypt menggunakan QRCodeService
   - Handle decryption failure
   - Log: "Validation Step 2: Decryption" + partial raw data

3. **Structure Validation**
   - Check required fields: ticket_id, role, event_id
   - Validate role value (mahasiswa|pendamping1|pendamping2)
   - Log: "Validation Step 3: Structure check"

4. **Database Lookup**
   - Find ticket by ID
   - Check ticket exists
   - Check ticket not expired
   - Log: "Validation Step 4: Database lookup" + ticket status

5. **Duplicate Check**
   - Check existing attendance record
   - Log: "Validation Step 5: Duplicate check"

6. **Record Attendance**
   - Use database transaction
   - Create attendance record
   - Commit transaction
   - Log: "Validation Step 6: Record created"

### 3. JavaScript Scanner Controller

**File**: `resources/views/livewire/scanner.blade.php` (dalam @push('scripts'))

**State Variables**:
```javascript
let html5QrCode = null;              // Scanner instance
let isProcessing = false;            // Flag untuk prevent concurrent scans
let lastScanTime = 0;                // Timestamp scan terakhir
let lastScannedCode = '';            // QR code terakhir yang di-scan
const SCAN_COOLDOWN = 5000;          // 5 detik cooldown
```

**Functions**:

```javascript
// Initialize scanner dengan error handling
function initScanner()

// Handle successful QR detection
function onScanSuccess(decodedText)

// Handle scan errors (silent, hanya log)
function onScanFailure(error)

// Resume scanner setelah processing
function resumeScanner()

// Check apakah scan diperbolehkan (cooldown check)
function canScan(decodedText)
```

**Scanning Logic Flow**:

1. QR Code terdeteksi → `onScanSuccess()` dipanggil
2. Check: Apakah sama dengan `lastScannedCode`? → Skip jika sama
3. Check: Apakah dalam cooldown period? → Skip jika masih cooldown
4. Check: Apakah `isProcessing`? → Skip jika sedang processing
5. Check: Apakah status = 'ready'? → Skip jika bukan ready
6. Set `isProcessing = true`
7. **Pause scanner** dengan `html5QrCode.pause(true)`
8. Update `lastScanTime` dan `lastScannedCode`
9. Call Livewire: `@this.scanQRCode(decodedText)`
10. Tunggu event `scanner-ready` untuk resume

**Event Listeners**:

```javascript
// Listen untuk resume scanner
Livewire.on('scanner-ready', () => {
    setTimeout(() => {
        if (@this.status === 'ready') {
            resumeScanner();
        }
    }, 1000); // Delay 1 detik untuk memastikan UI sudah update
});

// Listen untuk auto-reset
Livewire.on('scanner-auto-reset', (event) => {
    setTimeout(() => {
        @this.doReset();
    }, event.delay || 3000);
});
```

## Data Models

### Attendance Model

Tidak ada perubahan pada model, hanya penambahan logging.

### QR Code Data Structure

```json
{
    "ticket_id": 123,
    "role": "mahasiswa",
    "event_id": 1,
    "generated_at": "2025-10-29T10:00:00Z"
}
```

**Encrypted Format**: Base64 encoded encrypted JSON

## Error Handling

### Error Categories

1. **Client-Side Errors** (JavaScript)
   - Camera permission denied
   - No camera available
   - QR detection timeout
   - Network error saat call Livewire

2. **Validation Errors** (PHP)
   - Invalid QR format
   - Decryption failed
   - Missing required fields
   - Invalid role value

3. **Business Logic Errors** (PHP)
   - Ticket not found
   - Ticket expired
   - Duplicate attendance
   - Event not active

4. **System Errors** (PHP)
   - Database connection error
   - Transaction failed
   - Service unavailable

### Error Messages

**User-Facing Messages** (Bahasa Indonesia):
- "QR Code tidak valid atau rusak"
- "Data tidak ditemukan di database"
- "Tiket sudah kadaluarsa"
- "Sudah melakukan absensi sebelumnya"
- "Terjadi kesalahan sistem, silakan coba lagi"

**Log Messages** (English, detailed):
- "QR validation failed at step X: [reason]"
- "Decryption failed: [error details]"
- "Database lookup failed: ticket_id=[id] not found"
- "Duplicate attendance detected: ticket_id=[id], role=[role]"

### Error Recovery

1. **Auto-Recovery**: Semua error akan auto-reset ke state ready setelah 3 detik
2. **Manual Recovery**: User dapat klik tombol "Reset" kapan saja
3. **Camera Recovery**: Jika camera error, tampilkan pesan dengan tombol "Coba Lagi" yang reload halaman

## Testing Strategy

### Unit Tests

**AttendanceService Tests**:
```php
// Test validation pipeline
test_validate_qr_code_with_valid_data()
test_validate_qr_code_with_invalid_format()
test_validate_qr_code_with_decryption_failure()
test_validate_qr_code_with_missing_fields()
test_validate_qr_code_with_invalid_role()
test_validate_qr_code_with_nonexistent_ticket()
test_validate_qr_code_with_expired_ticket()

// Test duplicate check
test_check_duplicate_returns_true_when_exists()
test_check_duplicate_returns_false_when_not_exists()

// Test record attendance
test_record_attendance_success()
test_record_attendance_with_duplicate()
test_record_attendance_with_invalid_qr()
test_record_attendance_transaction_rollback_on_error()
```

**Scanner Component Tests**:
```php
// Test state management
test_initial_state_is_ready()
test_scan_qr_code_changes_state_to_scanning()
test_successful_scan_changes_state_to_success()
test_failed_scan_changes_state_to_error()
test_reset_returns_to_ready_state()

// Test validation
test_scan_qr_code_rejects_empty_data()
test_scan_qr_code_rejects_too_long_data()
test_scan_qr_code_sanitizes_input()

// Test events
test_success_dispatches_auto_reset_event()
test_error_dispatches_auto_reset_event()
test_reset_dispatches_scanner_ready_event()
```

### Integration Tests

```php
// Test full flow
test_complete_scan_flow_with_valid_qr()
test_complete_scan_flow_with_invalid_qr()
test_complete_scan_flow_with_duplicate()
test_scanner_auto_recovery_after_error()
test_scanner_cooldown_prevents_rapid_scans()
```

### Manual Testing Checklist

**Happy Path**:
- [ ] Scan QR code mahasiswa → Success screen muncul
- [ ] Scan QR code pendamping1 → Success screen muncul
- [ ] Scan QR code pendamping2 → Success screen muncul
- [ ] Auto-reset setelah 3 detik → Kembali ke ready state
- [ ] Scanner resume dan bisa scan lagi

**Error Cases**:
- [ ] Scan QR code invalid → Error message jelas
- [ ] Scan QR code expired → Error message "Tiket sudah kadaluarsa"
- [ ] Scan QR code yang sama 2x → Error "Sudah melakukan absensi"
- [ ] Scan QR code tidak ada di database → Error "Data tidak ditemukan"
- [ ] Auto-reset setelah error → Kembali ke ready state

**Edge Cases**:
- [ ] Scan QR code sangat cepat berturut-turut → Hanya 1 yang diproses
- [ ] Scan QR code yang sama dalam 5 detik → Diabaikan (cooldown)
- [ ] Camera permission denied → Pesan error muncul dengan tombol retry
- [ ] Network error saat processing → Error message muncul
- [ ] Manual reset saat processing → Scanner kembali ke ready

**Performance**:
- [ ] Scan response time < 2 detik
- [ ] No memory leak setelah 100+ scans
- [ ] Camera feed smooth (10 FPS)
- [ ] UI responsive saat scanning

## Logging Strategy

### Log Levels

**DEBUG**: Detailed flow information
```
Scanner: QR scan started, qr_length=256
Scanner: Status changed to scanning
Scanner: Calling AttendanceService
AttendanceService: Validation Step 1 - Format check passed
AttendanceService: Validation Step 2 - Decryption started
```

**INFO**: Important events
```
Scanner: Success, scheduled reset
AttendanceService: Attendance recorded successfully, ticket_id=123, role=mahasiswa
QR Scan Attempt: scanner_id=5, result=success, reason=valid
```

**WARNING**: Recoverable errors
```
Scanner: Invalid QR data length
AttendanceService: Duplicate attendance detected, ticket_id=123
QR Scan Attempt: result=failed, reason=duplicate
```

**ERROR**: System errors
```
AttendanceService: Database transaction failed: [error details]
AttendanceService: Decryption service unavailable
QR Scan Attempt: result=failed, reason=exception: [message]
```

### Log Format

```
[timestamp] [level] [context] message {structured_data}
```

Example:
```
[2025-10-29 10:15:30] INFO Scanner: Attendance recorded successfully {"ticket_id":123,"role":"mahasiswa","scanner_id":5,"duration_ms":450}
```

### Log Retention

- **Development**: All logs, no rotation
- **Production**: 
  - INFO and above: 30 days
  - DEBUG: 7 days
  - Rotate daily, compress after 1 day

## Performance Considerations

### Frontend Optimization

1. **Scanner Configuration**:
   - FPS: 10 (balance antara responsiveness dan CPU usage)
   - QR Box: 250x250 (optimal untuk mobile dan desktop)
   - Disable flip: false (support mirrored QR codes)

2. **State Management**:
   - Minimize Livewire round-trips
   - Use JavaScript untuk cooldown check (tidak perlu server call)
   - Debounce scan events

3. **Memory Management**:
   - Clear scanner state setelah reset
   - Remove event listeners saat component destroyed
   - Limit scan history size

### Backend Optimization

1. **Database Queries**:
   - Use eager loading untuk ticket->mahasiswa relationship
   - Index pada graduation_ticket_id dan role di attendances table
   - Use database transaction untuk consistency

2. **Caching**:
   - Cache active graduation events
   - Cache QR decryption keys
   - No cache untuk attendance records (real-time data)

3. **Validation**:
   - Fail fast: Check paling murah dulu (format, length)
   - Skip expensive operations jika basic validation gagal
   - Use database exists() untuk duplicate check (lebih cepat dari count())

## Security Considerations

1. **Input Validation**:
   - Sanitize QR data dengan strip_tags()
   - Limit QR data length (max 1000 chars)
   - Validate data structure sebelum processing

2. **Authentication**:
   - Scanner route hanya accessible untuk authenticated users
   - Check user role (scanner atau admin)
   - Log scanner user ID untuk audit trail

3. **Encryption**:
   - QR data harus encrypted
   - Use secure encryption algorithm (AES-256)
   - Rotate encryption keys periodically

4. **Rate Limiting**:
   - Cooldown period 5 detik per QR code
   - Max 100 scans per minute per user
   - Block suspicious patterns (rapid scans dari IP yang sama)

5. **Audit Trail**:
   - Log semua scan attempts (success dan failed)
   - Include timestamp, scanner ID, IP address
   - Store logs securely dengan retention policy

## Deployment Considerations

### Environment Variables

```env
# Scanner Configuration
SCANNER_COOLDOWN_MS=5000
SCANNER_AUTO_RESET_MS=3000
SCANNER_FPS=10
SCANNER_QR_BOX_SIZE=250

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=info
LOG_SCANNER_DEBUG=false  # Set true untuk detailed logging
```

### Database Migrations

Tidak ada perubahan schema, tapi pastikan indexes ada:

```sql
-- Check indexes
SHOW INDEX FROM attendances WHERE Key_name = 'idx_ticket_role';

-- Create if not exists
CREATE INDEX idx_ticket_role ON attendances(graduation_ticket_id, role);
```

### Monitoring

**Metrics to Track**:
- Scan success rate (target: > 95%)
- Average scan processing time (target: < 2s)
- Error rate by type
- Scanner uptime
- Concurrent scanner users

**Alerts**:
- Error rate > 10% dalam 5 menit
- Average processing time > 5 detik
- Database connection errors
- Decryption service failures

### Rollback Plan

Jika terjadi masalah setelah deployment:

1. **Quick Rollback**: Revert ke versi sebelumnya via Git
2. **Partial Rollback**: Disable scanner feature, redirect ke manual entry
3. **Data Integrity**: Attendance records tetap valid, tidak perlu rollback data
4. **Communication**: Notify users via admin panel jika scanner down

## Migration from Current Implementation

### Changes Required

**Scanner.php**:
- Tambah detailed logging di setiap method
- Improve error messages (lebih spesifik)
- Add scan history tracking (optional, untuk debugging)

**scanner.blade.php**:
- Implement pause/resume mechanism
- Add cooldown check di JavaScript
- Improve state management
- Add better error handling untuk camera permission

**AttendanceService.php**:
- Refactor validateQRCode() dengan step-by-step validation
- Add detailed logging di setiap validation step
- Improve error messages dengan reason codes
- Add database transaction untuk recordAttendance()

### Backward Compatibility

- QR code format tidak berubah (tetap encrypted JSON)
- Database schema tidak berubah
- API contract tidak berubah (return format sama)
- Existing QR codes tetap valid

### Testing Before Deployment

1. Test dengan existing QR codes
2. Test dengan berbagai devices (mobile, tablet, desktop)
3. Test dengan berbagai browsers (Chrome, Safari, Firefox)
4. Load test dengan multiple concurrent scanners
5. Test error scenarios (network error, database error, etc.)

### Deployment Steps

1. **Pre-deployment**:
   - Backup database
   - Test di staging environment
   - Prepare rollback plan

2. **Deployment**:
   - Deploy code changes
   - Clear cache (config, view, route)
   - Verify scanner accessible
   - Monitor logs untuk errors

3. **Post-deployment**:
   - Test scanner dengan real QR codes
   - Monitor error rates
   - Check performance metrics
   - Gather user feedback

4. **Monitoring Period**:
   - Monitor closely untuk 24 jam pertama
   - Check logs setiap 2 jam
   - Ready untuk quick rollback jika needed
