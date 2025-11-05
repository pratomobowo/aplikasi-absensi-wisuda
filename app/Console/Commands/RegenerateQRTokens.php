<?php

namespace App\Console\Commands;

use App\Models\GraduationTicket;
use App\Services\QRCodeService;
use Illuminate\Console\Command;

class RegenerateQRTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:regenerate-qr {--ticket= : Regenerate for specific ticket ID} {--all : Regenerate all tickets}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate QR tokens for graduation tickets with proper encryption';

    protected QRCodeService $qrCodeService;

    public function __construct(QRCodeService $qrCodeService)
    {
        parent::__construct();
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Starting QR Token Regeneration...');
        $this->newLine();

        // Get tickets to regenerate
        if ($ticketId = $this->option('ticket')) {
            $tickets = GraduationTicket::where('id', $ticketId)->get();

            if ($tickets->isEmpty()) {
                $this->error("❌ Ticket ID {$ticketId} not found!");
                return 1;
            }
        } elseif ($this->option('all')) {
            $tickets = GraduationTicket::all();
        } else {
            // Interactive mode
            $totalTickets = GraduationTicket::count();

            if ($totalTickets === 0) {
                $this->warn('⚠️ No tickets found in database.');
                return 0;
            }

            $this->info("Found {$totalTickets} ticket(s) in database.");

            if (!$this->confirm('Do you want to regenerate QR tokens for all tickets?', true)) {
                $this->info('Operation cancelled.');
                return 0;
            }

            $tickets = GraduationTicket::all();
        }

        $this->info("Processing {$tickets->count()} ticket(s)...");
        $this->newLine();

        $bar = $this->output->createProgressBar($tickets->count());
        $bar->start();

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($tickets as $ticket) {
            try {
                // Load relationships
                $ticket->load(['mahasiswa', 'graduationEvent']);

                // Generate encrypted QR tokens
                $roles = ['mahasiswa', 'pendamping1', 'pendamping2'];

                foreach ($roles as $role) {
                    $data = [
                        'ticket_id' => $ticket->id,
                        'role' => $role,
                        'event_id' => $ticket->graduation_event_id,
                    ];

                    $encrypted = $this->qrCodeService->encryptQRData($data);

                    // Verify encryption worked
                    $decrypted = $this->qrCodeService->decryptQRData($encrypted);

                    if (!$decrypted) {
                        throw new \Exception("Failed to verify encrypted token for role: {$role}");
                    }

                    // Save to database
                    $tokenField = "qr_token_{$role}";
                    $ticket->$tokenField = $encrypted;
                }

                $ticket->save();

                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
                $errors[] = [
                    'ticket_id' => $ticket->id,
                    'mahasiswa' => $ticket->mahasiswa->nama ?? 'Unknown',
                    'error' => $e->getMessage(),
                ];
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Show results
        $this->info("✅ Successfully regenerated: {$successCount} ticket(s)");

        if ($errorCount > 0) {
            $this->error("❌ Failed: {$errorCount} ticket(s)");
            $this->warn('Errors:');

            foreach ($errors as $error) {
                $this->line("  - Ticket #{$error['ticket_id']} ({$error['mahasiswa']}): {$error['error']}");
            }
        }

        $this->newLine();
        $this->info('🎉 QR Token regeneration complete!');

        return 0;
    }
}
