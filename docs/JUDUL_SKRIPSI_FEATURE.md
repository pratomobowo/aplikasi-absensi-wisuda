# Fitur Judul Skripsi/Thesis Mahasiswa

## Deskripsi
Fitur ini menambahkan field judul skripsi/thesis untuk data mahasiswa dan mengubah tampilan tabel data wisudawan di frontend.

## Perubahan yang Dilakukan

### 1. Database
- **Migration**: `2025_11_06_032034_add_judul_skripsi_to_mahasiswa_table.php`
- Menambahkan kolom `judul_skripsi` (text, nullable) pada tabel `mahasiswa`

### 2. Model
- **File**: `app/Models/Mahasiswa.php`
- Menambahkan `judul_skripsi` ke dalam array `$fillable`

### 3. Filament Resource
- **File**: `app/Filament/Resources/MahasiswaResource.php`
- Menambahkan form textarea untuk judul skripsi (opsional, max 500 karakter)
- Menambahkan kolom judul skripsi di tabel admin (hidden by default, limit 50 karakter)

### 4. Import/Export
- **File Import**: `app/Imports/MahasiswaImport.php`
  - Menambahkan field `judul_skripsi` ke dalam proses import
  - Menambahkan validasi untuk judul skripsi (nullable, max 500 karakter)
  
- **File Export**: `app/Exports/MahasiswaTemplateExport.php`
  - Menambahkan kolom `judul_skripsi` di template Excel dengan contoh data
  
- **CSV Template**: `public/templates/mahasiswa-import-template.csv`
  - Menambahkan kolom `judul_skripsi` dengan contoh data

### 5. Tampilan Data Wisudawan (Frontend)
- **File**: `resources/views/livewire/data-wisudawan.blade.php`
- **Perubahan Kolom Tabel**:
  - ❌ Dihapus: Fakultas, Nomor Kursi, Email
  - ✅ Ditambahkan: IPK, Yudisium, Judul Skripsi/Thesis
  
- **Kolom Baru**:
  1. **No** - Nomor urut
  2. **NPM** - Nomor Pokok Mahasiswa
  3. **Nama** - Nama lengkap dengan avatar
  4. **Program Studi** - Program studi mahasiswa
  5. **IPK** - Indeks Prestasi Kumulatif (format 2 desimal, bold)
  6. **Yudisium** - Badge dengan warna berbeda:
     - Cum Laude: Yellow badge
     - Sangat Memuaskan: Green badge
     - Memuaskan: Blue badge
  7. **Judul Skripsi/Thesis** - Ditampilkan dengan line-clamp-2 (maksimal 2 baris)

## Cara Penggunaan

### Input Manual (Admin Panel)
1. Login ke admin panel
2. Buka menu Mahasiswa
3. Edit data mahasiswa
4. Isi field "Judul Skripsi/Thesis" (opsional, max 500 karakter)
5. Simpan

### Import via Excel/CSV
1. Download template import dari admin panel
2. Isi kolom `judul_skripsi` (opsional, bisa dikosongkan)
3. Upload file ke sistem
4. Sistem akan mengimport data termasuk judul skripsi

## Validasi
- Field judul skripsi bersifat **opsional** (nullable)
- Maksimal 500 karakter
- Jika tidak diisi, akan ditampilkan sebagai "-"

## Tampilan

### Admin Panel
- Field textarea dengan 3 baris
- Helper text: "Judul skripsi/tugas akhir (opsional)"
- Kolom tabel dengan limit 50 karakter (hidden by default)

### Data Wisudawan (Frontend)
- Ditampilkan di kolom terakhir tabel
- Line-clamp-2: Maksimal 2 baris, sisanya dipotong dengan ellipsis (...)
- Tooltip menampilkan judul lengkap saat hover
- Menampilkan "-" jika tidak ada judul skripsi

### Styling Yudisium
- **Dengan Pujian**: Badge kuning (bg-yellow-100 text-yellow-800)
- **Sangat Memuaskan**: Badge hijau (bg-green-100 text-green-800)
- **Memuaskan**: Badge biru (bg-blue-100 text-blue-800)

## Catatan
- Judul skripsi tidak wajib diisi
- Dapat diupdate kapan saja melalui admin panel atau import ulang
- Tidak mempengaruhi data yang sudah ada (backward compatible)
- Tampilan tabel data wisudawan lebih fokus pada informasi akademik
