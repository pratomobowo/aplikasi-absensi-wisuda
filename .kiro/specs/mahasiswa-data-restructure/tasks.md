# Implementation Plan

- [x] 1. Install dan konfigurasi Laravel Excel package
  - Install package maatwebsite/excel via composer
  - Publish konfigurasi Excel jika diperlukan
  - _Requirements: 4.1, 4.2_

- [x] 2. Buat database migration untuk update struktur tabel mahasiswa
  - Buat migration file untuk rename column nim → npm
  - Buat migration file untuk rename column program_studi → prodi
  - Tambahkan column ipk (decimal 3,2)
  - Tambahkan column yudisium (string, nullable)
  - Tambahkan index untuk npm, prodi, dan fakultas
  - _Requirements: 1.1, 1.4, 2.1, 3.1_

- [x] 3. Update Mahasiswa model
- [x] 3.1 Update fillable attributes dan casts
  - Update fillable array dengan field baru (npm, prodi, ipk, yudisium)
  - Tambahkan cast untuk ipk sebagai decimal:2
  - _Requirements: 1.1, 2.1, 3.1_

- [x] 3.2 Update validation rules
  - Buat atau update Request class untuk validasi mahasiswa
  - Implementasi validation rules untuk semua field termasuk IPK dan Yudisium
  - _Requirements: 1.3, 2.5, 3.2_

- [ ] 4. Update MahasiswaResource (Filament)
- [ ] 4.1 Update form schema
  - Ganti field nim menjadi npm dengan label "NPM"
  - Ganti field program_studi menjadi prodi dengan label "Program Studi"
  - Tambahkan field ipk dengan input numeric (min 0, max 4, step 0.01)
  - Tambahkan field yudisium dengan select dropdown (Cum Laude, Sangat Memuaskan, Memuaskan)
  - Pastikan email dan phone tetap ada sebagai field opsional
  - _Requirements: 1.2, 2.2, 3.3_

- [ ] 4.2 Update table columns
  - Update kolom tabel untuk menampilkan npm (bukan nim)
  - Update kolom tabel untuk menampilkan prodi (bukan program_studi)
  - Tambahkan kolom ipk dengan sortable
  - Tambahkan kolom yudisium
  - Set email dan phone sebagai toggleable columns (hidden by default)
  - _Requirements: 1.2, 2.3, 3.4_

- [ ] 4.3 Update filters
  - Update filter program_studi menjadi prodi
  - Pastikan filter fakultas tetap berfungsi
  - _Requirements: 3.4_

- [ ] 5. Buat MahasiswaImport class untuk handle Excel import
- [ ] 5.1 Implementasi import class dengan Laravel Excel
  - Buat class MahasiswaImport yang implements ToModel, WithHeadingRow, WithValidation
  - Implementasi method model() untuk mapping row ke Mahasiswa model
  - Implementasi method rules() untuk validasi setiap row
  - Implementasi method headingRow() untuk define expected headers
  - _Requirements: 4.4, 5.1, 5.2_

- [ ] 5.2 Implementasi duplicate handling
  - Check NPM duplikat sebelum insert
  - Update existing record jika NPM sudah ada
  - Track jumlah duplicate untuk summary report
  - _Requirements: 5.4_

- [ ] 5.3 Implementasi error handling dan reporting
  - Implementasi method onFailure() untuk capture validation errors
  - Simpan detail error (row number, field, error message)
  - Buat method getImportSummary() untuk return summary (success, failed, duplicate count)
  - _Requirements: 5.3, 6.3_

- [ ] 6. Buat MahasiswaTemplateExport class untuk generate template Excel
  - Buat class yang implements FromArray, WithHeadings, WithStyles
  - Implementasi method array() dengan contoh data
  - Implementasi method headings() dengan header kolom (NPM, Nama, Prodi, Fakultas, IPK, Yudisium, Email, Phone)
  - Implementasi method styles() untuk styling header (bold)
  - _Requirements: 7.1, 7.2, 7.3_

- [ ] 7. Tambahkan import dan download template actions ke MahasiswaResource
- [ ] 7.1 Implementasi action "Import Excel"
  - Tambahkan header action dengan icon upload
  - Buat form dengan FileUpload component (accept .xls, .xlsx)
  - Implementasi action handler untuk process import
  - Handle file upload dan pass ke MahasiswaImport class
  - _Requirements: 4.1, 4.2, 4.3_

- [ ] 7.2 Implementasi feedback dan notification
  - Tampilkan loading indicator saat import berjalan
  - Tampilkan success notification dengan summary (jumlah berhasil, gagal, duplikat)
  - Tampilkan error notification jika file tidak valid
  - Refresh table setelah import berhasil
  - _Requirements: 6.1, 6.2, 6.3_

- [ ] 7.3 Implementasi action "Download Template"
  - Tambahkan header action dengan icon download
  - Implementasi action handler untuk generate dan download template
  - Return Excel file dengan nama "template-mahasiswa.xlsx"
  - _Requirements: 7.1, 7.5_

- [ ] 8. Update references di seluruh aplikasi
- [ ] 8.1 Update views dan components
  - Search dan replace references dari 'nim' ke 'npm' di views
  - Search dan replace references dari 'program_studi' ke 'prodi' di views
  - Update label dan text yang menampilkan NIM menjadi NPM
  - _Requirements: 1.2_

- [ ] 8.2 Update Livewire components
  - Update DataWisudawan component jika ada reference ke nim atau program_studi
  - Update Scanner component jika ada reference ke nim
  - Update BukuWisuda component jika ada reference ke nim atau program_studi
  - _Requirements: 1.2_

- [ ] 8.3 Update services dan controllers
  - Update AttendanceService jika ada reference ke nim
  - Update TicketService jika ada reference ke nim
  - Update InvitationController jika ada reference ke nim
  - _Requirements: 1.2_

- [ ] 9. Run migration dan verify data integrity
  - Backup database sebelum migration
  - Run migration: php artisan migrate
  - Verify semua data mahasiswa masih accessible dengan npm
  - Verify relationship dengan graduation_tickets masih berfungsi
  - _Requirements: 1.4, 1.5_

- [ ] 10. Testing dan validation
- [ ] 10.1 Manual testing form mahasiswa
  - Test create mahasiswa dengan semua field baru
  - Test edit mahasiswa dengan field baru
  - Test validasi NPM unique
  - Test validasi IPK range (0-4)
  - Test validasi Yudisium options
  - _Requirements: 1.3, 2.5, 3.2_

- [ ] 10.2 Manual testing import Excel
  - Test upload file Excel valid dengan data lengkap
  - Test upload file dengan NPM duplikat (should update)
  - Test upload file dengan data invalid (should show errors)
  - Test upload file dengan format salah (should reject)
  - Test download template dan verify struktur
  - _Requirements: 4.3, 4.4, 4.5, 5.3, 5.4, 5.5, 6.3, 7.1_

- [ ] 10.3 Verify UI/UX
  - Verify semua label sudah berubah dari NIM ke NPM
  - Verify field IPK dan Yudisium tampil dengan benar
  - Verify import button dan download template button tampil
  - Verify notification dan error messages jelas dan informatif
  - _Requirements: 1.2, 2.2, 2.3, 4.1, 6.2_
