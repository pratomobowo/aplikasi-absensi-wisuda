# Scanner QR Code Implementation - Fixed

## Masalah yang Diperbaiki

### 1. Scanner Paused dan Stuck
**Gejala:** Scanner berhenti setelah scan pertama dan tidak bisa scan lagi.

**Penyebab:**
- Livewire call tidak pernah complete dalam waktu yang wajar
- Event listener tidak ter-trigger dengan benar
- Scanner tidak di-resume setelah proses selesai

### 2. Scanner Looping Terus Menerus
**Gejala:** Setelah scan gagal, scanner langsung scan lagi QR yang sama berulang-ulang.

**Penyebab:**
- Scanner resume terlalu cepat sebelum UI berubah
- QR code masih terlihat di kamera saat scanner resume
- Tidak ada cooldown period antara scan

## Solusi yang Diterapkan

### 1. Simplified Event System
Menggunakan Livewire events yang lebih sederhana dan reliable:

```php
// Backend (Scanner.php)
$this->dispatch('scanner-auto-reset', delay: 3000);  // Schedule auto-reset
$this->dispatch('scanner-ready');                     // Scanner ready to scan
```

```javascript
// Frontend (scanner.blade.php)
Livewire.on('scanner-auto-reset', (event) => {
    // Keep scanner paused during delay
    setTimeout(() => {
        @this.doReset();
    }, delay);
});

Livewire.on('scanner-ready', () => {
    // Resume with delay to ensure UI updated
    setTimeout(() => {
        resumeScanner();
    }, 500);
});
```

### 2. Scan Cooldown Mechanism
Mencegah scan berulang dengan cooldown period dan duplicate detection:

```javascript
const SCAN_COOLDOWN = 5000; // 5 seconds
let lastScanTime = 0;
let lastScannedCode = '';

function onScanSuccess(decodedText) {
    // Check if same code scanned recently
    if (decodedText === lastScannedCode) {
        console.log('Same QR code, ignoring');
        return;
    }
    
    const now = Date.now();
    if (now - lastScanTime < SCAN_COOLDOWN) {
        console.log('Scan cooldown active, ignoring');
        return;
    }
    lastScanTime = now;
    lastScannedCode = decodedText;
    // ... process scan
}
```

### 3. Status Check Before Scan
Memastikan status 'ready' sebelum memproses scan:

```javascript
function onScanSuccess(decodedText) {
    // Check if status is ready
    if (@this.status !== 'ready') {
        console.log('Status not ready, ignoring scan');
        return;
    }
    // ... process scan
}
```

### 4. Delayed Resume with Double Check
Scanner resume dengan delay dan double check status:

```javascript
Livewire.on('scanner-ready', () => {
    // Add delay to ensure UI updated and QR out of view
    setTimeout(() => {
        // Double check status before resuming
        if (@this.status === 'ready') {
            resumeScanner();
        } else {
            console.log('Status not ready yet, skipping resume');
        }
    }, 1000); // 1 second delay
});

function resumeScanner() {
    isProcessing = false;
    lastScannedCode = ''; // Clear last scanned code
    html5QrCode.resume();
}
```

### 5. Proper Scanner Pause Management
Scanner di-pause sekali saat scan, tidak perlu pause lagi:

```javascript
function onScanSuccess(decodedText) {
    // Pause scanner immediately
    html5QrCode.pause(true);
    // ... process scan
}

Livewire.on('scanner-auto-reset', (event) => {
    // Scanner already paused, no need to pause again
    setTimeout(() => {
        @this.doReset();
    }, delay);
});
```

## Flow Scanner yang Benar

```
1. Status: Ready
   └─> Scanner aktif, siap scan
   
2. QR Detected
   ├─> Check cooldown (2 detik)
   ├─> Check status === 'ready'
   ├─> Set isProcessing = true
   └─> Pause scanner
   
3. Call Backend
   └─> @this.scanQRCode(qrData)
   
4. Backend Process
   ├─> Validate QR
   ├─> Record attendance
   ├─> Set status (success/error)
   └─> Dispatch 'scanner-auto-reset' event
   
5. Frontend Receives Event
   ├─> Keep scanner paused
   └─> Schedule reset after 3 seconds
   
6. Auto Reset
   ├─> Call @this.doReset()
   ├─> Backend set status = 'ready'
   └─> Dispatch 'scanner-ready' event
   
7. Resume Scanner
   ├─> Wait 500ms (ensure UI updated)
   ├─> Set isProcessing = false
   └─> Resume scanner
   
8. Loop back to step 1
```

## Debugging

### Frontend Console (F12)
```
✓ QR detected: xxx...
✓ Scanner paused
✓ Auto-reset scheduled in 3000 ms
✓ Scanner ready event - will resume after delay
✓ Resuming scanner...
✓ Scanner resumed
```

### Backend Log
```bash
tail -f storage/logs/laravel.log
```

```
✓ Scanner: QR scan started
✓ Scanner: Status changed to scanning
✓ Scanner: Calling AttendanceService
✓ Scanner: AttendanceService returned
✓ Scanner: Success, scheduled reset
```

### Common Issues

**Issue: Scanner tidak resume**
- Check console untuk error
- Pastikan event 'scanner-ready' ter-trigger
- Check apakah html5QrCode masih ada

**Issue: Scanner scan berulang**
- Check cooldown period (2 detik)
- Pastikan status check berfungsi
- Verify UI benar-benar berubah saat success/error

**Issue: Livewire call timeout**
- Check backend log untuk error
- Verify database connection
- Check QRCodeService decrypt performance

## Testing

### Test Case 1: Scan Valid QR
```
1. Buka scanner
2. Scan QR code valid
3. Verify: Status berubah ke "scanning"
4. Verify: Tampil success screen
5. Verify: Auto-reset setelah 3 detik
6. Verify: Scanner resume dan siap scan lagi
```

### Test Case 2: Scan Invalid QR
```
1. Buka scanner
2. Scan QR code invalid
3. Verify: Status berubah ke "scanning"
4. Verify: Tampil error screen
5. Verify: Auto-reset setelah 3 detik
6. Verify: Scanner resume dan siap scan lagi
```

### Test Case 3: Scan Duplicate
```
1. Scan QR code yang sudah pernah di-scan
2. Verify: Tampil error "Sudah melakukan absensi"
3. Verify: Auto-reset setelah 3 detik
4. Verify: Scanner resume
```

### Test Case 4: Rapid Scan Prevention
```
1. Scan QR code
2. Coba scan lagi dalam 2 detik
3. Verify: Scan kedua diabaikan (cooldown)
4. Verify: Console log "Scan cooldown active"
```

### Test Case 5: Manual Reset
```
1. Saat scanner ready
2. Klik tombol "Reset"
3. Verify: Scanner pause dan resume
4. Verify: Status tetap ready
```

## File yang Dimodifikasi

### Backend
- `app/Livewire/Scanner.php`
  - Simplified event dispatching
  - Added logging for debugging
  - Removed complex reset logic

### Frontend
- `resources/views/livewire/scanner.blade.php`
  - Simplified JavaScript logic
  - Added scan cooldown mechanism
  - Added status check before scan
  - Added delayed resume
  - Improved event listeners

## Dependencies

### JavaScript Libraries
- **html5-qrcode** v2.3.8: QR code scanning library
  - Source: https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js

### Laravel Packages
- **Livewire** v3.x: Full-stack framework
  - Events: `dispatch()`, `Livewire.on()`
  - Component communication

### Browser APIs
- **MediaDevices API**: Camera access
- **getUserMedia**: Video stream

## Best Practices Applied

### 1. Event-Driven Architecture
Menggunakan Livewire events untuk komunikasi component yang loose-coupled.

### 2. Debouncing/Cooldown
Mencegah rapid fire events dengan cooldown period.

### 3. State Management
Proper state checking sebelum action (`isProcessing`, `status`).

### 4. Error Handling
Comprehensive error handling dengan logging.

### 5. User Feedback
Clear visual feedback untuk setiap state (ready, scanning, success, error).

## Performance Considerations

### Scanner Configuration
```javascript
const config = {
    fps: 10,              // 10 frames per second (balance antara performance dan accuracy)
    qrbox: { 
        width: 250, 
        height: 250 
    },
    aspectRatio: 1.0
};
```

### Cooldown Period
- **2 seconds**: Cukup untuk mencegah duplicate scan
- **Not too long**: User tidak perlu menunggu lama untuk scan berikutnya

### Resume Delay
- **500ms**: Cukup untuk UI update dan QR keluar dari view
- **Not too long**: User experience tetap smooth

## Security Considerations

### Input Validation
```php
// Validate QR data length
if (empty($qrData) || strlen($qrData) > 1000) {
    return error;
}

// Sanitize input
$qrData = strip_tags($qrData);
```

### Authentication
```php
// Get authenticated scanner
$scanner = Auth::user();
```

### Audit Trail
```php
// Log every scan attempt
\Log::info('Scanner: QR scan started', [
    'qr_length' => strlen($qrData)
]);
```

## Future Improvements

1. **Offline Support**: Cache scans when offline, sync when online
2. **Batch Scanning**: Scan multiple QR codes in sequence
3. **Sound Feedback**: Different sounds for success/error
4. **Vibration**: Haptic feedback on mobile devices
5. **Statistics**: Real-time scan statistics on scanner page
6. **Multi-Camera**: Support untuk multiple cameras
7. **QR History**: Show recent scans on scanner page

## References

### Livewire Documentation
- Events: https://livewire.laravel.com/docs/events
- JavaScript: https://livewire.laravel.com/docs/javascript
- Testing: https://livewire.laravel.com/docs/testing

### html5-qrcode
- GitHub: https://github.com/mebjas/html5-qrcode
- Documentation: https://scanapp.org/html5-qrcode-docs/

### Best Practices
- Debouncing: https://css-tricks.com/debouncing-throttling-explained-examples/
- State Management: https://kentcdodds.com/blog/application-state-management-with-react
