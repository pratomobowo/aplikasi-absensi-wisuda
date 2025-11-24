<?php

namespace App\Console\Commands;

use App\Models\BukuWisuda;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateBukuWisudaSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-buku-wisuda-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate slugs for existing buku wisuda records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bukuWisudas = BukuWisuda::whereNull('slug')->get();

        if ($bukuWisudas->isEmpty()) {
            $this->info('Tidak ada buku wisuda yang perlu di-update.');
            return 0;
        }

        $this->info('Generating slugs untuk ' . $bukuWisudas->count() . ' records...');

        foreach ($bukuWisudas as $buku) {
            $slug = Str::slug($buku->filename, '-');

            // Check for duplicate slugs
            $count = BukuWisuda::where('slug', 'like', $slug . '%')
                ->where('id', '!=', $buku->id)
                ->count();

            if ($count > 0) {
                $slug = $slug . '-' . ($count + 1);
            }

            $buku->update(['slug' => $slug]);
            $this->line("✓ Generated slug for: {$buku->filename} → {$slug}");
        }

        $this->info('Done! All buku wisuda records now have slugs.');
        return 0;
    }
}
