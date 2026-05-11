<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SyncSiakadJob;
use App\Models\Mahasiswa;
use App\Services\SiakadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

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
        $skipPhoto = $request->boolean('skip_foto', false);
        
        $data = session()->get('siakad_preview_data');
        $sessionPeriode = session()->get('siakad_preview_periode');
        
        if (empty($data) || !is_array($data)) {
            return redirect()->route('admin.siakad-sync.index')
                ->with('error', 'Data preview tidak ditemukan. Silakan lakukan preview ulang.');
        }
        
        $jobId = Str::uuid()->toString();
        
        \Log::info('Siakad Sync: Dispatching job', [
            'job_id' => $jobId,
            'periode' => $periode,
            'data_count' => count($data),
        ]);
        
        // Dispatch ke queue (background)
        SyncSiakadJob::dispatch($jobId, $periode, $data, $skipPhoto);
        
        // Hapus session data
        session()->forget(['siakad_preview_data', 'siakad_preview_periode']);
        
        return redirect()->route('admin.siakad-sync.progress', ['job_id' => $jobId]);
    }
    
    public function progress(Request $request, string $jobId)
    {
        $progress = Cache::get("siakad_sync_{$jobId}");
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($progress ?? [
                'current' => 0,
                'total' => 0,
                'percentage' => 0,
                'status' => 'Starting...',
            ]);
        }
        
        return view('admin.siakad-sync.progress', compact('jobId', 'progress'));
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