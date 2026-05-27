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
                ->timeout(30)
                ->head($url);

            return $response->successful() && $response->header('Content-Length') > 1024;
        } catch (\Exception $e) {
            Log::warning("Gagal cek foto untuk NIM {$nim}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Download foto dari SEVIMA dengan retry logic
     * Skip checkFotoExists karena sering timeout DNS - langsung coba download saja
     */
    public function downloadFoto(string $nim, int $maxRetries = 3): ?string
    {
        $url  = "{$this->fotoBaseUrl}/{$nim}.jpg";
        $path = "graduation-photos/{$nim}.jpg";
        $disk = Storage::disk('public');

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $response = Http::withoutVerifying()
                    ->timeout(30)
                    ->get($url);

                if ($response->successful()) {
                    $content = $response->body();
                    $contentLength = strlen($content);
                    
                    // Validasi ukuran file
                    if ($contentLength < 1024) {
                        Log::warning("[downloadFoto] File terlalu kecil untuk NIM {$nim}: {$contentLength} bytes");
                        return null;
                    }
                    
                    // Validasi MIME type - pastikan ini gambar, bukan HTML error page
                    $finfo = new \finfo(FILEINFO_MIME_TYPE);
                    $mimeType = $finfo->buffer($content);
                    
                    if (!str_starts_with($mimeType, 'image/')) {
                        Log::error("[downloadFoto] Response bukan gambar untuk NIM {$nim}! MIME: {$mimeType}, Size: {$contentLength} bytes");
                        // Simpan untuk debug
                        $debugPath = "debug-foto/{$nim}_" . time() . '.txt';
                        $disk->put($debugPath, $content);
                        Log::info("[downloadFoto] Content disimpan ke {$debugPath} untuk debug");
                        return null;
                    }

                    // Hapus file lama jika ada
                    if ($disk->exists($path)) {
                        $disk->delete($path);
                        Log::info("[downloadFoto] File lama dihapus: {$path}");
                    }

                    // Pastikan folder graduation-photos ada
                    if (!$disk->exists('graduation-photos')) {
                        $disk->makeDirectory('graduation-photos');
                        Log::info("[downloadFoto] Folder graduation-photos dibuat");
                    }
                    
                    // Simpan file baru - periksa return value
                    $saved = $disk->put($path, $content);
                    
                    if (!$saved) {
                        Log::error("[downloadFoto] Storage::put() mengembalikan FALSE untuk NIM {$nim}");
                        Log::error("[downloadFoto] Path: {$path}, Size: " . strlen($content) . " bytes");
                        Log::error("[downloadFoto] Disk path: " . $disk->path(''));
                        Log::error("[downloadFoto] Writable: " . (is_writable($disk->path('')) ? 'Ya' : 'Tidak'));
                        return null;
                    }
                    
                    // Verifikasi file benar-benar tersimpan
                    clearstatcache();
                    if (!$disk->exists($path)) {
                        Log::error("[downloadFoto] File tidak tersimpan setelah put() untuk NIM {$nim}");
                        Log::error("[downloadFoto] Full path: " . $disk->path($path));
                        return null;
                    }
                    
                    $savedSize = $disk->size($path);
                    Log::info("[downloadFoto] ✓ Foto tersimpan untuk NIM {$nim}: {$path} ({$savedSize} bytes, {$mimeType})");
                    
                    return $path;
                }

                if ($response->status() === 404) {
                    Log::info("[downloadFoto] Foto tidak ditemukan (404) untuk NIM {$nim}");
                    return null;
                }

                Log::warning("[downloadFoto] HTTP {$response->status()} untuk NIM {$nim}, percobaan {$attempt}/{$maxRetries}");
                
                if ($attempt < $maxRetries) {
                    sleep(2);
                }

            } catch (\Exception $e) {
                $errorMsg = $e->getMessage();
                Log::warning("[downloadFoto] Exception NIM {$nim}: {$errorMsg} (percobaan {$attempt}/{$maxRetries})");
                
                if (strpos($errorMsg, 'Resolving timed out') !== false || strpos($errorMsg, 'Could not resolve') !== false) {
                    Log::warning("[downloadFoto] DNS timeout untuk NIM {$nim}, menunggu 5 detik...");
                    sleep(5);
                } elseif ($attempt < $maxRetries) {
                    sleep(2);
                }
            }
        }

        Log::error("[downloadFoto] Gagal untuk NIM {$nim} setelah {$maxRetries} percobaan");
        return null;
    }
}