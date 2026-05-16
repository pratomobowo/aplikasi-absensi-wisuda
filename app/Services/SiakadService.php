<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SiakadService
{
    protected ?string $baseUrl;
    protected array  $headers;
    protected ?string $fotoBaseUrl;

    public function __construct()
    {
        $this->baseUrl     = config('services.siakad.url');
        $this->fotoBaseUrl = config('services.foto.base_url');
        $this->headers     = [
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
            'X-App-Key'    => config('services.siakad.app_key'),
            'X-Secret-Key' => config('services.siakad.secret_key'),
        ];

        if (empty($this->baseUrl)) {
            throw new \RuntimeException('Konfigurasi SIAKAD_API_URL belum diatur di file .env');
        }
    }

    public function fetchKelulusan(?string $periode = null): array
    {
        $allData = [];
        $params  = ['page' => 1];

        if ($periode) {
            $params['f-id_periode_akademik'] = $periode;
        }

        // Halaman pertama
        $first    = Http::withoutVerifying()->withHeaders($this->headers)->get("{$this->baseUrl}/kelulusan", $params);
        $lastPage = $first->json('meta.last_page') ?? 1;
        $allData  = array_merge($allData, $first->json('data') ?? []);

        Log::info("Fetch kelulusan halaman 1/{$lastPage} (" . count($first->json('data') ?? []) . " data)");

        // Halaman berikutnya
        for ($page = 2; $page <= $lastPage; $page++) {
            $params['page'] = $page;

            sleep(1); // jeda 1 detik antar request

            $response = $this->getWithRetry("{$this->baseUrl}/kelulusan", $params);

            if (!$response || $response->failed()) {
                Log::error('Gagal fetch halaman ' . $page);
                break;
            }

            $data    = $response->json('data') ?? [];
            $allData = array_merge($allData, $data);

            Log::info("Fetch kelulusan halaman {$page}/{$lastPage} (" . count($data) . " data)");
        }

        return $allData;
    }

    protected function getWithRetry(string $url, array $params, int $maxRetry = 3): ?\Illuminate\Http\Client\Response
    {
        for ($attempt = 1; $attempt <= $maxRetry; $attempt++) {
            $response = Http::withoutVerifying()
                ->withHeaders($this->headers)
                ->get($url, $params);

            if ($response->status() !== 403) {
                return $response;
            }

            Log::warning("403 pada halaman {$params['page']}, percobaan {$attempt}/{$maxRetry}, tunggu 3 detik...");
            sleep(3);
        }

        return null;
    }

    /**
     * Cek apakah foto tersedia di server SEVIMA
     */
    public function checkFotoExists(string $nim): bool
    {
        $url = "{$this->fotoBaseUrl}/{$nim}.jpg";

        try {
            $response = Http::withoutVerifying()
                ->timeout(5)
                ->head($url);

            return $response->successful() && $response->header('Content-Length') > 1024;
        } catch (\Exception $e) {
            Log::warning("Gagal cek foto untuk NIM {$nim}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Download foto dari SEVIMA dengan retry logic
     */
    public function downloadFoto(string $nim, int $maxRetries = 3): ?string
    {
        $url  = "{$this->fotoBaseUrl}/{$nim}.jpg";
        $path = "graduation-photos/{$nim}.jpg";

        // Cek apakah foto tersedia terlebih dahulu
        if (!$this->checkFotoExists($nim)) {
            Log::info("Foto tidak tersedia di server untuk NIM {$nim}");
            return null;
        }

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $response = Http::withoutVerifying()
                    ->timeout(30)
                    ->get($url);

                if ($response->successful()) {
                    $content = $response->body();
                    
                    // Validasi ukuran file
                    if (strlen($content) < 1024) {
                        Log::warning("File foto terlalu kecil untuk NIM {$nim}: " . strlen($content) . " bytes");
                        return null;
                    }

                    Storage::disk('public')->put($path, $content);
                    Log::info("Foto berhasil didownload untuk NIM {$nim} (percobaan {$attempt})");
                    return $path;
                }

                if ($response->status() === 404) {
                    Log::info("Foto tidak ditemukan (404) untuk NIM {$nim}");
                    return null;
                }

                Log::warning("Download foto gagal (status {$response->status()}) untuk NIM {$nim}, percobaan {$attempt}/{$maxRetries}");
                
                if ($attempt < $maxRetries) {
                    sleep(1);
                }

            } catch (\Exception $e) {
                Log::warning("Exception download foto NIM {$nim}: " . $e->getMessage() . " (percobaan {$attempt}/{$maxRetries})");
                
                if ($attempt < $maxRetries) {
                    sleep(1);
                }
            }
        }

        Log::error("Gagal download foto untuk NIM {$nim} setelah {$maxRetries} percobaan");
        return null;
    }
}