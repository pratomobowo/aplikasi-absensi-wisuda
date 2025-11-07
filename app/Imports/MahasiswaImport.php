<?php

namespace App\Imports;

use App\Models\Mahasiswa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

class MahasiswaImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    private int $successCount = 0;
    private int $failureCount = 0;
    private int $duplicateCount = 0;
    private array $errors = [];

    /**
     * Prepare data for validation - convert numeric NPM to string
     * 
     * @param array $row
     * @param int $index
     * @return array
     */
    public function prepareForValidation($row, $index)
    {
        // Convert NPM to string if it's numeric (Excel auto-converts numbers)
        if (isset($row['npm']) && is_numeric($row['npm'])) {
            $row['npm'] = (string) $row['npm'];
        }
        
        // Convert phone to string if it's numeric
        if (isset($row['phone']) && is_numeric($row['phone'])) {
            $row['phone'] = (string) $row['phone'];
        }
        
        // Clean up empty strings to null for optional fields
        $optionalFields = ['yudisium', 'email', 'phone', 'nomor_kursi', 'judul_skripsi'];
        foreach ($optionalFields as $field) {
            if (isset($row[$field]) && trim($row[$field]) === '') {
                $row[$field] = null;
            }
        }
        
        // Trim yudisium to handle extra spaces
        if (isset($row['yudisium']) && $row['yudisium'] !== null) {
            $row['yudisium'] = trim($row['yudisium']);
        }
        
        return $row;
    }

    /**
     * Map each row to a Mahasiswa model
     * 
     * @param array $row
     * @return Mahasiswa|null
     */
    public function model(array $row)
    {
        // NPM should already be converted to string by prepareForValidation
        $npm = $row['npm'];

        // Check for duplicate NPM
        $existing = Mahasiswa::where('npm', $npm)->first();

        if ($existing) {
            $this->duplicateCount++;
            // Update existing record (excluding password)
            $existing->update([
                'nama' => $row['nama'],
                'program_studi' => $row['program_studi'],
                'ipk' => $row['ipk'],
                'yudisium' => $row['yudisium'] ?? null,
                'email' => $row['email'] ?? null,
                'phone' => $row['phone'] ?? null,
                'nomor_kursi' => $row['nomor_kursi'] ?? null,
                'judul_skripsi' => $row['judul_skripsi'] ?? null,
            ]);
            return null;
        }

        $this->successCount++;
        // Auto-generate password from NPM for new records
        return new Mahasiswa([
            'npm' => $npm,
            'nama' => $row['nama'],
            'program_studi' => $row['program_studi'],
            'ipk' => $row['ipk'],
            'yudisium' => $row['yudisium'] ?? null,
            'email' => $row['email'] ?? null,
            'phone' => $row['phone'] ?? null,
            'nomor_kursi' => $row['nomor_kursi'] ?? null,
            'judul_skripsi' => $row['judul_skripsi'] ?? null,
            'password' => bcrypt($npm), // Use NPM as password, hashed with bcrypt
        ]);
    }

    /**
     * Define validation rules for each row
     * 
     * @return array
     */
    public function rules(): array
    {
        return [
            'npm' => 'required|string|max:20',
            'nama' => 'required|string|max:255',
            'program_studi' => 'required|string|max:255',
            'ipk' => 'required|numeric|between:0,4',
            'yudisium' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    if ($value === null || trim($value) === '') {
                        return;
                    }
                    $allowed = ['Dengan Pujian', 'Sangat Memuaskan', 'Memuaskan'];
                    $normalized = trim($value);
                    if (!in_array($normalized, $allowed)) {
                        $fail('Yudisium harus salah satu dari: Dengan Pujian, Sangat Memuaskan, Memuaskan (atau kosongkan jika tidak ada)');
                    }
                },
            ],
            'email' => [
                'nullable',
                'max:255',
                function ($attribute, $value, $fail) {
                    if ($value === null || trim($value) === '') {
                        return;
                    }
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $fail('Format email tidak valid');
                    }
                },
            ],
            'phone' => 'nullable|string|max:20',
            'nomor_kursi' => 'nullable|string|max:20',
            'judul_skripsi' => 'nullable|string|max:500',
        ];
    }

    /**
     * Define custom validation messages
     * 
     * @return array
     */
    public function customValidationMessages(): array
    {
        return [
            'npm.required' => 'NPM wajib diisi',
            'npm.max' => 'NPM maksimal 20 karakter',
            'nama.required' => 'Nama wajib diisi',
            'nama.max' => 'Nama maksimal 255 karakter',
            'program_studi.required' => 'Program Studi wajib diisi',
            'program_studi.max' => 'Program Studi maksimal 255 karakter',
            'ipk.required' => 'IPK wajib diisi',
            'ipk.numeric' => 'IPK harus berupa angka',
            'ipk.between' => 'IPK harus antara 0 dan 4',
            'yudisium.in' => 'Yudisium harus salah satu dari: Dengan Pujian, Sangat Memuaskan, Memuaskan',
            'email.email' => 'Format email tidak valid',
            'email.max' => 'Email maksimal 255 karakter',
            'phone.max' => 'Telepon maksimal 20 karakter',
            'nomor_kursi.max' => 'Nomor kursi maksimal 20 karakter',
            'judul_skripsi.max' => 'Judul skripsi maksimal 500 karakter',
        ];
    }

    /**
     * Define the heading row number
     * 
     * @return int
     */
    public function headingRow(): int
    {
        return 1;
    }

    /**
     * Handle validation failures
     * 
     * @param Failure[] $failures
     */
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

    /**
     * Get import summary with statistics
     * 
     * @return array
     */
    public function getImportSummary(): array
    {
        return [
            'success' => $this->successCount,
            'failed' => $this->failureCount,
            'duplicate' => $this->duplicateCount,
            'total' => $this->successCount + $this->failureCount + $this->duplicateCount,
            'errors' => $this->errors,
        ];
    }
}
