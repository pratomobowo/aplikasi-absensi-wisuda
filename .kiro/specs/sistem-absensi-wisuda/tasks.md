# Implementation Plan - Sistem Absensi Wisuda Digital

## Task List

- [x] 1. Setup Laravel 12 project dan konfigurasi dasar
  - Install Laravel 12 dengan composer
  - Configure database connection dengan kredensial yang diberikan (DB: wisuda, User: root, Pass: Srid3v1@#14)
  - Setup .env file dengan konfigurasi yang diperlukan
  - Install dan configure Tailwind CSS
  - _Requirements: All requirements depend on this foundation_

- [x] 2. Install dan setup dependencies utama
  - Install Filament v3 dengan command `composer require filament/filament`
  - Install Livewire v3 (included with Filament)
  - Install SimpleSoftwareIO/simple-qrcode untuk QR code generation
  - Install barryvdh/laravel-dompdf untuk PDF generation
  - Configure Filament admin panel dengan `php artisan filament:install --panels`
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_

- [x] 3. Create database migrations dan schema
  - [x] 3.1 Create migration untuk table users dengan role field
    - Add role enum field ('admin', 'scanner') to users table
    - _Requirements: 6.1, 6.2_
  
  - [x] 3.2 Create migration untuk table mahasiswa
    - Fields: nim, nama, program_studi, fakultas, email, phone
    - Add unique index on nim
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_
  
  - [x] 3.3 Create migration untuk table graduation_events
    - Fields: name, date, time, location_name, location_address, location_lat, location_lng, is_active
    - _Requirements: 5.2, 5.3, 5.4_
  
  - [x] 3.4 Create migration untuk table graduation_tickets
    - Fields: mahasiswa_id, graduation_event_id, magic_link_token, qr_token_mahasiswa, qr_token_pendamping1, qr_token_pendamping2, is_distributed, distributed_at, expires_at
    - Add unique index on magic_link_token
    - Add foreign keys to mahasiswa and graduation_events
    - _Requirements: 2.1, 2.2, 2.3, 6.1, 6.2, 6.3, 6.4, 6.5_
  
  - [x] 3.5 Create migration untuk table attendances
    - Fields: graduation_ticket_id, role, scanned_by, scanned_at
    - Add unique composite index on (graduation_ticket_id, role)
    - Add foreign keys to graduation_tickets and users
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 9.1, 9.2, 9.3, 9.4_

- [x] 4. Create Eloquent models dengan relationships
  - [x] 4.1 Create User model dengan role functionality
    - Add role enum casting
    - Add attendances relationship (hasMany)
    - Add scopes for admins() and scanners()
    - Implement FilamentUser interface
    - _Requirements: 6.1, 6.2_
  
  - [x] 4.2 Create Mahasiswa model
    - Add graduationTickets relationship (hasMany)
    - Add accessor for full_name
    - Add method getActiveTicket()
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_
  
  - [x] 4.3 Create GraduationEvent model
    - Add graduationTickets relationship (hasMany)
    - Add scopes for active() and upcoming()
    - Add method getMapEmbedUrl() for Google Maps integration
    - _Requirements: 5.2, 5.3, 5.4_
  
  - [x] 4.4 Create GraduationTicket model
    - Add mahasiswa relationship (belongsTo)
    - Add graduationEvent relationship (belongsTo)
    - Add attendances relationship (hasMany)
    - Add methods: generateMagicLink(), generateQRTokens(), isExpired(), getAttendanceStatus()
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 6.1, 6.2, 6.3, 6.4, 6.5_
  
  - [x] 4.5 Create Attendance model
    - Add graduationTicket relationship (belongsTo)
    - Add scannedBy relationship (belongsTo User)
    - Add scopes for byRole() and today()
    - Add role enum casting
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 9.1, 9.2, 9.3, 9.4_

- [x] 5. Create service classes untuk business logic
  - [x] 5.1 Create QRCodeService
    - Implement generateQRCode() method untuk generate QR code image (base64)
    - Implement encryptQRData() method dengan AES-256 encryption
    - Implement decryptQRData() method untuk decrypt QR data
    - Implement validateQRSignature() method dengan HMAC validation
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 13.1, 13.2, 13.3, 13.4, 13.5_
  
  - [x] 5.2 Create TicketService
    - Implement createTicket() method untuk create graduation ticket
    - Implement generateMagicLink() method dengan encrypted token
    - Implement generateQRTokens() method untuk generate 3 QR tokens (mahasiswa, pendamping1, pendamping2)
    - Implement validateMagicLink() method untuk validate token
    - Implement markAsDistributed() method untuk track distribution
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 3.1, 3.2, 3.3, 3.4, 3.5_
  
  - [x] 5.3 Create AttendanceService
    - Implement recordAttendance() method untuk record scan result
    - Implement validateQRCode() method untuk validate scanned QR data
    - Implement checkDuplicate() method untuk prevent duplicate scans
    - Implement getStatistics() method untuk attendance statistics
    - Add logging for all scan attempts (success and failed)
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5, 9.1, 9.2, 9.3, 9.4, 11.1, 11.2, 11.3, 11.4, 11.5_
  
  - [x] 5.4 Create PDFService
    - Implement generateInvitationPDF() method dengan dompdf
    - Create PDF template dengan student info, event details, dan 3 QR codes
    - Optimize PDF untuk A4 printing
    - Implement file naming dengan student name dan NIM
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5_

- [x] 6. Create Filament resources untuk admin panel
  - [x] 6.1 Create MahasiswaResource
    - Create table dengan columns: NIM, Nama, Program Studi, Fakultas
    - Add filters untuk Program Studi dan Fakultas
    - Create form dengan fields: nim, nama, program_studi, fakultas, email, phone
    - Add validation rules untuk required fields
    - Add search functionality
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_
  
  - [x] 6.2 Create GraduationEventResource
    - Create table dengan columns: Name, Date, Time, Location, Active Status
    - Add filters untuk Active status dan Date Range
    - Create form dengan fields: name, date, time, location details, map coordinates
    - Add action untuk Set Active event
    - _Requirements: 5.2, 5.3, 5.4_
  
  - [ ]* 6.3 Add statistics widget untuk GraduationEventResource
    - Add statistics widget untuk event
    - _Requirements: 5.2, 5.3, 5.4_
  
  - [x] 6.4 Create GraduationTicketResource
    - Create table dengan columns: Mahasiswa, Event, Status, Distributed, Attendance Status
    - Add filters untuk Event, Distributed Status, Attendance Status
    - Add action untuk Copy Magic Link dengan clipboard functionality
    - Add action untuk Send WhatsApp dengan pre-filled message template
    - Add action untuk View Invitation (open in new tab)
    - Create infolist untuk display ticket details dengan QR codes preview
    - Add bulk action untuk Create Tickets untuk multiple mahasiswa
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 3.1, 3.2, 3.3, 3.4, 3.5_
  
  - [ ]* 6.5 Add Regenerate QR Codes action ke GraduationTicketResource
    - Add action untuk Regenerate QR Codes
    - _Requirements: 2.1, 2.2, 2.3_
  
  - [x] 6.6 Create AttendanceResource
    - Create table dengan columns: Mahasiswa, Role, Event, Scanned At, Scanned By
    - Add filters untuk Event, Role, Date Range, Scanner
    - _Requirements: 11.1, 11.2, 11.3, 11.4, 11.5_
  
  - [ ]* 6.7 Add export functionality ke AttendanceResource
    - Add export actions untuk CSV dan Excel format
    - Implement export dengan all relevant fields
    - Add filtering capability untuk export data
    - _Requirements: 12.1, 12.2, 12.3, 12.4, 12.5_
  
  - [x] 6.8 Create dashboard widgets untuk statistics
    - Create StatsOverview widget dengan cards: Total Students, Total Attended, Pendamping 1, Pendamping 2
    - Implement real-time statistics update
    - _Requirements: 11.1, 11.2, 11.3, 11.4, 11.5_
  
  - [ ]* 6.9 Add chart widget untuk attendance trends
    - Add chart widget untuk attendance trends
    - _Requirements: 11.1, 11.2, 11.3, 11.4, 11.5_

- [x] 7. Create InvitationController dan views
  - [x] 7.1 Create InvitationController dengan show method
    - Implement show() method untuk validate magic link token
    - Handle invalid/expired token dengan error page
    - Pass ticket data, student info, event details ke view
    - Generate 3 QR codes untuk display
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 5.1, 5.2, 5.3, 5.4, 6.1, 6.2, 6.3, 6.4, 6.5_
  
  - [x] 7.2 Create invitation Blade view dengan Tailwind CSS
    - Design mobile-first responsive layout
    - Display student information (nama, NIM, program studi)
    - Display event details (date, time, location)
    - Embed Google Maps dengan location coordinates
    - Display 3 QR codes dengan clear labels (Mahasiswa, Pendamping 1, Pendamping 2)
    - Add download PDF button
    - Style dengan Tailwind CSS dan university branding
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 6.1, 6.2, 6.3, 6.4, 6.5_
  
  - [x] 7.3 Implement downloadPDF method di InvitationController
    - Validate magic link token
    - Call PDFService untuk generate PDF
    - Return PDF sebagai download response
    - Implement proper file naming
    - Add rate limiting (5 requests per minute per token)
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5_
  
  - [x] 7.4 Create error page untuk invalid magic link
    - Design error page dengan clear message
    - Add helpful information untuk user
    - Style dengan Tailwind CSS
    - _Requirements: 4.4_

- [x] 8. Create Livewire Scanner component
  - [x] 8.1 Create Scanner Livewire component class
    - Add properties: status (ready/scanning/success/error), scanResult, errorMessage
    - Implement scanQRCode() method untuk handle scan result dari JavaScript
    - Call AttendanceService untuk validate dan record attendance
    - Implement resetScanner() method untuk auto-reset after 3 seconds
    - Add real-time UI updates dengan Livewire
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 7.1, 7.2, 7.3, 7.4, 7.5_
  
  - [x] 8.2 Create Scanner Blade view dengan camera interface
    - Integrate html5-qrcode JavaScript library
    - Create full-screen camera viewfinder
    - Implement QR code detection dan send data ke Livewire method
    - Handle camera permission request
    - Display clear scanning indicator
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5_
  
  - [x] 8.3 Create success screen component
    - Design green background success screen
    - Display student name dan role (mahasiswa/pendamping 1/pendamping 2)
    - Display confirmation message
    - Add audio feedback (beep sound)
    - Implement auto-reset after 3 seconds
    - _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5_
  
  - [x] 8.4 Create error screen component
    - Design red background error screen
    - Display appropriate error messages (invalid QR, duplicate scan, expired ticket)
    - Implement auto-reset after 3 seconds
    - _Requirements: 10.1, 10.2, 10.3_
  
  - [x] 8.5 Add authentication middleware untuk scanner route
    - Protect scanner route dengan auth middleware
    - Ensure only authenticated users can access scanner
    - _Requirements: 6.1, 6.2_

- [x] 9. Setup routes dan middleware
  - Create web routes untuk invitation show dan download
  - Create route untuk Livewire scanner dengan auth middleware
  - Add rate limiting middleware untuk magic link access (10 requests per minute per IP)
  - Add rate limiting middleware untuk scanner API (30 requests per minute per user)
  - Configure Filament routes (auto-registered)
  - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 6.1, 6.2, 8.1, 8.2, 8.3, 8.4, 8.5_

- [x] 10. Implement security features
  - Configure encryption key untuk QR data di .env
  - Implement CSRF protection untuk all forms
  - Add input validation untuk all user inputs
  - Configure secure session settings
  - Add SQL injection prevention checks (verify Eloquent usage)
  - _Requirements: 13.1, 13.2, 13.3, 13.4, 13.5_

- [x] 11. Setup database indexes untuk performance
  - Add database indexes (nim, magic_link_token, graduation_ticket_id + role)
  - Configure eager loading untuk relationships di resources
  - _Requirements: All requirements benefit from optimization_

- [ ]* 11.1 Setup caching untuk optimization
  - Configure cache driver (Redis recommended, file as fallback)
  - Implement cache untuk active graduation event
  - Implement cache untuk attendance statistics (60 seconds TTL)
  - Setup Tailwind CSS purging untuk production
  - _Requirements: All requirements benefit from optimization_

- [x] 12. Create seeders untuk testing
  - [x] 12.1 Create UserSeeder
    - Create admin user dengan role 'admin'
    - Create scanner user dengan role 'scanner'
    - _Requirements: 6.1, 6.2_
  
  - [ ]* 12.2 Create MahasiswaSeeder
    - Create sample mahasiswa records (at least 10)
    - Use realistic data (nama, NIM, program studi, fakultas)
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_
  
  - [ ]* 12.3 Create GraduationEventSeeder
    - Create active graduation event dengan complete details
    - Include location coordinates untuk map testing
    - _Requirements: 5.2, 5.3, 5.4_
  
  - [ ]* 12.4 Create GraduationTicketSeeder
    - Create tickets untuk seeded mahasiswa
    - Generate magic links dan QR tokens
    - _Requirements: 2.1, 2.2, 2.3, 6.1, 6.2, 6.3, 6.4, 6.5_

- [ ] 13. Final integration dan testing
  - Run all migrations dengan `php artisan migrate`
  - Run seeders dengan `php artisan db:seed`
  - Test admin panel functionality (create mahasiswa, create tickets, copy magic links)
  - Test invitation page dengan sample magic link
  - Test QR code display dan PDF download
  - Test scanner application dengan sample QR codes
  - Test attendance recording dan duplicate prevention
  - Verify statistics display dan real-time updates
  - Verify all error handling scenarios
  - _Requirements: All requirements_

- [ ]* 14. Documentation dan deployment preparation
  - Create README.md dengan installation instructions
  - Document environment variables yang diperlukan
  - Document database credentials dan setup
  - Create deployment checklist
  - Document API endpoints (if any)
  - Add inline code comments untuk complex logic
  - _Requirements: All requirements_
