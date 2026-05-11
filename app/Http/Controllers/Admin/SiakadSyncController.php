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

            // Simpan ke session (bukan flash data) agar tersedia saat sync
            session()->put('siakad_preview_data', $data);
            session()->put('siakad_preview_periode', $periode);

            return view('admin.siakad-sync.preview', compact(
                'previewData', 
                'periode', 
                'totalData', 
                'existingCount'
            ));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengambil data: ' . $e->getMessage());
        }
    }

    public function sync(Request $request)
    {
        $request->validate([
            'periode' => ['required', 'string', 'regex:/^\d{5}$/'],
        ]);

        $periode = $request->input('periode');
        
        // Ambil data dari session (bukan dari form input)
        $data = session()->get('siakad_preview_data');
        $sessionPeriode = session()->get('siakad_preview_periode');
        
        \Log::info('Siakad Sync: Starting sync from session', [
            'periode' => $periode,
            'session_periode' => $sessionPeriode,
            'data_count' => is_array($data) ? count($data) : 0,
            'has_session_data' => !is_null($data),
        ]);
        
        if (empty($data) || !is_array($data)) {
            \Log::error('Siakad Sync: Data tidak ditemukan di session', [
                'data_type' => gettype($data),
                'session_periode' => $sessionPeriode,
                'request_periode' => $periode,
            ]);
            
            return redirect()->route('admin.siakad-sync.index')
                ->with('error', 'Data preview tidak ditemukan. Silakan lakukan preview ulang.');
        }
        
        // Validasi periode cocok
        if ($sessionPeriode && $sessionPeriode !== $periode) {
            \Log::warning('Siakad Sync: Periode tidak cocok', [
                'session_periode' => $sessionPeriode,
                'request_periode' => $periode,
            ]);
        }
        
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

        // Hapus data dari session setelah sync selesai
        session()->forget(['siakad_preview_data', 'siakad_preview_periode']);
        
        \Log::info('Siakad Sync: Completed', [
            'created' => $created,
            'updated' => $updated,
            'failed' => $failed,
            'photo_downloaded' => $photoDownloaded,
        ]);

        $message = "Sync selesai! {$created} baru, {$updated} diupdate";
        if ($photoDownloaded > 0) {
            $message .= ", {$photoDownloaded} foto didownload";
        }
        if ($failed > 0) {
            $message .= ", {$failed} gagal";
        }

        return redirect()->route('admin.siakad-sync.index')->with('success', $message);
    }

    /**
     * Halaman download foto
     */
    public function photoIndex()
    {
        $stats = [
            'total_mahasiswa' => Mahasiswa::count(),
            'with_photo' => Mahasiswa::whereNotNull('foto_wisuda')->count(),
            'without_photo' => Mahasiswa::whereNull('foto_wisuda')->count(),
            'by_prodi' => Mahasiswa::selectRaw('program_studi, 
                count(*) as total,
                sum(case when foto_wisuda is not null then 1 else 0 end) as with_photo,
                sum(case when foto_wisuda is null then 1 else 0 end) as without_photo')
                ->groupBy('program_studi')
                ->orderBy('total', 'desc')
                ->get(),
        ];

        $programStudiList = Mahasiswa::select('program_studi')
            ->distinct()
            ->orderBy('program_studi')
            ->pluck('program_studi');

        return view('admin.siakad-sync.photo-index', compact('stats', 'programStudiList'));
    }

    /**
     * Download foto dari SEVIMA
     */
    public function downloadPhotos(Request $request)
    {
        $request->validate([
            'npm' => ['nullable', 'string'],
            'program_studi' => ['nullable', 'string'],
            'download_all' => ['nullable', 'boolean'],
        ]);

        $siakad = app(SiakadService::class);
        $downloaded = 0;
        $failed = 0;
        $skipped = 0;

        $query = Mahasiswa::query();

        if ($request->filled('npm')) {
            $query->where('npm', $request->input('npm'));
        }

        if ($request->filled('program_studi')) {
            $query->where('program_studi', $request->input('program_studi'));
        }

        if (!$request->boolean('download_all')) {
            $query->whereNull('foto_wisuda');
        }

        $mahasiswas = $query->get();

        if ($mahasiswas->isEmpty()) {
            return redirect()->route('admin.siakad-sync.photo')
                ->with('error', 'Tidak ada mahasiswa yang memenuhi kriteria.');
        }

        foreach ($mahasiswas as $mahasiswa) {
            try {
                if (!$request->boolean('download_all') && $mahasiswa->foto_wisuda) {
                    $skipped++;
                    continue;
                }

                $fotoPath = $siakad->downloadFoto($mahasiswa->npm);
                
                if ($fotoPath) {
                    $mahasiswa->update(['foto_wisuda' => basename($fotoPath)]);
                    $downloaded++;
                } else {
                    $failed++;
                }

                usleep(200000); // 0.2 detik

            } catch (\Exception $e) {
                $failed++;
                \Log::error('Gagal download foto: ' . $e->getMessage(), ['npm' => $mahasiswa->npm]);
            }
        }

        $message = "Download selesai! {$downloaded} berhasil";
        if ($skipped > 0) {
            $message .= ", {$skipped} dilewati (sudah ada foto)";
        }
        if ($failed > 0) {
            $message .= ", {$failed} gagal";
        }

        return redirect()->route('admin.siakad-sync.photo')
            ->with('success', $message);
    }

    /**
     * Preview foto (check apakah foto tersedia di server SEVIMA)
     */
    public function previewPhoto(Request $request)
    {
        $request->validate([
            'npm' => ['required', 'string'],
        ]);

        $npm = $request->input('npm');
        $url = config('services.foto.base_url') . "/{$npm}.jpg";

        try {
            $response = \Http::withoutVerifying()->head($url);
            $exists = $response->successful();
        } catch (\Exception $e) {
            $exists = false;
        }

        return response()->json([
            'npm' => $npm,
            'url' => $url,
            'exists' => $exists,
        ]);
    }
}