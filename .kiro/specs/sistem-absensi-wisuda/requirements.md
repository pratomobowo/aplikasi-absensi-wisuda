# Requirements Document

## Introduction

Sistem Absensi Wisuda Digital adalah aplikasi berbasis web yang memungkinkan pengelolaan kehadiran mahasiswa pada acara wisuda secara digital. Sistem ini menggunakan Laravel 12 sebagai backend, Filament untuk admin dashboard, dan Livewire untuk aplikasi scanner panitia. Mahasiswa akan menerima link unik yang berisi QR code untuk absensi, dan panitia dapat memindai QR code tersebut menggunakan aplikasi scanner berbasis browser di perangkat mobile mereka.

## Glossary

- **Admin System**: Sistem dashboard berbasis Filament yang digunakan oleh administrator untuk mengelola data mahasiswa dan tiket wisuda
- **Magic Link**: URL unik yang digenerate untuk setiap mahasiswa yang berisi token terenkripsi untuk mengakses halaman undangan
- **QR Code**: Quick Response code yang berisi informasi terenkripsi tentang identitas mahasiswa dan tiket wisuda
- **Scanner Application**: Aplikasi berbasis Livewire yang digunakan panitia untuk memindai QR code mahasiswa
- **Invitation Page**: Halaman undangan yang ditampilkan kepada mahasiswa setelah mengakses magic link
- **Attendance System**: Sistem yang mencatat dan memvalidasi kehadiran mahasiswa berdasarkan scan QR code
- **Student Record**: Data mahasiswa yang tersimpan dalam database termasuk nama, NIM, dan informasi wisuda
- **Graduation Ticket**: Tiket digital yang digenerate untuk setiap mahasiswa yang berisi 3 QR code untuk mahasiswa, pendamping 1, dan pendamping 2
- **Companion**: Pendamping mahasiswa yang ikut hadir dalam acara wisuda (maksimal 2 pendamping per mahasiswa)

## Requirements

### Requirement 1

**User Story:** Sebagai administrator, saya ingin mengelola data mahasiswa melalui dashboard admin, sehingga saya dapat menambah, mengubah, dan menghapus data mahasiswa yang akan mengikuti wisuda

#### Acceptance Criteria

1. THE Admin System SHALL provide a CRUD interface for managing Student Records
2. WHEN an administrator creates a new Student Record, THE Admin System SHALL automatically generate a Magic Link for that student
3. THE Admin System SHALL validate that required fields (nama, NIM, program studi) are filled before saving a Student Record
4. THE Admin System SHALL display a list of all Student Records with search and filter capabilities
5. WHEN an administrator updates a Student Record, THE Admin System SHALL preserve the existing Magic Link unless explicitly regenerated

### Requirement 2

**User Story:** Sebagai administrator, saya ingin sistem secara otomatis men-generate link unik untuk setiap mahasiswa, sehingga setiap mahasiswa memiliki akses personal ke halaman undangan mereka

#### Acceptance Criteria

1. WHEN a new Student Record is created, THE Admin System SHALL generate a unique encrypted token for the Magic Link
2. THE Admin System SHALL ensure that each Magic Link is unique and cannot be predicted or duplicated
3. THE Admin System SHALL store the Magic Link token in the database associated with the Student Record
4. THE Admin System SHALL provide a copy function to easily copy the Magic Link for distribution
5. THE Magic Link SHALL remain valid until the graduation event is completed or manually revoked by administrator

### Requirement 3

**User Story:** Sebagai administrator, saya ingin mendistribusikan link undangan kepada mahasiswa melalui WhatsApp, sehingga mahasiswa dapat dengan mudah mengakses halaman undangan mereka

#### Acceptance Criteria

1. THE Admin System SHALL provide a formatted WhatsApp message template containing the Magic Link
2. THE Admin System SHALL provide a button to open WhatsApp with pre-filled message for each Student Record
3. THE Admin System SHALL support bulk export of Magic Links with student information for mass distribution
4. THE Admin System SHALL log when a Magic Link has been distributed to track communication status
5. THE Admin System SHALL allow administrators to resend Magic Links if needed

### Requirement 4

**User Story:** Sebagai mahasiswa, saya ingin membuka link unik yang saya terima dan melihat halaman undangan dengan QR code, sehingga saya dapat menunjukkan QR code tersebut saat absensi di acara wisuda

#### Acceptance Criteria

1. WHEN a student accesses a valid Magic Link, THE Invitation Page SHALL display student information and graduation details
2. THE Invitation Page SHALL generate and display 3 distinct QR Codes for the student
3. THE Invitation Page SHALL be responsive and display properly on mobile devices
4. IF a student accesses an invalid or expired Magic Link, THEN THE Invitation Page SHALL display an error message
5. THE Invitation Page SHALL prevent unauthorized access by validating the Magic Link token before displaying content

### Requirement 5

**User Story:** Sebagai mahasiswa, saya ingin melihat informasi lengkap tentang acara wisuda pada halaman undangan, sehingga saya mengetahui detail acara seperti jadwal, lokasi, dan peta lokasi

#### Acceptance Criteria

1. THE Invitation Page SHALL display student basic information including name, NIM, and program studi
2. THE Invitation Page SHALL display graduation event schedule including date and time
3. THE Invitation Page SHALL display graduation event location with complete address
4. THE Invitation Page SHALL embed an interactive map showing the graduation venue location
5. THE Invitation Page SHALL ensure all information is clearly formatted and easy to read on mobile devices

### Requirement 6

**User Story:** Sebagai mahasiswa, saya ingin melihat 3 QR code yang berbeda pada halaman undangan saya, sehingga saya dapat menunjukkan QR code untuk absensi mahasiswa, pendamping 1, dan pendamping 2

#### Acceptance Criteria

1. THE Invitation Page SHALL display QR Code 1 labeled "Mahasiswa" containing student identification and ticket information
2. THE Invitation Page SHALL display QR Code 2 labeled "Pendamping 1" containing companion 1 identification linked to the student
3. THE Invitation Page SHALL display QR Code 3 labeled "Pendamping 2" containing companion 2 identification linked to the student
4. THE Invitation Page SHALL clearly distinguish each QR Code with visual labels and descriptions
5. THE Invitation Page SHALL ensure all 3 QR Codes are scannable and contain valid encrypted data with their respective roles

### Requirement 7

**User Story:** Sebagai mahasiswa, saya ingin dapat mengunduh undangan dalam format PDF, sehingga saya dapat menyimpan atau mencetak undangan untuk dibawa ke acara wisuda

#### Acceptance Criteria

1. THE Invitation Page SHALL provide a download button to generate PDF version of the invitation
2. WHEN the download button is clicked, THE Invitation Page SHALL generate a PDF containing all invitation information including student data, event details, and 3 QR Codes
3. THE Invitation Page SHALL ensure the PDF is properly formatted for printing on A4 paper size
4. THE Invitation Page SHALL generate the PDF within 5 seconds of the download request
5. THE Invitation Page SHALL name the PDF file with student name and NIM for easy identification

### Requirement 8

**User Story:** Sebagai panitia, saya ingin menggunakan aplikasi scanner di browser HP saya untuk memindai QR code mahasiswa, sehingga saya dapat mencatat kehadiran mahasiswa dengan cepat dan mudah

#### Acceptance Criteria

1. THE Scanner Application SHALL access the device camera to scan QR Codes
2. WHEN the Scanner Application is opened, THE Scanner Application SHALL request camera permission from the user
3. THE Scanner Application SHALL provide a clear viewfinder for QR Code scanning
4. THE Scanner Application SHALL work on mobile browsers (Chrome, Safari) without requiring app installation
5. THE Scanner Application SHALL provide visual feedback during the scanning process

### Requirement 9

**User Story:** Sebagai panitia, saya ingin sistem memvalidasi QR code secara real-time dan menampilkan status kehadiran, sehingga saya dapat segera mengetahui apakah QR code tersebut valid dan belum digunakan sebelumnya

#### Acceptance Criteria

1. WHEN a QR Code is successfully scanned, THE Scanner Application SHALL send the scanned data to the Attendance System for validation
2. THE Attendance System SHALL verify that the scanned QR Code contains valid ticket information and identify the role (mahasiswa, pendamping 1, or pendamping 2)
3. THE Attendance System SHALL check if the specific QR Code (by role) has already been scanned and marked as present
4. IF the QR Code is valid and has not been scanned before, THEN THE Attendance System SHALL record the attendance with timestamp and role information
5. THE Scanner Application SHALL display validation results within 2 seconds of scanning

### Requirement 10

**User Story:** Sebagai panitia, saya ingin melihat feedback visual yang jelas setelah memindai QR code, sehingga saya dapat dengan cepat mengetahui apakah absensi berhasil atau gagal

#### Acceptance Criteria

1. WHEN attendance is successfully recorded, THE Scanner Application SHALL display a green success screen with student name, role (mahasiswa/pendamping 1/pendamping 2), and confirmation message
2. IF the QR Code is invalid or has already been scanned, THEN THE Scanner Application SHALL display a red error screen with appropriate error message
3. THE Scanner Application SHALL display the success or error screen for 3 seconds before returning to scanning mode
4. THE Scanner Application SHALL provide audio feedback (beep sound) for successful scans
5. THE Scanner Application SHALL display student name and role information on the success screen for visual verification

### Requirement 11

**User Story:** Sebagai administrator, saya ingin melihat laporan statistik kehadiran secara real-time, sehingga saya dapat memantau jumlah mahasiswa dan pendamping yang sudah hadir

#### Acceptance Criteria

1. THE Admin System SHALL display total number of registered students
2. THE Admin System SHALL display total number of students who have attended (QR Code Mahasiswa scanned)
3. THE Admin System SHALL display total number of Companion 1 who have attended (QR Code Pendamping 1 scanned)
4. THE Admin System SHALL display total number of Companion 2 who have attended (QR Code Pendamping 2 scanned)
5. THE Admin System SHALL update attendance statistics in real-time when new attendance is recorded

### Requirement 12

**User Story:** Sebagai administrator, saya ingin dapat mengekspor data kehadiran, sehingga saya dapat membuat laporan atau analisis lebih lanjut

#### Acceptance Criteria

1. THE Admin System SHALL provide an export function to download attendance data in CSV format
2. THE Admin System SHALL provide an export function to download attendance data in Excel format
3. THE Admin System SHALL include all relevant fields in the export (nama, NIM, waktu hadir, status)
4. THE Admin System SHALL allow filtering of export data by date range or attendance status
5. THE Admin System SHALL generate the export file within 10 seconds for up to 1000 records

### Requirement 13

**User Story:** Sebagai sistem, saya ingin memastikan keamanan data dan mencegah pemalsuan QR code, sehingga hanya mahasiswa yang sah yang dapat melakukan absensi

#### Acceptance Criteria

1. THE Attendance System SHALL encrypt all data stored in QR Codes using secure encryption algorithm
2. THE Attendance System SHALL validate the encryption signature of scanned QR Codes before processing
3. THE Attendance System SHALL prevent replay attacks by marking QR Codes as used after successful scan
4. THE Attendance System SHALL log all scan attempts including failed validations for security audit
5. THE Attendance System SHALL expire QR Codes after the graduation event date has passed
