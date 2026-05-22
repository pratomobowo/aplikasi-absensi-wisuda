<?php

namespace App\Console\Commands;

use App\Models\GraduationEvent;
use App\Models\GraduationTicket;
use Illuminate\Console\Command;

class FixTicketVisibilityCommand extends Command
{
    protected $signature = 'fix:ticket-visibility 
                            {--show-all : Tampilkan semua tiket termasuk yang diarsip}
                            {--unarchive : Kembalikan tiket dari arsip untuk event active}
                            {--event-id= : ID event tertentu untuk di-unarchive}';
    
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
                'ID' => $event->id,
                'Event' => $event->name,
                'Status' => $event->status,
                'Total' => $total,
                'Aktif' => $active,
                'Diarsip' => $total - $active,
            ];
        }
        
        $this->table(['ID', 'Event', 'Status', 'Total', 'Aktif', 'Diarsip'], $eventData);
        
        // 3. Unarchive option - HANYA untuk event active
        if ($this->option('unarchive') && $archived > 0) {
            $this->newLine();
            
            // Jika event-id di-specify, gunakan itu
            if ($this->option('event-id')) {
                $eventId = (int) $this->option('event-id');
                $event = GraduationEvent::find($eventId);
                
                if (!$event) {
                    $this->error("Event ID {$eventId} tidak ditemukan!");
                    return 1;
                }
                
                $this->info("Unarchive tiket untuk event: {$event->name} (ID: {$eventId})");
                
                $count = GraduationTicket::where('graduation_event_id', $eventId)
                    ->whereNotNull('archived_at')
                    ->update(['archived_at' => null]);
                
                $this->info("✓ {$count} tiket untuk event '{$event->name}' berhasil dikembalikan dari arsip!");
            } else {
                // Default: hanya unarchive event yang active
                $activeEvents = GraduationEvent::where('status', 'active')->get();
                
                if ($activeEvents->isEmpty()) {
                    $this->warn('Tidak ada event dengan status active.');
                    $this->info('Gunakan --event-id={id} untuk unarchive event tertentu.');
                    return 0;
                }
                
                $totalUnarchived = 0;
                foreach ($activeEvents as $event) {
                    $count = GraduationTicket::where('graduation_event_id', $event->id)
                        ->whereNotNull('archived_at')
                        ->update(['archived_at' => null]);
                    
                    if ($count > 0) {
                        $this->info("✓ {$count} tiket untuk event '{$event->name}' berhasil dikembalikan dari arsip!");
                        $totalUnarchived += $count;
                    }
                }
                
                if ($totalUnarchived === 0) {
                    $this->info('Tidak ada tiket yang perlu di-unarchive untuk event active.');
                } else {
                    $this->info("\nTotal: {$totalUnarchived} tiket di-unarchive");
                }
            }
        }
        
        // 4. Show all option
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
        $this->info('=== CARA PENGGUNAAN ===');
        $this->info('Unarchive semua event active:');
        $this->info('  php artisan fix:ticket-visibility --unarchive');
        $this->info('Unarchive event tertentu (ganti 2 dengan ID event):');
        $this->info('  php artisan fix:ticket-visibility --unarchive --event-id=2');
        
        return 0;
    }
}
