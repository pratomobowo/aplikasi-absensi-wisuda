# Design Document

## Overview

Desain ini mencakup perubahan struktur data mahasiswa dari NIM ke NPM, penambahan field IPK dan Yudisium, serta implementasi fitur import Excel untuk data mahasiswa. Sistem menggunakan Laravel dengan Filament Admin Panel dan akan memanfaatkan package Laravel Excel (Maatwebsite/Excel) untuk handling import Excel.

## Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    Filament Admin Panel                      │
│  ┌────────────────────────────────────────────────────────┐ │
│  │         MahasiswaResource (UI Layer)                   │ │
│  │  - Form (Create/Edit)                                  │ │
│  │  - Table (List/Search/Filter)                          │ │
│  │  - Import Action (Excel Upload)                        │ │
│  │  - Download Template Action                            │ │
│  └────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                   Application Layer                          │
│  ┌────────────────────────────────────────────────────────┐ │
│  │         MahasiswaImport (Import Handler)               │ │
│  │  - Validate Excel Structure                            │ │
│  │  - Process Rows                                        │ │
│  │  - Handle Duplicates                                   │ │
│  │  - Generate Import Report                              │ │
│  └────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                      Data Layer                              │
│  ┌────────────────────────────────────────────────────────┐ │
│  │              Mahasiswa Model                           │ │
│  │  - NPM (unique identifier)                             │ │
│  │  - Nama                                                │ │
│  │  - Prodi                                               │ │
│  │  - Fakultas                                            │ │
│  │  - IPK                                                 │ │
│  │  - Yudisium                                            │ │
│  └────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                      Database                                │
│                   mahasiswa table                            │
└─────────────────────────────────────────────────────────────┘
```

## Components and Interfaces

### 1. Database Migration

**File:** `database/migrations/YYYY_MM_DD_HHMMSS_update_mahasiswa_table_structure.php`

**Purpose:** Mengubah struktur tabel mahasiswa dari NIM ke NPM dan menambah field IPK dan Yudisium

**Schema Changes:**
```php
- Rename column: 'nim' → 'npm'
- Rename column: 'program_studi' → 'prodi' (untuk konsistensi)
- Add column: 'ipk' (decimal, 2 decimal places)
- Add column: 'yudisium' (string, nullable)
- Keep columns: 'email', 'phone' (nullable, optional fields)
```

**Migration Strategy:**
- Menggunakan `renameColumn` untuk mengubah nim → npm
- Menggunakan `dropColumn` untuk menghapus field yang tidak diperlukan
- Menggunakan `addColumn` untuk field baru
- Data existing akan tetap terjaga

### 2. Mahasiswa Model

**File:** `app/Models/Mahasiswa.php`

**Updates:**
```php
protected $fillable = [
    'npm',      // changed from 'nim'
    'nama',
    'prodi',    // changed from 'program_studi'
    'fakultas',
    'ipk',      // new field
    'yudisium', // new field
    'email',    // optional field
    'phone',    // optional field
];

protected $casts = [
    'ipk' => 'decimal:2',
];
```

**Validation Rules:**
- NPM: required, unique, string, max 20 characters
- Nama: required, string, max 255 characters
- Prodi: required, string, max 255 characters
- Fakultas: required, string, max 255 characters
- IPK: required, numeric, between 0.00 and 4.00, 2 decimal places
- Yudisium: nullable, string, in:['Cum Laude', 'Sangat Memuaskan', 'Memuaskan']
- Email: nullable, email, max 255 characters
- Phone: nullable, string, max 20 characters

### 3. MahasiswaResource (Filament)

**File:** `app/Filament/Resources/MahasiswaResource.php`

**Form Schema:**
```php
Forms\Components\TextInput::make('npm')
    ->label('NPM')
    ->required()
    ->unique(ignoreRecord: true)
    ->maxLength(20)

Forms\Components\TextInput::make('nama')
    ->label('Nama')
    ->required()
    ->maxLength(255)

Forms\Components\TextInput::make('prodi')
    ->label('Program Studi')
    ->required()
    ->maxLength(255)

Forms\Components\TextInput::make('fakultas')
    ->label('Fakultas')
    ->required()
    ->maxLength(255)

Forms\Components\TextInput::make('ipk')
    ->label('IPK')
    ->required()
    ->numeric()
    ->minValue(0)
    ->maxValue(4)
    ->step(0.01)

Forms\Components\Select::make('yudisium')
    ->label('Yudisium')
    ->options([
        'Cum Laude' => 'Cum Laude',
        'Sangat Memuaskan' => 'Sangat Memuaskan',
        'Memuaskan' => 'Memuaskan',
    ])
    ->nullable()

Forms\Components\TextInput::make('email')
    ->label('Email')
    ->email()
    ->maxLength(255)
    ->nullable()

Forms\Components\TextInput::make('phone')
    ->label('Telepon')
    ->tel()
    ->maxLength(20)
    ->nullable()
```

**Table Columns:**
- NPM (searchable, sortable)
- Nama (searchable, sortable)
- Prodi (searchable, sortable)
- Fakultas (searchable, sortable)
- IPK (sortable)
- Yudisium
- Email (searchable, toggleable - hidden by default)
- Phone (toggleable - hidden by default)

**Header Actions:**
```php
Tables\Actions\Action::make('import')
    ->label('Import Excel')
    ->icon('heroicon-o-arrow-up-tray')
    ->form([
        Forms\Components\FileUpload::make('file')
            ->label('File Excel')
            ->acceptedFileTypes([
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ])
            ->required()
    ])
    ->action(function (array $data) {
        // Import logic
    })

Tables\Actions\Action::make('downloadTemplate')
    ->label('Download Template')
    ->icon('heroicon-o-arrow-down-tray')
    ->action(function () {
        // Download template logic
    })
```

### 4. MahasiswaImport Class

**File:** `app/Imports/MahasiswaImport.php`

**Purpose:** Handle Excel import menggunakan Laravel Excel package

**Implementation:**
```php
class MahasiswaImport implements ToModel, WithHeadingRow, WithValidation
{
    private $successCount = 0;
    private $failureCount = 0;
    private $duplicateCount = 0;
    private $errors = [];

    public function model(array $row)
    {
        // Check for duplicate NPM
        $existing = Mahasiswa::where('npm', $row['npm'])->first();
        
        if ($existing) {
            $this->duplicateCount++;
            // Update existing record
            $existing->update([...]);
            return null;
        }

        $this->successCount++;
        return new Mahasiswa([
            'npm' => $row['npm'],
            'nama' => $row['nama'],
            'prodi' => $row['prodi'],
            'fakultas' => $row['fakultas'],
            'ipk' => $row['ipk'],
            'yudisium' => $row['yudisium'] ?? null,
            'email' => $row['email'] ?? null,
            'phone' => $row['phone'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'npm' => 'required|string|max:20',
            'nama' => 'required|string|max:255',
            'prodi' => 'required|string|max:255',
            'fakultas' => 'required|string|max:255',
            'ipk' => 'required|numeric|between:0,4',
            'yudisium' => 'nullable|string|in:Cum Laude,Sangat Memuaskan,Memuaskan',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->failureCount++;
            $this->errors[] = [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values(),
            ];
        }
    }

    public function getImportSummary(): array
    {
        return [
            'success' => $this->successCount,
            'failed' => $this->failureCount,
            'duplicate' => $this->duplicateCount,
            'errors' => $this->errors,
        ];
    }
}
```

### 5. Excel Template Generator

**File:** `app/Exports/MahasiswaTemplateExport.php`

**Purpose:** Generate template Excel untuk import

**Structure:**
```php
class MahasiswaTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        return [
            [
                '2024010001',
                'John Doe',
                'Teknik Informatika',
                'Fakultas Teknik',
                '3.75',
                'Cum Laude',
                'john.doe@example.com',
                '081234567890'
            ],
            [
                '2024010002',
                'Jane Smith',
                'Sistem Informasi',
                'Fakultas Teknik',
                '3.50',
                'Sangat Memuaskan',
                'jane.smith@example.com',
                '081234567891'
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'NPM',
            'Nama',
            'Prodi',
            'Fakultas',
            'IPK',
            'Yudisium',
            'Email',
            'Phone'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
```

## Data Models

### Mahasiswa Table Schema

```sql
CREATE TABLE mahasiswa (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    npm VARCHAR(20) UNIQUE NOT NULL,
    nama VARCHAR(255) NOT NULL,
    prodi VARCHAR(255) NOT NULL,
    fakultas VARCHAR(255) NOT NULL,
    ipk DECIMAL(3,2) NOT NULL,
    yudisium VARCHAR(255) NULL,
    email VARCHAR(255) NULL,
    phone VARCHAR(20) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_npm (npm),
    INDEX idx_prodi (prodi),
    INDEX idx_fakultas (fakultas)
);
```

### Excel Import Format

**Expected Columns (Header Row):**
1. NPM
2. Nama
3. Prodi
4. Fakultas
5. IPK
6. Yudisium
7. Email (optional)
8. Phone (optional)

**Example Data:**
```
NPM         | Nama          | Prodi                | Fakultas        | IPK  | Yudisium          | Email                  | Phone
2024010001  | John Doe      | Teknik Informatika   | Fakultas Teknik | 3.75 | Cum Laude         | john.doe@example.com   | 081234567890
2024010002  | Jane Smith    | Sistem Informasi     | Fakultas Teknik | 3.50 | Sangat Memuaskan  | jane.smith@example.com | 081234567891
2024010003  | Bob Johnson   | Manajemen            | Fakultas Ekonomi| 3.25 | Memuaskan         |                        |
```

## Error Handling

### Import Error Scenarios

1. **Invalid File Format**
   - Error: File bukan format Excel (.xls/.xlsx)
   - Response: Notification error "Format file tidak valid. Gunakan file Excel (.xls atau .xlsx)"

2. **Missing Required Columns**
   - Error: Header tidak sesuai template
   - Response: Notification error "Struktur file tidak sesuai. Download template untuk format yang benar"

3. **Validation Errors**
   - Error: Data tidak sesuai aturan validasi
   - Response: Notification dengan detail baris yang error
   - Provide download link untuk error report

4. **Duplicate NPM**
   - Behavior: Update existing record
   - Response: Include in summary "X data diupdate (duplikat NPM)"

5. **Database Error**
   - Error: Gagal menyimpan ke database
   - Response: Rollback transaction, notification error dengan pesan teknis

### Form Validation Errors

- Real-time validation pada form input
- Error message yang jelas untuk setiap field
- Highlight field yang error

## Testing Strategy

### Unit Tests

**File:** `tests/Unit/MahasiswaImportTest.php`

**Test Cases:**
1. `test_import_valid_excel_file()`
   - Upload file Excel valid
   - Verify data tersimpan dengan benar

2. `test_import_handles_duplicate_npm()`
   - Upload file dengan NPM duplikat
   - Verify data diupdate, bukan create baru

3. `test_import_validates_required_fields()`
   - Upload file dengan field kosong
   - Verify validation error muncul

4. `test_import_validates_ipk_range()`
   - Upload file dengan IPK di luar range 0-4
   - Verify validation error muncul

5. `test_import_validates_yudisium_options()`
   - Upload file dengan yudisium tidak valid
   - Verify validation error muncul

### Feature Tests

**File:** `tests/Feature/MahasiswaResourceTest.php`

**Test Cases:**
1. `test_can_access_mahasiswa_list_page()`
2. `test_can_create_mahasiswa_with_new_fields()`
3. `test_can_edit_mahasiswa_with_new_fields()`
4. `test_npm_must_be_unique()`
5. `test_can_see_import_button()`
6. `test_can_download_template()`
7. `test_can_import_excel_file()`

### Integration Tests

1. Test full import flow dari upload hingga data tersimpan
2. Test migration dari NIM ke NPM tidak kehilangan data
3. Test relationship dengan GraduationTicket masih berfungsi

## Dependencies

### Required Packages

1. **maatwebsite/excel** (^3.1)
   - Purpose: Handle Excel import/export
   - Installation: `composer require maatwebsite/excel`

2. **filament/filament** (already installed)
   - Purpose: Admin panel framework

### Configuration

**File:** `config/excel.php`

```php
'imports' => [
    'read_only' => true,
    'heading_row' => [
        'formatter' => 'slug',
    ],
],
```

## Migration Strategy

### Data Migration Steps

1. **Backup Database**
   - Create backup sebelum migration
   - Command: `php artisan db:backup`

2. **Run Migration**
   - Rename nim → npm
   - Rename program_studi → prodi
   - Add new fields (ipk, yudisium)
   - Keep existing fields (email, phone)

3. **Update Related Code**
   - Update all references dari 'nim' ke 'npm'
   - Update all references dari 'program_studi' ke 'prodi'

4. **Verify Data Integrity**
   - Check semua data mahasiswa masih accessible
   - Check relationship dengan graduation_tickets masih berfungsi

### Rollback Plan

Jika terjadi masalah:
1. Run migration rollback: `php artisan migrate:rollback`
2. Restore database dari backup
3. Investigate issue sebelum retry

## Performance Considerations

### Import Optimization

1. **Batch Processing**
   - Process import dalam chunks (500 rows per batch)
   - Prevent memory overflow untuk file besar

2. **Database Indexing**
   - Index pada npm untuk faster duplicate check
   - Index pada prodi dan fakultas untuk filtering

3. **Queue Processing**
   - Untuk file > 1000 rows, process via queue
   - Provide progress notification

### Caching

- Cache distinct values untuk prodi dan fakultas (untuk filter dropdown)
- Clear cache setelah import

## Security Considerations

1. **File Upload Validation**
   - Validate file type (only .xls, .xlsx)
   - Validate file size (max 5MB)
   - Scan for malicious content

2. **Input Sanitization**
   - Sanitize all input dari Excel
   - Prevent SQL injection via prepared statements (handled by Eloquent)

3. **Authorization**
   - Only admin users can import
   - Check user permission before import action

4. **Data Privacy**
   - Log import activities
   - Track who imported what data

## UI/UX Considerations

### Import Flow

1. User clicks "Import Excel" button
2. Modal opens with file upload field
3. User selects file
4. User clicks "Import"
5. Loading indicator shows
6. Success notification with summary appears
7. Table refreshes with new data

### Template Download Flow

1. User clicks "Download Template"
2. Excel file downloads immediately
3. File contains headers and example data

### Error Display

- Clear, actionable error messages
- Option to download detailed error report
- Highlight which rows failed and why
