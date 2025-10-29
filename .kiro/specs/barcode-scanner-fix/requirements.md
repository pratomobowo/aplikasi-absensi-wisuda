# Requirements Document

## Introduction

Sistem scan barcode saat ini mengalami masalah looping dan gagal terus saat memindai, meskipun data sudah ada di database. Fitur ini bertujuan untuk menyederhanakan implementasi scan barcode agar lebih stabil, reliable, dan mudah di-debug. Fokus utama adalah menghilangkan kompleksitas yang tidak perlu, memperbaiki flow scanning, dan memastikan validasi data berjalan dengan benar.

## Glossary

- **Scanner System**: Sistem pemindaian QR code untuk mencatat kehadiran wisudawan dan pendamping
- **QR Code**: Kode QR yang berisi data terenkripsi untuk identifikasi tiket wisuda
- **Livewire Component**: Komponen Scanner yang menangani interaksi frontend-backend
- **AttendanceService**: Service layer yang menangani logika bisnis pencatatan kehadiran
- **Scan Loop**: Kondisi dimana scanner terus memindai ulang QR code yang sama tanpa henti
- **Cooldown Period**: Periode waktu tunggu antara scan untuk mencegah duplikasi
- **Html5-QRCode Library**: Library JavaScript untuk scanning QR code menggunakan kamera

## Requirements

### Requirement 1

**User Story:** Sebagai petugas scanner, saya ingin sistem dapat memindai QR code dengan stabil tanpa looping, sehingga proses absensi berjalan lancar

#### Acceptance Criteria

1. WHEN Scanner System menerima QR code yang valid, THE Scanner System SHALL memproses QR code tersebut tepat satu kali
2. WHEN Scanner System sedang memproses QR code, THE Scanner System SHALL menghentikan sementara proses scanning untuk mencegah scan ganda
3. WHEN Scanner System selesai memproses QR code, THE Scanner System SHALL menunggu minimal 3 detik sebelum kembali ke mode ready
4. WHEN Scanner System kembali ke mode ready, THE Scanner System SHALL melanjutkan scanning dengan state yang bersih
5. IF Scanner System mendeteksi QR code yang sama dalam waktu 5 detik, THEN THE Scanner System SHALL mengabaikan scan tersebut

### Requirement 2

**User Story:** Sebagai petugas scanner, saya ingin melihat pesan error yang jelas ketika scan gagal, sehingga saya dapat memahami penyebab kegagalan

#### Acceptance Criteria

1. WHEN validasi QR code gagal, THE Scanner System SHALL menampilkan pesan error yang spesifik dan mudah dipahami
2. WHEN data tidak ditemukan di database, THE Scanner System SHALL menampilkan pesan "Data tidak ditemukan di database"
3. WHEN terjadi duplikasi absensi, THE Scanner System SHALL menampilkan pesan "Sudah melakukan absensi sebelumnya"
4. WHEN QR code tidak valid atau rusak, THE Scanner System SHALL menampilkan pesan "QR Code tidak valid atau rusak"
5. THE Scanner System SHALL mencatat semua error ke log file untuk debugging

### Requirement 3

**User Story:** Sebagai developer, saya ingin proses validasi QR code yang sederhana dan mudah di-debug, sehingga masalah dapat diidentifikasi dengan cepat

#### Acceptance Criteria

1. THE AttendanceService SHALL memvalidasi format QR code sebelum dekripsi
2. THE AttendanceService SHALL mencatat setiap langkah validasi ke log dengan level yang sesuai
3. WHEN dekripsi QR code gagal, THE AttendanceService SHALL mencatat raw data (sebagian) untuk debugging
4. THE AttendanceService SHALL memvalidasi keberadaan data di database sebelum mencatat kehadiran
5. THE AttendanceService SHALL menggunakan database transaction untuk memastikan konsistensi data

### Requirement 4

**User Story:** Sebagai petugas scanner, saya ingin scanner dapat pulih otomatis dari error, sehingga saya tidak perlu reload halaman setiap kali terjadi masalah

#### Acceptance Criteria

1. WHEN Scanner System mengalami error, THE Scanner System SHALL menampilkan pesan error selama 3 detik
2. WHEN periode tampilan error selesai, THE Scanner System SHALL kembali ke mode ready secara otomatis
3. WHEN Scanner System kembali ke mode ready, THE Scanner System SHALL membersihkan state error sebelumnya
4. THE Scanner System SHALL menyediakan tombol "Reset Manual" untuk memaksa kembali ke mode ready
5. WHEN tombol reset manual ditekan, THE Scanner System SHALL membersihkan semua state dan memulai ulang scanner

### Requirement 5

**User Story:** Sebagai administrator sistem, saya ingin dapat memonitor aktivitas scanning, sehingga saya dapat mengidentifikasi pola masalah

#### Acceptance Criteria

1. THE Scanner System SHALL mencatat setiap scan attempt dengan timestamp, user, dan hasil
2. THE Scanner System SHALL mencatat durasi proses scanning dari mulai hingga selesai
3. WHEN scan gagal, THE Scanner System SHALL mencatat alasan kegagalan secara detail
4. THE Scanner System SHALL mencatat informasi browser dan device untuk troubleshooting
5. THE Scanner System SHALL menyimpan log dalam format yang mudah dianalisis

### Requirement 6

**User Story:** Sebagai petugas scanner, saya ingin feedback visual yang jelas saat scanning, sehingga saya tahu status proses scanning

#### Acceptance Criteria

1. WHEN Scanner System dalam mode ready, THE Scanner System SHALL menampilkan indikator "Siap Memindai" dengan warna biru
2. WHEN Scanner System sedang memproses, THE Scanner System SHALL menampilkan overlay loading dengan animasi
3. WHEN scan berhasil, THE Scanner System SHALL menampilkan layar sukses dengan warna hijau dan data mahasiswa
4. WHEN scan gagal, THE Scanner System SHALL menampilkan layar error dengan warna merah dan pesan error
5. THE Scanner System SHALL menggunakan animasi smooth untuk transisi antar state
