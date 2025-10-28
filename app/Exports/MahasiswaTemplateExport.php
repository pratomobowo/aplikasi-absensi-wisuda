<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MahasiswaTemplateExport implements FromArray, WithHeadings, WithStyles
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
            [
                '2024010003',
                'Bob Johnson',
                'Manajemen',
                'Fakultas Ekonomi',
                '3.25',
                'Memuaskan',
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
            'fakultas',
            'ipk',
            'yudisium',
            'email',
            'phone'
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
}
