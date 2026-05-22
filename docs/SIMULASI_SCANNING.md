# LAPORAN SIMULASI SCANNING KEHADIRAN WISUDAWAN

## Ringkasan

Sistem telah dilengkapi dengan **simulasi komprehensif** untuk menguji berbagai scenario scanning kehadiran wisudawan oleh tim admin.

## Fitur Simulasi

### Scenarios yang Tersedia:

#### 1. **full** (Default)
Menjalankan SEMUA scenario test secara berurutan:
- Validasi QR Code
- Duplicate Scan Prevention
- Tiket Expired
- Attendance Manual
- Bulk Scanning

**Perintah:**
```bash
php artisan simulate:attendance full
```

#### 2. **validation** 
Test validasi QR code dengan berbagai kondisi:
- QR Code Kosong
- QR Code Random/Invalid
- QR Code Format Salah
- QR Code Valid

**Perintah:**
```bash
php artisan simulate:attendance validation
```

#### 3. **duplicate**
Test pencegahan scan ganda (double scan):
- Scan pertama (berhasil)
- Scan kedua (gagal - duplicate)
- Verifikasi hanya 1 record di database

**Perintah:**
```bash
php artisan simulate:attendance duplicate
```

#### 4. **expired**
Test tiket yang sudah expired:
- Generate tiket
- Set expired date ke masa lalu
- Scan (harus ditolak)

**Perintah:**
```bash
php artisan simulate:attendance expired
```

#### 5. **manual**
Test attendance manual tanpa QR code:
- Input NPM mahasiswa
- Scan manual oleh admin
- Test duplicate manual

**Perintah:**
```bash
php artisan simulate:attendance manual
```

#### 6. **bulk**
Test scanning massal:
- Generate N mahasiswa (default 5)
- Generate tiket untuk semua
- Scan semua secara otomatis
- Laporan persentase kehadiran

**Perintah:**
```bash
php artisan simulate:attendance bulk 10
```

## Cara Penggunaan di Server

### Langkah 1: Pull Update
```bash
cd /www/wwwroot/wisuda.usbypkp.ac.id/aplikasi-absensi-wisuda
sudo git pull
php artisan view:clear
```

### Langkah 2: Jalankan Simulasi

**Simulasi Lengkap:**
```bash
php artisan simulate:attendance full
```

**Simulasi Validasi QR:**
```bash
php artisan simulate:attendance validation
```

**Simulasi 50 Mahasiswa (Bulk):**
```bash
php artisan simulate:attendance bulk 50
```

### Langkah 3: Lihat Hasil

Output akan menampilkan:
1. Status setup (Admin, Event)
2. Hasil test per scenario
3. Ringkasan akhir dalam bentuk tabel
4. Statistik pass/fail
5. Total attendance record

### Output Example:

```
╔════════════════════════════════════════════════════════════╗
║                   RINGKASAN TEST                          ║
╚════════════════════════════════════════════════════════════╝

+----------------------------------+--------+----------+---------+----------------+
| Test                             | Status | Expected | Actual  | Message        |
+----------------------------------+--------+----------+---------+----------------+
| QR kosong harus ditolak          | PASS   | Failed   | Failed  | QR Code tidak..|
| QR invalid harus ditolak         | PASS   | Failed   | Failed  | QR Code tidak..|
| QR valid harus diterima          | PASS   | Success  | Success | Absensi berhasil|
| Scan pertama harus berhasil      | PASS   | Success  | Success | Absensi berhasil|
| Scan kedua harus ditolak         | PASS   | Failed   | Failed  | Sudah melakukan..|
| Tiket expired harus ditolak      | PASS   | Failed   | Failed  | Tiket sudah..  |
+----------------------------------+--------+----------+---------+----------------+

Total Test: 6
Pass: 6 ✅
Fail: 0 ❌
Persentase: 100%
```

## Keamanan

- Command **tidak bisa dijalankan di production** (auto-block)
- Data simulasi bisa dihapus (rollback) setelah test
- Tidak mempengaruhi data production

## Troubleshooting

### Error: "Command ini tidak boleh dijalankan di production"
**Solusi:** Pastikan environment bukan production. Check `.env`:
```
APP_ENV=local  # atau testing
```

### Error: "Connection refused"
**Solusi:** Pastikan MySQL/MariaDB berjalan:
```bash
sudo systemctl status mysql
```

### Error: "no such table"
**Solusi:** Jalankan migration:
```bash
php artisan migrate
```
