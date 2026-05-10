<?php

namespace App\Console\Commands;

use App\Models\Mahasiswa;
use App\Services\SiakadService;
use Illuminate\Console\Command;

class FetchDataWisuda extends Command
{
    protected $signature   = 'wisuda:fetch {--periode= : Filter periode akademik, contoh: 20251} {--skip-foto : Lewati download foto}';
    protected $description = 'Fetch data kelulusan dari API SIAKAD Sevima';

    public function handle(SiakadService $siakad): int
    {
        $periode = $this->option('periode');

        if ($periode) {
            $this->info("Mengambil data kelulusan periode: {$periode}");
        } else {
            $this->info('Mengambil SEMUA data kelulusan...');
        }

        $data = $siakad->fetchKelulusan($periode);

        if (empty($data)) {
            $this->error('Tidak ada data yang diterima dari API.');
            return Command::FAILURE;
        }

        $this->info('Total data ditemukan: ' . count($data));

        if (!$this->confirm('Lanjut simpan ke database?')) {
            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar(count($data));
        $bar->start();

        $created = 0;
        $updated = 0;

        foreach ($data as $item) {
            $attr = $item['attributes'] ?? [];
            $nim  = $attr['nim'] ?? null;

            if (!$nim) continue;

            // Default password = NPM
            $password = bcrypt($nim);

            $mahasiswa = Mahasiswa::updateOrCreate(
                ['npm' => $nim],
                [
                    'nama'          => $attr['nama']                ?? '-',
                    'program_studi' => $attr['program_studi']       ?? '-',
                    'ipk'           => $attr['ipk_lulusan']         ?? 0,
                    'yudisium'      => ($attr['nama_predikat'] ?? '') !== '' ? $attr['nama_predikat'] : null,
                    'password'      => $password,
                ]
            );

            if ($mahasiswa->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }

            // Download foto
            if (!$this->option('skip-foto')) {
                $fotoPath = $siakad->downloadFoto($nim);
                if ($fotoPath) {
                    $mahasiswa->update(['foto_wisuda' => basename($fotoPath)]);
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Selesai! Total: {$created} baru, {$updated} diupdate, " . Mahasiswa::count() . ' total mahasiswa.');

        return Command::SUCCESS;
    }
}