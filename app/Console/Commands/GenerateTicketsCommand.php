<?php

namespace App\Console\Commands;

use App\Models\GraduationEvent;
use App\Services\TicketService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateTicketsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:tickets
                            {--event= : ID of the graduation event (required)}
                            {--limit= : Maximum number of mahasiswa to process}
                            {--skip-existing : Skip if ticket already exists}
                            {--chunk=100 : Process in chunks of X records}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate graduation tickets for mahasiswa in bulk';

    protected TicketService $ticketService;

    public function __construct(TicketService $ticketService)
    {
        parent::__construct();
        $this->ticketService = $ticketService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $eventId = $this->option('event');
        $limit = $this->option('limit');
        $skipExisting = $this->option('skip-existing');
        $chunkSize = (int)$this->option('chunk');

        // Validate event ID
        if (!$eventId) {
            $this->error('❌ Event ID is required. Use --event={id}');
            return self::FAILURE;
        }

        $event = GraduationEvent::find($eventId);
        if (!$event) {
            $this->error("❌ Event with ID {$eventId} not found");
            return self::FAILURE;
        }

        $this->info("📋 Starting ticket generation for event: <fg=cyan>{$event->name}</> (ID: {$event->id})");
        $this->newLine();

        // Get mahasiswa to process
        $missingCount = $this->ticketService->getMissingTicketCount($event);

        if ($missingCount === 0 && !$limit) {
            $this->warn("⚠️  All mahasiswa already have tickets for this event");
            return self::SUCCESS;
        }

        $displayCount = $limit ?? $missingCount;
        $this->line("📊 Will process: <fg=yellow>{$displayCount}</> mahasiswa");

        // Start timer
        $startTime = microtime(true);

        // Generate tickets
        $result = $this->ticketService->generateTicketsForEvent($event, null, $skipExisting);

        // Calculate execution time
        $duration = microtime(true) - $startTime;
        $this->newLine();

        // Display results
        $this->displayResults($result, $duration);

        // Log to file
        Log::info('GenerateTicketsCommand: Command executed', [
            'event_id' => $event->id,
            'event_name' => $event->name,
            'results' => $result,
            'duration_seconds' => round($duration, 2),
        ]);

        // Return based on result
        if ($result['failed'] === 0) {
            return self::SUCCESS;
        } elseif ($result['failed'] === -1) {
            return self::FAILURE;
        } else {
            return self::INVALID;
        }
    }

    /**
     * Display formatted results
     *
     * @param array $result
     * @param float $duration
     * @return void
     */
    private function displayResults(array $result, float $duration): void
    {
        $this->info('═══════════════════════════════════════════');
        $this->line('<fg=green>✓ Generation completed!</>');
        $this->info('═══════════════════════════════════════════');
        $this->newLine();

        // Results table
        $this->table(
            ['Status', 'Count'],
            [
                [
                    '<fg=green>✓ Created</>',
                    "<fg=green>{$result['created']}</>"
                ],
                [
                    '<fg=yellow>⊘ Skipped</>',
                    "<fg=yellow>{$result['skipped']}</>"
                ],
                [
                    '<fg=red>✗ Failed</>',
                    "<fg=red>{$result['failed']}</>"
                ],
            ]
        );

        $this->newLine();
        $totalProcessed = $result['created'] + $result['skipped'] + $result['failed'];
        $this->line("<fg=cyan>⏱️  Total time: " . sprintf('%.2f', $duration) . " seconds</>");
        $this->line("<fg=cyan>📈 Throughput: " . sprintf('%.0f', $totalProcessed / $duration) . " tickets/sec</>");

        // Display errors if any
        if (!empty($result['errors'])) {
            $this->newLine();
            $this->error('Errors encountered:');
            foreach ($result['errors'] as $error) {
                $this->line("  <fg=red>•</> {$error}");
            }
        }
    }
}
