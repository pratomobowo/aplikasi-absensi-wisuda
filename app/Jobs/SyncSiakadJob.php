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

    // Retry configuration - auto retry on failure/timeout
    public int $tries = 3;
    public int $timeout = 3600;
    public array $backoff = [60, 180, 300]; // Retry after 1min, 3min, 5min
    public bool $failOnTimeout = true;

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
        $logs = [];

        $logs[] = "[INFO] Memulai sinkronisasi {$total} data mahasiswa...";
        $logs[] = "[INFO] Periode: {$this->periode}";
        $logs[] = "[INFO] Download foto: " . ($this->skipPhoto ? 'Tidak' : 'Ya');
        
        $this->updateProgress(0, $total, 'Processing...', compact('created', 'updated', 'failed', 'photoDownloaded'), $logs);

        foreach ($this->data as $index => $item) {
            try {
                $attr = $item['attributes'] ?? [];
                $nim = $attr['nim'] ?? null;

                if (!$nim) {
                    $failed++;
                    $logs[] = "[WARN] Data ke-" . ($index + 1) . " tidak memiliki NIM, dilewati";
                    $this->updateProgress($index + 1, $total, "Processing...", compact('created', 'updated', 'failed', 'photoDownloaded'), $logs);
                    continue;
                }

                $nama = $attr['nama'] ?? '-';
                $logs[] = "[PROCESS] " . ($index + 1) . "/{$total} - {$nim} - {$nama}";

                $password = bcrypt($nim);

                $mahasiswa = Mahasiswa::updateOrCreate(
                    ['npm' => $nim],
                    [
                        'nama' => $attr['nama'] ?? '-',
                        'program_studi' => $attr['program_studi'] ?? '-',
                        'ipk' => $attr['ipk_lulusan'] ?? 0,
                        'yudisium' => ($attr['nama_predikat'] ?? '') !== '' ? $attr['nama_predikat'] : null,
                        'password' => $password,
                        'judul_skripsi' => !empty($attr['judul_skripsi']) ? strip_tags($attr['judul_skripsi']) : null,
                    ]
                );

                if ($mahasiswa->wasRecentlyCreated) {
                    $created++;
                    $logs[] = "[CREATE] ✓ Data baru ditambahkan: {$nama}";
                } else {
                    $updated++;
                    $logs[] = "[UPDATE] ✓ Data diperbarui: {$nama}";
                }

                if (!$this->skipPhoto) {
                    // Hapus foto lama jika ada
                    if ($mahasiswa->foto_wisuda) {
                        $oldPath = 'graduation-photos/' . $mahasiswa->foto_wisuda;
                        if (Storage::disk('public')->exists($oldPath)) {
                            Storage::disk('public')->delete($oldPath);
                            $logs[] = "[PHOTO] Foto lama dihapus: {$mahasiswa->foto_wisuda}";
                        }
                    }

                    $logs[] = "[PHOTO] Mencoba download foto untuk {$nim}...";
                    $fotoPath = $siakad->downloadFoto($nim);
                    if ($fotoPath) {
                        $mahasiswa->update(['foto_wisuda' => basename($fotoPath)]);
                        $photoDownloaded++;
                        $logs[] = "[PHOTO] ✓ Foto berhasil didownload: {$nim}.jpg";
                    } else {
                        $logs[] = "[PHOTO] ✗ Foto tidak tersedia/gagal download untuk {$nim}";
                    }
                }
            } catch (\Exception $e) {
                $failed++;
                $errorMsg = $e->getMessage();
                $logs[] = "[ERROR] ✗ Gagal memproses {$nim}: {$errorMsg}";
                Log::error('Gagal sync mahasiswa: ' . $e->getMessage(), ['nim' => $nim ?? 'unknown']);
            }

            $this->updateProgress($index + 1, $total, "Processing...", compact('created', 'updated', 'failed', 'photoDownloaded'), $logs);
        }

        $logs[] = "[DONE] Sinkronisasi selesai!";
        $logs[] = "[SUMMARY] Baru: {$created} | Update: {$updated} | Foto: {$photoDownloaded} | Gagal: {$failed}";
        
        $this->updateProgress($total, $total, 'Completed', compact('created', 'updated', 'failed', 'photoDownloaded'), $logs);
    }

    private function updateProgress(int $current, int $total, string $status, array $stats, array $logs = []): void
    {
        $existing = Cache::get("siakad_sync_{$this->jobId}");
        $allLogs = $existing['logs'] ?? [];
        $allLogs = array_merge($allLogs, $logs);
        
        // Keep only last 200 logs to prevent memory issues
        if (count($allLogs) > 200) {
            $allLogs = array_slice($allLogs, -200);
        }

        Cache::put("siakad_sync_{$this->jobId}", [
            'current' => $current,
            'total' => $total,
            'percentage' => $total > 0 ? round(($current / $total) * 100, 1) : 0,
            'status' => $status,
            'stats' => $stats,
            'logs' => $allLogs,
            'updated_at' => now()->toIso8601String(),
        ], now()->addMinutes(30));
    }

    public function failed(\Throwable $exception): void
    {
        $existing = Cache::get("siakad_sync_{$this->jobId}");
        $logs = $existing['logs'] ?? [];
        $logs[] = "[FATAL] ✗✗✗ Job gagal: " . $exception->getMessage();
        $logs[] = "[FATAL] Stack trace tersedia di log file";

        Cache::put("siakad_sync_{$this->jobId}", [
            'current' => $existing['current'] ?? 0,
            'total' => count($this->data),
            'percentage' => 0,
            'status' => 'Failed',
            'error' => $exception->getMessage(),
            'logs' => $logs,
            'updated_at' => now()->toIso8601String(),
        ], now()->addMinutes(30));

        Log::error('SyncSiakadJob failed', [
            'job_id' => $this->jobId,
            'error' => $exception->getMessage(),
        ]);
    }
}
