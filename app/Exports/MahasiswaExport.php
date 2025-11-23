<?php

namespace App\Exports;

use App\Models\Mahasiswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MahasiswaExport implements FromCollection, WithHeadings, WithStyles, WithColumnFormatting
{
    /**
     * Get mahasiswa data collection for export
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Get all mahasiswa ordered by nama
        return Mahasiswa::orderBy('nama', 'asc')
            ->get()
            ->map(function ($mahasiswa) {
                return [
                    $mahasiswa->npm,
                    $mahasiswa->nama,
                    $mahasiswa->program_studi,
                    number_format($mahasiswa->ipk, 2, '.', ''),
                    $mahasiswa->yudisium ?? '',
                    $mahasiswa->email ?? '',
                    $mahasiswa->phone ?? '',
                    $mahasiswa->nomor_kursi ?? '',
                    $mahasiswa->judul_skripsi ?? '',
                ];
            });
    }

    /**
     * Define the column headings (sesuai dengan template import)
     *
     * @return array
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
            'judul_skripsi',
        ];
    }

    /**
     * Apply styles to the worksheet (bold header sesuai template)
     *
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    /**
     * Format columns as text to prevent Excel conversion (sesuai template)
     *
     * @return array
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
