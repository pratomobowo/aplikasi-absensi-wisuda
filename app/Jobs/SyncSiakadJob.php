&lt;?php

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

class SyncSiakadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $jobId;
    public string $periode;
    public array $data;
    public bool $skipPhoto;

    public function __construct(string $jobId, string $periode, array $data, bool $skipPhoto = false)
    {
        $this-&gt;jobId = $jobId;
        $this-&gt;periode = $periode;
        $this-&gt;data = $data;
        $this-&gt;skipPhoto = $skipPhoto;
    }

    public function handle(SiakadService $siakad): void
    {
        $total = count($this-&gt;data);
        $created = 0;
        $updated = 0;
        $failed = 0;
        $photoDownloaded = 0;

        $this-&gt;updateProgress(0, $total, 'Processing...', compact('created', 'updated', 'failed', 'photoDownloaded'));

        foreach ($this-&gt;data as $index =&gt; $item) {
            try {
                $attr = $item['attributes'] ?? [];
                $nim = $attr['nim'] ?? null;

                if (!$nim) {
                    $failed++;
                    $this-&gt;updateProgress($index + 1, $total, "Processing...", compact('created', 'updated', 'failed', 'photoDownloaded'));
                    continue;
                }

                $password = bcrypt($nim);

                $mahasiswa = Mahasiswa::updateOrCreate(
                    ['npm' =&gt; $nim],
                    [
                        'nama' =&gt; $attr['nama'] ?? '-',
                        'program_studi' =&gt; $attr['program_studi'] ?? '-',
                        'ipk' =&gt; $attr['ipk_lulusan'] ?? 0,
                        'yudisium' =&gt; ($attr['nama_predikat'] ?? '') !== '' ? $attr['nama_predikat'] : null,
                        'password' =&gt; $password,
                    ]
                );

                if ($mahasiswa-&gt;wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }

                // Download foto
                if (!$this-&gt;skipPhoto) {
                    $fotoPath = $siakad-&gt;downloadFoto($nim);
                    if ($fotoPath) {
                        $mahasiswa-&gt;update(['foto_wisuda' =&gt; basename($fotoPath)]);
                        $photoDownloaded++;
                    }
                }

            } catch (\Exception $e) {
                $failed++;
                Log::error('Gagal sync mahasiswa: ' . $e-&gt;getMessage(), ['nim' =&gt; $nim ?? 'unknown']);
            }

            $this-&gt;updateProgress($index + 1, $total, "Processing...", compact('created', 'updated', 'failed', 'photoDownloaded'));
        }

        $this-&gt;updateProgress($total, $total, 'Completed', compact('created', 'updated', 'failed', 'photoDownloaded'));
    }

    private function updateProgress(int $current, int $total, string $status, array $stats): void
    {
        Cache::put("siakad_sync_{$this-&gt;jobId}", [
            'current' =&gt; $current,
            'total' =&gt; $total,
            'percentage' =&gt; $total &gt; 0 ? round(($current / $total) * 100, 1) : 0,
            'status' =&gt; $status,
            'stats' =&gt; $stats,
            'updated_at' =&gt; now()-&gt;toIso8601String(),
        ], now()-&gt;addMinutes(30));
    }

    public function failed(\Throwable $exception): void
    {
        Cache::put("siakad_sync_{$this-&gt;jobId}", [
            'current' =&gt; 0,
            'total' =&gt; count($this-&gt;data),
            'percentage' =&gt; 0,
            'status' =&gt; 'Failed',
            'error' =&gt; $exception-&gt;getMessage(),
            'updated_at' =&gt; now()-&gt;toIso8601String(),
        ], now()-&gt;addMinutes(30));

        Log::error('SyncSiakadJob failed', [
            'job_id' =&gt; $this-&gt;jobId,
            'error' =&gt; $exception-&gt;getMessage(),
        ]);
    }
}