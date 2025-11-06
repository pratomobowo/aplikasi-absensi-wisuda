<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MahasiswaTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnFormatting
{
    /**
     * Return array of example data for the template
     */
    public function array(): array
    {
        return [
            [
                '2024010001',
                'John Doe',
                'Teknik Informatika',
                '3.75',
                'Dengan Pujian',
                'john.doe@example.com',
                '081234567890',
                'A-001',
                'Sistem Informasi Manajemen Berbasis Web'
            ],
            [
                '2024010002',
                'Jane Smith',
                'Sistem Informasi',
                '3.50',
                'Sangat Memuaskan',
                'jane.smith@example.com',
                '081234567891',
                'A-002',
                'Analisis dan Perancangan Sistem Informasi Akademik'
            ],
            [
                '2024010003',
                'Bob Johnson',
                'Manajemen',
                '3.25',
                'Memuaskan',
                '',
                '',
                '',
                ''
            ],
        ];
    }

    /**
     * Return column headings for the template
     */
    public function headings(): array
    {
        return [
            'npm',
            'nama',
            'program_studi',
            'ipk',
            'yudisium',
            'email',
            'phone',
            'nomor_kursi',
            'judul_skripsi'
        ];
    }

    /**
     * Apply styles to the worksheet (bold header)
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    /**
     * Format NPM column as text to prevent Excel from converting to number
     */
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT, // NPM column
            'G' => NumberFormat::FORMAT_TEXT, // Phone column
            'H' => NumberFormat::FORMAT_TEXT, // Nomor Kursi column
        ];
    }
}
