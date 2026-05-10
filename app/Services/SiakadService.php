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

        echo "  Halaman 1/{$lastPage} (" . count($first->json('data') ?? []) . " data)\n";

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

            echo "  Halaman {$page}/{$lastPage} (" . count($data) . " data)\n";
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

            echo "  403 pada halaman {$params['page']}, percobaan {$attempt}/{$maxRetry}, tunggu 3 detik...\n";
            sleep(3);
        }

        return null;
    }

    public function downloadFoto(string $nim): ?string
    {
        $url  = "{$this->fotoBaseUrl}/{$nim}.jpg";
        $path = "graduation-photos/{$nim}.jpg";

        if (Storage::disk('public')->exists($path)) {
            return $path;
        }

        try {
            $response = Http::withoutVerifying()->get($url);

            if ($response->successful()) {
                Storage::disk('public')->put($path, $response->body());
                return $path;
            }
        } catch (\Exception $e) {
            Log::warning("Foto tidak ditemukan untuk NIM {$nim}: " . $e->getMessage());
        }

        return null;
    }
}