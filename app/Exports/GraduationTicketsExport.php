<?php

namespace App\Exports;

use App\Models\GraduationEvent;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class GraduationTicketsExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    /**
     * @var GraduationEvent|null
     */
    protected $event;

    /**
     * @var array|null
     */
    protected $ticketIds;

    /**
     * Constructor
     *
     * @param GraduationEvent|null $event Filter by event, or null for all
     * @param array|null $ticketIds Specific ticket IDs to export, or null for all
     */
    public function __construct(?GraduationEvent $event = null, ?array $ticketIds = null)
    {
        $this->event = $event;
        $this->ticketIds = $ticketIds;
    }

    /**
     * Get the collection of data to export
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = \App\Models\GraduationTicket::with(['mahasiswa', 'graduationEvent']);

        // Filter by event if provided
        if ($this->event) {
            $query->where('graduation_event_id', $this->event->id);
        }

        // Filter by specific ticket IDs if provided
        if ($this->ticketIds) {
            $query->whereIn('id', $this->ticketIds);
        }

        $tickets = $query->get();

        // Transform data for export
        return $tickets->map(function ($ticket, $index) {
            return [
                'No' => $index + 1,
                'NPM' => $ticket->mahasiswa->npm,
                'Nama' => $ticket->mahasiswa->nama,
                'Acara' => $ticket->graduationEvent->name,
                'Link Undangan' => route('invitation.show', ['token' => $ticket->magic_link_token]),
            ];
        });
    }

    /**
     * Define the headings for the spreadsheet
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'NPM',
            'Nama',
            'Acara',
            'Link Undangan',
        ];
    }

    /**
     * Style the spreadsheet
     *
     * @param Worksheet $sheet
     * @return array|void
     */
    public function styles(Worksheet $sheet)
    {
        // Style header row
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1e40af'], // Blue
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Center align columns
        $sheet->getStyle('A:E')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('A:A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // No column

        // Add borders to all cells with data
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:E{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'D3D3D3'],
                ],
            ],
        ]);

        // Set row height for header
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Wrap text in all cells
        $sheet->getStyle("A1:E{$lastRow}")->getAlignment()->setWrapText(true);
    }
}
