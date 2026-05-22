<?php

namespace App\Console\Commands;

use App\Models\GraduationEvent;
use App\Models\GraduationTicket;
use Illuminate\Console\Command;

class FixTicketVisibilityCommand extends Command
{
    protected $signature = 'fix:ticket-visibility 
                            {--show-all : Tampilkan semua tiket termasuk yang diarsip}
                            {--unarchive : Kembalikan tiket dari arsip}';
    
    protected $description = 'Diagnose dan perbaiki masalah tiket tidak muncul di menu';

    public function handle(): int
    {
        $this->info('=== DIAGNOSIS TIKET WISUDA ===\n');
        
        // 1. Total tiket
        $totalTickets = GraduationTicket::count();
        $notArchived = GraduationTicket::notArchived()->count();
        $archived = GraduationTicket::archived()->count();
        
        $this->table(
            ['Status', 'Jumlah'],
            [
                ['Total Tiket', $totalTickets],
                ['Tiket Aktif (not archived)', $notArchived],
                ['Tiket Diarsipkan', $archived],
            ]
        );
        
        // 2. Per event
        $this->newLine();
        $this->info('Per Event:');
        
        $events = GraduationEvent::withCount(['graduationTickets' => function ($q) {
            $q->whereNull('archived_at');
        }])->get();
        
        $eventData = [];
        foreach ($events as $event) {
            $total = GraduationTicket::where('graduation_event_id', $event->id)->count();
            $active = GraduationTicket::where('graduation_event_id', $event->id)->whereNull('archived_at')->count();
            
            $eventData[] = [
                'Event' => $event->name,
                'Status' => $event->status,
                'Total' => $total,
                'Aktif' => $active,
                'Diarsip' => $total - $active,
            ];
        }
        
        $this->table(['Event', 'Status', 'Total', 'Aktif', 'Diarsip'], $eventData);
        
        // 3. Sample tiket yang diarsipkan
        if ($archived > 0) {
            $this->newLine();
            $this->info('Sample Tiket yang Diarsipkan:');
            
            $archivedTickets = GraduationTicket::archived()
                ->with(['mahasiswa', 'graduationEvent'])
                ->take(5)
                ->get();
            
            $sampleData = [];
            foreach ($archivedTickets as $ticket) {
                $sampleData[] = [
                    'Nama' => $ticket->mahasiswa->nama ?? 'N/A',
                    'NPM' => $ticket->mahasiswa->npm ?? 'N/A',
                    'Event' => $ticket->graduationEvent->name ?? 'N/A',
                    'Diarsip' => $ticket->archived_at?->format('Y-m-d H:i:s') ?? '-',
                ];
            }
            
            $this->table(['Nama', 'NPM', 'Event', 'Diarsip'], $sampleData);
        }
        
        // 4. Unarchive option
        if ($this->option('unarchive') && $archived > 0) {
            $this->newLine();
            $count = GraduationTicket::archived()->update(['archived_at' => null]);
            $this->info("✓ {$count} tiket berhasil dikembalikan dari arsip!");
        }
        
        // 5. Show all option
        if ($this->option('show-all')) {
            $this->newLine();
            $this->info('Semua Tiket (termasuk yang diarsip):');
            
            $allTickets = GraduationTicket::with(['mahasiswa', 'graduationEvent'])
                ->take(10)
                ->get();
            
            $allData = [];
            foreach ($allTickets as $ticket) {
                $allData[] = [
                    'Nama' => $ticket->mahasiswa->nama ?? 'N/A',
                    'NPM' => $ticket->mahasiswa->npm ?? 'N/A',
                    'Event' => $ticket->graduationEvent->name ?? 'N/A',
                    'Status' => $ticket->archived_at ? 'Diarsip' : 'Aktif',
                ];
            }
            
            $this->table(['Nama', 'NPM', 'Event', 'Status'], $allData);
        }
        
        $this->newLine();
        $this->info('=== SOLUSI ===');
        $this->info('Jika tiket diarsipkan, jalankan:');
        $this->info('  php artisan fix:ticket-visibility --unarchive');
        
        return 0;
    }
}
