# QR Code - Plain JSON Format

## Perubahan

Sistem QR code telah diubah dari format terenkripsi menjadi **plain JSON** untuk kemudahan scanning dan debugging.

## Format Token

Token QR code sekarang menggunakan format JSON sederhana:

```json
{
    "ticket_id": 1,
    "role": "mahasiswa",
    "event_id": 1,
    "timestamp": "2025-10-29T04:07:02+00:00"
}
```

## Keuntungan

✅ **Mudah di-scan**: QR code lebih sederhana dan mudah dibaca oleh webcam  
✅ **Mudah di-debug**: Token dapat dibaca langsung tanpa perlu dekripsi  
✅ **Lebih cepat**: Tidak ada overhead enkripsi/dekripsi  
✅ **Lebih reliable**: Scanning lebih stabil dan konsisten  

## Keamanan

Meskipun token tidak terenkripsi, sistem tetap aman karena:

- Token hanya berisi ID referensi (ticket_id, event_id)
- Validasi dilakukan di backend terhadap database
- Token tidak mengandung data sensitif
- Duplicate check mencegah penyalahgunaan
- Timestamp untuk referensi waktu

## Implementasi

### Generate Token

```php
// app/Services/QRCodeService.php
public function encryptQRData(array $data): string
{
    $data['timestamp'] = now()->toIso8601String();
    return json_encode($data);
}
```

**Catatan**: Saat membuat ticket baru, QR tokens di-generate dalam dua tahap:
1. Placeholder `{}` disimpan saat insert pertama (karena kolom required)
2. Token asli dengan ticket_id yang benar di-generate dan di-update setelah save

### Decode Token

```php
// app/Services/QRCodeService.php
public function decryptQRData(string $token): ?array
{
    try {
        $data = json_decode($token, true);
        if (!is_array($data)) {
            return null;
        }
        return $data;
    } catch (\Exception $e) {
        return null;
    }
}
```

## Alur QR Code

### 1. Generate Ticket
```
TicketService::createTicket()
  ↓
TicketService::generateQRTokens()
  ↓
QRCodeService::encryptQRData([...])
  ↓
JSON token disimpan ke database
```

### 2. Display QR Code
```
InvitationController::show()
  ↓
Ambil JSON token dari database
  ↓
QRCodeService::generateQRCode(token)
  ↓
QR code image ditampilkan
```

### 3. Scan QR Code
```
Scanner membaca QR code
  ↓
Kirim JSON token ke backend
  ↓
AttendanceService::recordAttendance()
  ↓
QRCodeService::decryptQRData(token)
  ↓
Validasi dan catat attendance
```

## Validasi

Validasi dilakukan di `AttendanceService::validateQRCode()`:

1. **Format Check**: Validasi panjang dan format JSON
2. **Decoding**: Parse JSON token
3. **Structure Validation**: Cek field required (ticket_id, role, event_id)
4. **Database Lookup**: Verifikasi ticket exists dan valid
5. **Duplicate Check**: Cek apakah sudah absen sebelumnya

## Migrasi dari Sistem Lama

Jika Anda memiliki token terenkripsi dari sistem lama, Anda perlu:

1. Generate ulang semua QR tokens dengan format baru
2. Update database dengan token baru
3. Distribusikan ulang undangan ke mahasiswa

---

**Tanggal**: 29 Oktober 2025  
**Status**: ✅ Active  
**Format**: Plain JSON
