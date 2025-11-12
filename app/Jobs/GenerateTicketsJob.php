<?php

namespace App\Jobs;

use App\Models\GraduationEvent;
use App\Models\Mahasiswa;
use App\Services\TicketService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateTicketsJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 600; // 10 minutes

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Calculate the seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        return [10, 60, 300]; // 10s, 1m, 5m
    }

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Mahasiswa $mahasiswa,
        protected GraduationEvent $event,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(TicketService $ticketService): void
    {
        try {
            // Check if ticket already exists
            $existingTicket = $this->mahasiswa->graduationTickets()
                ->where('graduation_event_id', $this->event->id)
                ->first();

            if ($existingTicket) {
                Log::info('GenerateTicketsJob: Ticket already exists, skipping', [
                    'mahasiswa_id' => $this->mahasiswa->id,
                    'event_id' => $this->event->id,
                ]);
                return;
            }

            // Create ticket
            $ticketService->createTicket($this->mahasiswa, $this->event);

            Log::info('GenerateTicketsJob: Ticket generated successfully', [
                'mahasiswa_id' => $this->mahasiswa->id,
                'mahasiswa_name' => $this->mahasiswa->nama,
                'event_id' => $this->event->id,
                'event_name' => $this->event->name,
            ]);

        } catch (\Exception $e) {
            Log::error('GenerateTicketsJob: Failed to generate ticket', [
                'mahasiswa_id' => $this->mahasiswa->id,
                'mahasiswa_name' => $this->mahasiswa->nama,
                'event_id' => $this->event->id,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            // Re-throw for queue retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('GenerateTicketsJob: Job failed permanently', [
            'mahasiswa_id' => $this->mahasiswa->id,
            'mahasiswa_name' => $this->mahasiswa->nama,
            'event_id' => $this->event->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
