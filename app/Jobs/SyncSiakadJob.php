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

class SyncSiakadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $jobId;
    public string $periode;
    public array $data;
    public bool $skipPhoto;

    public function __construct(string $jobId, string $periode, array $data, bool $skipPhoto = false)
    {
        $this->jobId = $jobId;
        $this->periode = $periode;
        $this->data = $data;
        $this->skipPhoto = $skipPhoto;
    }

    public function handle(SiakadService $siakad): void
    {
        $total = count($this->data);
        $created = 0;
        $updated = 0;
        $failed = 0;
        $photoDownloaded = 0;

        $this->updateProgress(0, $total, 'Processing...', compact('created', 'updated', 'failed', 'photoDownloaded'));

        foreach ($this->data as $index => $item) {
            try {
                $attr = $item['attributes'] ?? [];
                $nim = $attr['nim'] ?? null;

                if (!$nim) {
                    $failed++;
                    $this->updateProgress($index + 1, $total, "Processing...", compact('created', 'updated', 'failed', 'photoDownloaded'));
                    continue;
                }

                $password = bcrypt($nim);

                $mahasiswa = Mahasiswa::updateOrCreate(
                    ['npm' => $nim],
                    [
                        'nama' => $attr['nama'] ?? '-',
                        'program_studi' => $attr['program_studi'] ?? '-',
                        'ipk' => $attr['ipk_lulusan'] ?? 0,
                        'yudisium' => ($attr['nama_predikat'] ?? '') !== '' ? $attr['nama_predikat'] : null,
                        'password' => $password,
                    ]
                );

                if ($mahasiswa->wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }

                if (!$this->skipPhoto) {
                    // Hapus foto lama jika ada
                    if ($mahasiswa->foto_wisuda) {
                        $oldPath = 'graduation-photos/' . $mahasiswa->foto_wisuda;
                        if (Storage::disk('public')->exists($oldPath)) {
                            Storage::disk('public')->delete($oldPath);
                        }
                    }

                    $fotoPath = $siakad->downloadFoto($nim);
                    if ($fotoPath) {
                        $mahasiswa->update(['foto_wisuda' => basename($fotoPath)]);
                        $photoDownloaded++;
                    }
                }
            } catch (\Exception $e) {
                $failed++;
                Log::error('Gagal sync mahasiswa: ' . $e->getMessage(), ['nim' => $nim ?? 'unknown']);
            }

            $this->updateProgress($index + 1, $total, "Processing...", compact('created', 'updated', 'failed', 'photoDownloaded'));
        }

        $this->updateProgress($total, $total, 'Completed', compact('created', 'updated', 'failed', 'photoDownloaded'));
    }

    private function updateProgress(int $current, int $total, string $status, array $stats): void
    {
        Cache::put("siakad_sync_{$this->jobId}", [
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
        Cache::put("siakad_sync_{$this->jobId}", [
            'current' => 0,
            'total' => count($this->data),
            'percentage' => 0,
            'status' => 'Failed',
            'error' => $exception->getMessage(),
            'updated_at' => now()->toIso8601String(),
        ], now()->addMinutes(30));

        Log::error('SyncSiakadJob failed', [
            'job_id' => $this->jobId,
            'error' => $exception->getMessage(),
        ]);
    }
}
