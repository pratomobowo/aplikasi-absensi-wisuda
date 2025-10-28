# Requirements Document

## Introduction

Sistem ini memerlukan perubahan pada struktur data mahasiswa untuk mengganti field NIM menjadi NPM dan menambahkan field Yudisium. Selain itu, sistem memerlukan fitur import data mahasiswa melalui file Excel (.xls/.xlsx) untuk memudahkan input data secara massal.

## Glossary

- **Mahasiswa System**: Sistem manajemen data mahasiswa dalam aplikasi wisuda
- **NPM**: Nomor Pokok Mahasiswa, pengganti dari NIM sebagai identifier unik mahasiswa
- **Yudisium**: Status kelulusan akademik mahasiswa (contoh: Cum Laude, Sangat Memuaskan, Memuaskan)
- **Excel Import**: Fitur untuk mengupload dan memproses file Excel yang berisi data mahasiswa
- **Filament Resource**: Interface admin panel untuk mengelola data mahasiswa
- **Database Migration**: Proses perubahan struktur database

## Requirements

### Requirement 1

**User Story:** Sebagai administrator, saya ingin field identitas mahasiswa menggunakan NPM (bukan NIM), sehingga sesuai dengan standar institusi

#### Acceptance Criteria

1. THE Mahasiswa System SHALL menyimpan NPM sebagai identifier unik untuk setiap mahasiswa
2. THE Mahasiswa System SHALL menampilkan label "NPM" pada semua form dan tampilan data mahasiswa
3. THE Mahasiswa System SHALL memvalidasi format NPM sesuai dengan aturan institusi
4. THE Mahasiswa System SHALL memigrasikan data NIM yang ada menjadi NPM tanpa kehilangan data
5. THE Mahasiswa System SHALL memastikan NPM bersifat unik untuk setiap mahasiswa

### Requirement 2

**User Story:** Sebagai administrator, saya ingin menyimpan data Yudisium mahasiswa, sehingga informasi status kelulusan akademik tercatat dengan lengkap

#### Acceptance Criteria

1. THE Mahasiswa System SHALL menyimpan field Yudisium untuk setiap mahasiswa
2. THE Mahasiswa System SHALL menampilkan field Yudisium pada form input dan edit mahasiswa
3. THE Mahasiswa System SHALL menampilkan Yudisium pada daftar dan detail mahasiswa
4. THE Mahasiswa System SHALL mengizinkan field Yudisium bersifat opsional (nullable)
5. THE Mahasiswa System SHALL menyediakan pilihan Yudisium yang standar (Cum Laude, Sangat Memuaskan, Memuaskan)

### Requirement 3

**User Story:** Sebagai administrator, saya ingin struktur data mahasiswa mencakup semua field yang diperlukan (NPM, Nama, Prodi, Fakultas, IPK, Yudisium), sehingga data mahasiswa tersimpan lengkap

#### Acceptance Criteria

1. THE Mahasiswa System SHALL menyimpan field NPM, Nama, Prodi, Fakultas, IPK, dan Yudisium untuk setiap mahasiswa
2. THE Mahasiswa System SHALL memvalidasi bahwa field wajib (NPM, Nama, Prodi, Fakultas, IPK) terisi sebelum menyimpan data
3. THE Mahasiswa System SHALL menampilkan semua field pada form create dan edit mahasiswa
4. THE Mahasiswa System SHALL menampilkan semua field pada tabel daftar mahasiswa
5. THE Mahasiswa System SHALL memformat IPK dengan 2 desimal (contoh: 3.75)

### Requirement 4

**User Story:** Sebagai administrator, saya ingin mengupload file Excel untuk import data mahasiswa secara massal, sehingga tidak perlu input data satu per satu

#### Acceptance Criteria

1. WHEN administrator mengakses halaman daftar mahasiswa, THE Mahasiswa System SHALL menampilkan tombol "Import Excel"
2. WHEN administrator mengklik tombol import, THE Mahasiswa System SHALL menampilkan dialog upload file
3. THE Mahasiswa System SHALL menerima file dengan format .xls dan .xlsx
4. THE Mahasiswa System SHALL memvalidasi struktur file Excel sebelum memproses data
5. IF file Excel tidak sesuai format, THEN THE Mahasiswa System SHALL menampilkan pesan error yang jelas

### Requirement 5

**User Story:** Sebagai administrator, saya ingin sistem memproses dan menyimpan data dari file Excel yang diupload, sehingga data mahasiswa tersimpan ke database

#### Acceptance Criteria

1. WHEN file Excel valid diupload, THE Mahasiswa System SHALL membaca semua baris data dari file
2. THE Mahasiswa System SHALL memvalidasi setiap baris data sesuai dengan aturan validasi mahasiswa
3. THE Mahasiswa System SHALL menyimpan data yang valid ke database
4. IF terdapat data duplikat NPM, THEN THE Mahasiswa System SHALL melewati atau mengupdate data tersebut sesuai konfigurasi
5. WHEN proses import selesai, THE Mahasiswa System SHALL menampilkan ringkasan hasil import (jumlah berhasil, gagal, duplikat)

### Requirement 6

**User Story:** Sebagai administrator, saya ingin melihat feedback yang jelas saat proses import, sehingga saya tahu status dan hasil dari proses import

#### Acceptance Criteria

1. WHILE proses import berjalan, THE Mahasiswa System SHALL menampilkan indikator loading atau progress
2. WHEN import selesai, THE Mahasiswa System SHALL menampilkan notifikasi sukses dengan jumlah data yang berhasil diimport
3. IF terdapat error pada beberapa baris, THE Mahasiswa System SHALL menampilkan detail baris yang error beserta alasan error
4. THE Mahasiswa System SHALL menyediakan opsi untuk download laporan error dalam format yang mudah dibaca
5. WHEN import gagal total, THE Mahasiswa System SHALL menampilkan pesan error yang informatif

### Requirement 7

**User Story:** Sebagai administrator, saya ingin format Excel yang jelas untuk import data, sehingga saya tahu struktur data yang harus diupload

#### Acceptance Criteria

1. THE Mahasiswa System SHALL menyediakan tombol "Download Template" untuk mengunduh template Excel
2. THE Mahasiswa System SHALL menghasilkan file template dengan header kolom yang sesuai (NPM, Nama, Prodi, Fakultas, IPK, Yudisium)
3. THE Mahasiswa System SHALL menyertakan contoh data pada template untuk panduan
4. THE Mahasiswa System SHALL mendokumentasikan format dan aturan validasi pada sheet terpisah atau komentar header
5. THE Mahasiswa System SHALL memastikan template dapat dibuka di Microsoft Excel dan aplikasi spreadsheet lainnya
