<?php

namespace App\Console\Commands;

use App\Models\GraduationEvent;
use App\Models\GraduationTicket;
use Illuminate\Console\Command;

class FixArchivedTicketsCommand extends Command
{
    protected $signature = 'fix:archived-tickets';
    
    protected $description = 'Arsipkan tiket completed, unarchive tiket active';

    public function handle(): int
    {
        $this->info('=== MEMPERBAIKI STATUS ARSIP TIKET ===\n');
        
        // 1. Arsipkan tiket untuk event completed
        $completedEvent = GraduationEvent::where('status', 'completed')->first();
        if ($completedEvent) {
            $count = GraduationTicket::where('graduation_event_id', $completedEvent->id)
                ->whereNull('archived_at')
                ->update(['archived_at' => now()]);
            $this->info("✓ {$count} tiket untuk event '{$completedEvent->name}' (completed) diarsipkan");
        }
        
        // 2. Unarchive tiket untuk event active
        $activeEvent = GraduationEvent::where('status', 'active')->first();
        if ($activeEvent) {
            $count = GraduationTicket::where('graduation_event_id', $activeEvent->id)
                ->whereNotNull('archived_at')
                ->update(['archived_at' => null]);
            $this->info("✓ {$count} tiket untuk event '{$activeEvent->name}' (active) di-unarchive");
        }
        
        // 3. Verifikasi
        $this->newLine();
        $this->info('=== HASIL AKHIR ===');
        
        $events = GraduationEvent::all();
        foreach ($events as $event) {
            $total = GraduationTicket::where('graduation_event_id', $event->id)->count();
            $active = GraduationTicket::where('graduation_event_id', $event->id)->whereNull('archived_at')->count();
            $archived = $total - $active;
            
            $this->info("{$event->name} ({$event->status}): {$active} aktif, {$archived} diarsip");
        }
        
        return 0;
    }
}
