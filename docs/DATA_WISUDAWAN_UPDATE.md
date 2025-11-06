# Update Data Wisudawan - Perubahan Lengkap

## Overview
Dokumen ini merangkum semua perubahan yang dilakukan pada sistem untuk menambahkan field baru dan mengubah tampilan data wisudawan.

## Fitur Baru yang Ditambahkan

### 1. Nomor Kursi
- **Tipe Data**: VARCHAR(20), nullable
- **Fungsi**: Menyimpan informasi nomor kursi mahasiswa saat wisuda
- **Tampilan**: 
  - Admin panel: Input text field
  - Undangan web/PDF: Ditampilkan di informasi mahasiswa
  - Data wisudawan: ~~Ditampilkan dengan badge~~ (dihapus dari tampilan publik)

### 2. Judul Skripsi/Thesis
- **Tipe Data**: TEXT, nullable
- **Fungsi**: Menyimpan judul skripsi atau thesis mahasiswa
- **Tampilan**:
  - Admin panel: Textarea (3 baris, max 500 karakter)
  - Data wisudawan: Ditampilkan dengan line-clamp-2 (max 2 baris)

## Perubahan Tampilan Data Wisudawan

### Sebelum
Kolom yang ditampilkan:
1. No
2. NPM
3. Nama
4. Fakultas
5. Program Studi
6. Nomor Kursi
7. Email

### Sesudah
Kolom yang ditampilkan:
1. No
2. NPM
3. Nama (dengan avatar)
4. Program Studi
5. **IPK** (baru, format 2 desimal, bold)
6. **Yudisium** (baru, dengan badge berwarna)
7. **Judul Skripsi/Thesis** (baru, line-clamp-2)

## File yang Dimodifikasi

### Database Migrations
1. `2025_11_06_022332_add_nomor_kursi_to_mahasiswa_table.php`
2. `2025_11_06_032034_add_judul_skripsi_to_mahasiswa_table.php`

### Models
- `app/Models/Mahasiswa.php`
  - Menambahkan `nomor_kursi` dan `judul_skripsi` ke `$fillable`

### Filament Resources
- `app/Filament/Resources/MahasiswaResource.php`
  - Form: Menambahkan input nomor_kursi dan textarea judul_skripsi
  - Table: Menambahkan kolom nomor_kursi dan judul_skripsi (hidden by default)

### Import/Export
- `app/Imports/MahasiswaImport.php`
  - Menambahkan field nomor_kursi dan judul_skripsi
  - Menambahkan validasi untuk kedua field
  
- `app/Exports/MahasiswaTemplateExport.php`
  - Menambahkan kolom nomor_kursi dan judul_skripsi di template
  
- `public/templates/mahasiswa-import-template.csv`
  - Update template dengan kolom baru

### Views
- `resources/views/invitation/show.blade.php`
  - Menambahkan nomor kursi di informasi mahasiswa
  
- `resources/views/pdf/invitation.blade.php`
  - Menambahkan nomor kursi di PDF undangan
  
- `resources/views/livewire/data-wisudawan.blade.php`
  - **Perubahan besar**: Mengubah struktur tabel
  - Menghapus kolom: Fakultas, Nomor Kursi, Email
  - Menambahkan kolom: IPK, Yudisium, Judul Skripsi
  - Styling: Badge untuk yudisium, line-clamp untuk judul skripsi

## Struktur Database Mahasiswa (Terbaru)

```
mahasiswa
├── id (bigint, primary key)
├── npm (varchar 20, unique)
├── nama (varchar 255)
├── program_studi (varchar 255)
├── fakultas (varchar 255)
├── ipk (decimal 3,2)
├── yudisium (varchar 255, nullable)
├── email (varchar 255, nullable)
├── phone (varchar 20, nullable)
├── nomor_kursi (varchar 20, nullable) ← BARU
├── judul_skripsi (text, nullable) ← BARU
├── created_at (timestamp)
└── updated_at (timestamp)
```

## Validasi

### Nomor Kursi
- Nullable (opsional)
- Max 20 karakter
- Default display: "-"

### Judul Skripsi
- Nullable (opsional)
- Max 500 karakter
- Default display: "-"

## Styling Yudisium (Frontend)

```php
Dengan Pujian      → Yellow badge (bg-yellow-100 text-yellow-800)
Sangat Memuaskan   → Green badge (bg-green-100 text-green-800)
Memuaskan          → Blue badge (bg-blue-100 text-blue-800)
```

## Testing

### Database
```bash
php artisan migrate:status
# Verifikasi kedua migration sudah ran

php artisan tinker --execute="echo json_encode(Schema::getColumnListing('mahasiswa'), JSON_PRETTY_PRINT);"
# Verifikasi kolom nomor_kursi dan judul_skripsi ada
```

### Data Sample
```bash
php artisan tinker --execute="
\$mhs = App\Models\Mahasiswa::first();
\$mhs->nomor_kursi = 'A-001';
\$mhs->judul_skripsi = 'Sistem Informasi Manajemen Berbasis Web';
\$mhs->save();
"
```

## Backward Compatibility
- ✅ Data lama tetap berfungsi (field baru nullable)
- ✅ Import lama tetap bisa digunakan (field baru opsional)
- ✅ Tidak ada breaking changes pada API atau service

## Dokumentasi Terkait
- `docs/NOMOR_KURSI_FEATURE.md` - Detail fitur nomor kursi
- `docs/JUDUL_SKRIPSI_FEATURE.md` - Detail fitur judul skripsi
- `docs/DATA_WISUDAWAN_UPDATE.md` - Dokumen ini

## Catatan Penting
1. Kedua field baru bersifat opsional
2. Tampilan data wisudawan lebih fokus pada informasi akademik
3. Nomor kursi tetap ditampilkan di undangan (web & PDF)
4. Judul skripsi hanya ditampilkan di halaman data wisudawan publik
5. Admin dapat toggle visibility kolom di admin panel
