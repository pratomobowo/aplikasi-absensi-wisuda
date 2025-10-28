<?php

namespace App\Imports;

use App\Models\Mahasiswa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

class MahasiswaImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    private int $successCount = 0;
    private int $failureCount = 0;
    private int $duplicateCount = 0;
    private array $errors = [];

    /**
     * Map each row to a Mahasiswa model
     * 
     * @param array $row
     * @return Mahasiswa|null
     */
    public function model(array $row)
    {
        // Check for duplicate NPM
        $existing = Mahasiswa::where('npm', $row['npm'])->first();
        
        if ($existing) {
            $this->duplicateCount++;
            // Update existing record
            $existing->update([
                'nama' => $row['nama'],
                'program_studi' => $row['program_studi'],
                'fakultas' => $row['fakultas'],
                'ipk' => $row['ipk'],
                'yudisium' => $row['yudisium'] ?? null,
                'email' => $row['email'] ?? null,
                'phone' => $row['phone'] ?? null,
            ]);
            return null;
        }

        $this->successCount++;
        return new Mahasiswa([
            'npm' => $row['npm'],
            'nama' => $row['nama'],
            'program_studi' => $row['program_studi'],
            'fakultas' => $row['fakultas'],
            'ipk' => $row['ipk'],
            'yudisium' => $row['yudisium'] ?? null,
            'email' => $row['email'] ?? null,
            'phone' => $row['phone'] ?? null,
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
            'fakultas' => 'required|string|max:255',
            'ipk' => 'required|numeric|between:0,4',
            'yudisium' => 'nullable|string|in:Cum Laude,Sangat Memuaskan,Memuaskan',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
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
            'fakultas.required' => 'Fakultas wajib diisi',
            'fakultas.max' => 'Fakultas maksimal 255 karakter',
            'ipk.required' => 'IPK wajib diisi',
            'ipk.numeric' => 'IPK harus berupa angka',
            'ipk.between' => 'IPK harus antara 0 dan 4',
            'yudisium.in' => 'Yudisium harus salah satu dari: Cum Laude, Sangat Memuaskan, Memuaskan',
            'email.email' => 'Format email tidak valid',
            'email.max' => 'Email maksimal 255 karakter',
            'phone.max' => 'Telepon maksimal 20 karakter',
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
