<?php

namespace App\Console\Commands;

use App\Models\Mahasiswa;
use Illuminate\Console\Command;

class GenerateMahasiswaPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-mahasiswa-password {--all : Generate password for all mahasiswa without password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate password for mahasiswa based on their NPM (hashed with bcrypt)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $all = $this->option('all');

        if ($all) {
            // Generate password for all mahasiswa without password
            $mahasiswaCount = Mahasiswa::whereNull('password')->count();

            if ($mahasiswaCount === 0) {
                $this->info('Semua mahasiswa sudah memiliki password.');
                return Command::SUCCESS;
            }

            $this->info("Generating password untuk {$mahasiswaCount} mahasiswa...");

            Mahasiswa::whereNull('password')
                ->chunk(100, function ($mahasiswas) {
                    foreach ($mahasiswas as $mahasiswa) {
                        $mahasiswa->update([
                            'password' => bcrypt($mahasiswa->npm),
                        ]);
                    }
                    $this->line("Processed chunk of {$mahasiswas->count()} records");
                });

            $this->info('✓ Password berhasil di-generate untuk semua mahasiswa!');
            return Command::SUCCESS;
        }

        // Show current status
        $withPassword = Mahasiswa::whereNotNull('password')->count();
        $withoutPassword = Mahasiswa::whereNull('password')->count();
        $total = Mahasiswa::count();

        $this->info("Status Password Mahasiswa:");
        $this->line("- Total mahasiswa: {$total}");
        $this->line("- Sudah punya password: {$withPassword}");
        $this->line("- Belum punya password: {$withoutPassword}");

        if ($withoutPassword > 0) {
            $this->warn("\nAda {$withoutPassword} mahasiswa yang belum memiliki password.");
            $this->info("Jalankan dengan flag --all untuk generate password:");
            $this->line("  php artisan app:generate-mahasiswa-password --all");
        }

        return Command::SUCCESS;
    }
}
