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

                // Siapkan data update
                $updateData = [
                    'nama' => $attr['nama'] ?? '-',
                    'program_studi' => $attr['program_studi'] ?? '-',
                    'jenjang' => $attr['id_jenjang'] ?? null,
                    'ipk' => $attr['ipk_lulusan'] ?? 0,
                    'yudisium' => ($attr['nama_predikat'] ?? '') !== '' ? $attr['nama_predikat'] : null,
                ];
                
                // Jangan timpa password kalau mahasiswa sudah pernah mengganti password
                // mahasiswa yang password_changed_at = null berarti belum pernah rubah password
                $existingMahasiswa = Mahasiswa::where('npm', $nim)->first();
                if (!$existingMahasiswa || !$existingMahasiswa->password_changed_at) {
                    $updateData['password'] = bcrypt($nim);
                }
                
                // Hanya update judul_skripsi kalau ada datanya dari SIAKAD
                // Jangan timpa dengan null kalau data lokal sudah terisi
                if (!empty($attr['judul_skripsi'])) {
                    $updateData['judul_skripsi'] = strip_tags($attr['judul_skripsi']);
                    $logs[] = "[INFO] Judul skripsi ditemukan: " . substr(strip_tags($attr['judul_skripsi']), 0, 50) . "...";
                } else {
                    $logs[] = "[WARN] Judul skripsi kosong dari SIAKAD untuk NIM {$nim}";
                }
                
                $mahasiswa = Mahasiswa::updateOrCreate(
                    ['npm' => $nim],
                    $updateData
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
                    $oldFoto = $mahasiswa->foto_wisuda;
                    if ($oldFoto) {
                        $oldPath = 'graduation-photos/' . $oldFoto;
                        if (Storage::disk('public')->exists($oldPath)) {
                            Storage::disk('public')->delete($oldPath);
                            $logs[] = "[PHOTO] Foto lama dihapus: {$oldFoto}";
                        } else {
                            $logs[] = "[PHOTO] ⚠ Foto lama tidak ditemukan di storage: {$oldFoto}";
                        }
                    }

                    $logs[] = "[PHOTO] Mencoba download foto untuk {$nim}...";
                    $fotoPath = $siakad->downloadFoto($nim);
                    if ($fotoPath) {
                        $filename = basename($fotoPath);
                        $mahasiswa->update(['foto_wisuda' => $filename]);
                        
                        // Verifikasi foto benar-benar tersedia setelah update
                        $mahasiswa->refresh();
                        if ($mahasiswa->hasFotoWisuda()) {
                            $photoDownloaded++;
                            $logs[] = "[PHOTO] ✓ Foto OK: {$filename} (tersimpan & terverifikasi)";
                        } else {
                            $logs[] = "[PHOTO] ⚠ CRITICAL: Foto disimpan tapi tidak terverifikasi!";
                            $logs[] = "[PHOTO]   Filename: {$filename}";
                            $logs[] = "[PHOTO]   DB Value: {$mahasiswa->foto_wisuda}";
                            $logs[] = "[PHOTO]   Full Path: " . Storage::disk('public')->path('graduation-photos/' . $filename);
                            $logs[] = "[PHOTO]   Storage exists: " . (Storage::disk('public')->exists('graduation-photos/' . $filename) ? 'Ya' : 'Tidak');
                        }
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
