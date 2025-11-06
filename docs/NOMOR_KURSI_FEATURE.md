# Fitur Nomor Kursi Mahasiswa

## Deskripsi
Fitur ini menambahkan field nomor kursi untuk data mahasiswa yang berfungsi sebagai informasi nomor kursi saat wisuda.

## Perubahan yang Dilakukan

### 1. Database
- **Migration**: `2025_11_06_022332_add_nomor_kursi_to_mahasiswa_table.php`
- Menambahkan kolom `nomor_kursi` (varchar 20, nullable) pada tabel `mahasiswa`

### 2. Model
- **File**: `app/Models/Mahasiswa.php`
- Menambahkan `nomor_kursi` ke dalam array `$fillable`

### 3. Filament Resource
- **File**: `app/Filament/Resources/MahasiswaResource.php`
- Menambahkan form input untuk nomor kursi (opsional)
- Menambahkan kolom nomor kursi di tabel (hidden by default)
- Default value: "-" jika tidak ada nomor kursi

### 4. Import/Export
- **File Import**: `app/Imports/MahasiswaImport.php`
  - Menambahkan field `nomor_kursi` ke dalam proses import
  - Menambahkan validasi untuk nomor kursi (nullable, max 20 karakter)
  
- **File Export**: `app/Exports/MahasiswaTemplateExport.php`
  - Menambahkan kolom `nomor_kursi` di template Excel
  - Format kolom sebagai text
  
- **CSV Template**: `public/templates/mahasiswa-import-template.csv`
  - Menambahkan kolom `nomor_kursi` dengan contoh data

### 5. Tampilan Undangan
- **File Web**: `resources/views/invitation/show.blade.php`
  - Menampilkan nomor kursi di informasi mahasiswa
  - Menampilkan "-" jika nomor kursi tidak ada
  
- **File PDF**: `resources/views/pdf/invitation.blade.php`
  - Menampilkan nomor kursi di informasi mahasiswa pada PDF
  - Menampilkan "-" jika nomor kursi tidak ada

### 6. Halaman Publik
- **File**: `resources/views/livewire/data-wisudawan.blade.php`
  - Menambahkan kolom nomor kursi di tabel data wisudawan
  - Menampilkan nomor kursi dengan badge styling
  - Menampilkan "-" jika nomor kursi tidak ada

## Cara Penggunaan

### Input Manual (Admin Panel)
1. Login ke admin panel
2. Buka menu Mahasiswa
3. Edit data mahasiswa
4. Isi field "Nomor Kursi" (opsional)
5. Simpan

### Import via Excel/CSV
1. Download template import dari admin panel
2. Isi kolom `nomor_kursi` (opsional, bisa dikosongkan)
3. Upload file ke sistem
4. Sistem akan mengimport data termasuk nomor kursi

## Validasi
- Field nomor kursi bersifat **opsional** (nullable)
- Maksimal 20 karakter
- Jika tidak diisi, akan ditampilkan sebagai "-"

## Tampilan
- **Admin Panel**: Field input text dengan helper text
- **Undangan Web**: Ditampilkan di grid informasi mahasiswa
- **Undangan PDF**: Ditampilkan di tabel informasi mahasiswa
- **Data Wisudawan**: Ditampilkan dengan badge styling di tabel

## Catatan
- Nomor kursi tidak wajib diisi
- Dapat diupdate kapan saja melalui admin panel atau import ulang
- Tidak mempengaruhi data yang sudah ada (backward compatible)
