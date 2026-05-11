<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Services\SiakadService;
use Illuminate\Http\Request;

class SiakadSyncController extends Controller
{
    public function index()
    {
        // Statistik data yang sudah ada
        $stats = [
            'total_mahasiswa' => Mahasiswa::count(),
            'with_photo' => Mahasiswa::whereNotNull('foto_wisuda')->count(),
            'without_photo' => Mahasiswa::whereNull('foto_wisuda')->count(),
            'by_prodi' => Mahasiswa::selectRaw('program_studi, count(*) as total')
                ->groupBy('program_studi')
                ->orderBy('total', 'desc')
                ->get(),
        ];

        return view('admin.siakad-sync.index', compact('stats'));
    }

    public function preview(Request $request)
    {
        $request->validate([
            'periode' => ['required', 'string', 'regex:/^\d{5}$/'],
        ]);

        $periode = $request->input('periode');
        $siakad = app(SiakadService::class);
        
        try {
            $data = $siakad->fetchKelulusan($periode);
            
            if (empty($data)) {
                return redirect()->back()->with('error', 'Tidak ada data ditemukan untuk periode ' . $periode);
            }

            // Transform data untuk preview
            $previewData = [];
            foreach (array_slice($data, 0, 20) as $item) {
                $attr = $item['attributes'] ?? [];
                $nim = $attr['nim'] ?? null;
                
                if (!$nim) continue;

                // Cek apakah sudah ada di database
                $existing = Mahasiswa::where('npm', $nim)->first();

                $previewData[] = [
                    'nim' => $nim,
                    'nama' => $attr['nama'] ?? '-',
                    'program_studi' => $attr['program_studi'] ?? '-',
                    'ipk' => $attr['ipk_lulusan'] ?? 0,
                    'yudisium' => $attr['nama_predikat'] ?? '-',
                    'exists' => $existing ? true : false,
                    'has_photo' => $existing ? ($existing->foto_wisuda ? true : false) : false,
                ];
            }

            // Hitung ringkasan
            $totalData = count($data);
            $existingCount = Mahasiswa::whereIn('npm', array_column(array_map(function($item) {
                return $item['attributes'] ?? [];
            }, $data), 'nim'))->count();

            return view('admin.siakad-sync.preview', compact(
                'previewData', 
                'periode', 
                'totalData', 
                'existingCount'
            ))->with('preview_data', $data);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengambil data: ' . $e->getMessage());
        }
    }

    public function sync(Request $request)
    {
        $request->validate([
            'periode' => ['required', 'string', 'regex:/^\d{5}$/'],
            'preview_data' => ['required', 'json'],
        ]);

        $periode = $request->input('periode');
        $previewDataRaw = $request->input('preview_data');
        
        // Log untuk debugging di production
        \Log::info('Siakad Sync: Starting sync', [
            'periode' => $periode,
            'preview_data_length' => strlen($previewDataRaw),
            'preview_data_size_kb' => round(strlen($previewDataRaw) / 1024, 2),
        ]);
        
        $data = json_decode($previewDataRaw, true);
        
        // Validasi json_decode
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            \Log::error('Siakad Sync: JSON decode failed', [
                'json_error' => json_last_error_msg(),
                'json_error_code' => json_last_error(),
                'preview_data_length' => strlen($previewDataRaw),
                'preview_data_sample' => substr($previewDataRaw, 0, 500),
            ]);
            
            return redirect()->route('admin.siakad-sync.index')
                ->with('error', 'Data tidak valid. Error: ' . json_last_error_msg());
        }
        
        if (empty($data) || !is_array($data)) {
            \Log::error('Siakad Sync: Data kosong atau bukan array', [
                'data_type' => gettype($data),
                'data_count' => is_array($data) ? count($data) : 0,
            ]);
            
            return redirect()->route('admin.siakad-sync.index')
                ->with('error', 'Data tidak valid. Data kosong atau format tidak sesuai.');
        }

        \Log::info('Siakad Sync: Data valid', ['count' => count($data)]);
        
        $siakad = app(SiakadService::class);

        $created = 0;
        $updated = 0;
        $failed = 0;
        $photoDownloaded = 0;
        $skipPhoto = $request->boolean('skip_foto', false);

        foreach ($data as $item) {
            try {
                $attr = $item['attributes'] ?? [];
                $nim = $attr['nim'] ?? null;

                if (!$nim) {
                    $failed++;
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

                // Download foto
                if (!$skipPhoto) {
                    $fotoPath = $siakad->downloadFoto($nim);
                    if ($fotoPath) {
                        $mahasiswa->update(['foto_wisuda' => basename($fotoPath)]);
                        $photoDownloaded++;
                    }
                }

            } catch (\Exception $e) {
                $failed++;
                \Log::error('Gagal sync mahasiswa: ' . $e->getMessage(), ['nim' => $nim ?? 'unknown']);
            }
        }

        $message = "Sync selesai! {$created} baru, {$updated} diupdate";
        if ($photoDownloaded > 0) {
            $message .= ", {$photoDownloaded} foto didownload";
        }
        if ($failed > 0) {
            $message .= ", {$failed} gagal";
        }

        return redirect()->route('admin.siakad-sync.index')->with('success', $message);
    }
}