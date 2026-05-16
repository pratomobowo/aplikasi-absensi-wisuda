<?php

namespace App\Jobs;

use App\Models\Mahasiswa;
use App\Services\SiakadService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DownloadPhotosJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $jobId;
    public array $filters;
    public bool $downloadAll;

    public int $timeout = 3600; // 1 jam timeout
    public int $tries = 1;

    public function __construct(string $jobId, array $filters, bool $downloadAll = false)
    {
        $this->jobId = $jobId;
        $this->filters = $filters;
        $this->downloadAll = $downloadAll;
    }

    public function handle(SiakadService $siakad): void
    {
        $query = Mahasiswa::query();

        if (!empty($this->filters['npm'])) {
            $query->where('npm', $this->filters['npm']);
        }

        if (!empty($this->filters['program_studi'])) {
            $query->where('program_studi', $this->filters['program_studi']);
        }

        if (!$this->downloadAll) {
            $query->whereNull('foto_wisuda');
        }

        $mahasiswas = $query->get();
        $total = $mahasiswas->count();

        if ($total === 0) {
            $this->updateProgress(0, 0, 'No data', [
                'downloaded' => 0,
                'skipped' => 0,
                'failed' => 0,
                'failedList' => [],
            ]);
            return;
        }

        $downloaded = 0;
        $skipped = 0;
        $failed = 0;
        $failedList = [];

        $this->updateProgress(0, $total, 'Starting...', compact('downloaded', 'skipped', 'failed', 'failedList'));

        foreach ($mahasiswas as $index => $mahasiswa) {
            try {
                $nim = $mahasiswa->npm;
                
                // Cek apakah foto tersedia di Sevima (HEAD request)
                if (!$siakad->checkFotoExists($nim)) {
                    $skipped++;
                    Log::info("Foto tidak tersedia untuk NIM {$nim}, dilewati");
                    $this->updateProgress($index + 1, $total, "Processing...", compact('downloaded', 'skipped', 'failed', 'failedList'));
                    continue;
                }

                // Hapus foto lama jika ada
                if ($mahasiswa->foto_wisuda) {
                    $oldPath = 'graduation-photos/' . $mahasiswa->foto_wisuda;
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                        Log::info("Foto lama dihapus untuk NIM {$nim}");
                    }
                }

                // Download foto baru
                $fotoPath = $siakad->downloadFoto($nim);
                
                if ($fotoPath) {
                    // Validasi file
                    $fullPath = Storage::disk('public')->path($fotoPath);
                    $fileSize = filesize($fullPath);
                    
                    if ($fileSize < 1024) { // Kurang dari 1KB, kemungkinan file placeholder/corrupt
                        Storage::disk('public')->delete($fotoPath);
                        $failed++;
                        $failedList[] = ['npm' => $nim, 'reason' => 'File too small (placeholder)'];
                        Log::warning("File foto terlalu kecil untuk NIM {$nim}: {$fileSize} bytes");
                    } else {
                        $mahasiswa->update(['foto_wisuda' => basename($fotoPath)]);
                        $downloaded++;
                        Log::info("Foto berhasil didownload untuk NIM {$nim}");
                    }
                } else {
                    $failed++;
                    $failedList[] = ['npm' => $nim, 'reason' => 'Download failed'];
                    Log::error("Gagal download foto untuk NIM {$nim}");
                }

                // Rate limiting: jeda 0.5 detik antar request
                usleep(500000);

            } catch (\Exception $e) {
                $failed++;
                $failedList[] = ['npm' => $mahasiswa->npm, 'reason' => $e->getMessage()];
                Log::error('Gagal download foto: ' . $e->getMessage(), ['npm' => $mahasiswa->npm]);
            }

            $this->updateProgress($index + 1, $total, "Processing...", compact('downloaded', 'skipped', 'failed', 'failedList'));
        }

        $this->updateProgress($total, $total, 'Completed', compact('downloaded', 'skipped', 'failed', 'failedList'));
    }

    private function updateProgress(int $current, int $total, string $status, array $stats): void
    {
        Cache::put("photo_download_{$this->jobId}", [
            'current' => $current,
            'total' => $total,
            'percentage' => $total > 0 ? round(($current / $total) * 100, 1) : 0,
            'status' => $status,
            'stats' => $stats,
            'updated_at' => now()->toIso8601String(),
        ], now()->addMinutes(30));
    }

    public function failed(\Throwable $exception): void
    {
        Cache::put("photo_download_{$this->jobId}", [
            'current' => 0,
            'total' => 0,
            'percentage' => 0,
            'status' => 'Failed',
            'error' => $exception->getMessage(),
            'updated_at' => now()->toIso8601String(),
        ], now()->addMinutes(30));

        Log::error('DownloadPhotosJob failed', [
            'job_id' => $this->jobId,
            'error' => $exception->getMessage(),
        ]);
    }
}
