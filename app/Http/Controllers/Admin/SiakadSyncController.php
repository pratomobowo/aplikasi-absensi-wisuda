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
        $mahasiswas = Mahasiswa::all(['id', 'npm', 'nama', 'program_studi', 'foto_wisuda']);
        
        $withPhoto = 0;
        $withoutPhoto = 0;
        
        foreach ($mahasiswas as $mhs) {
            if ($mhs->hasFotoWisuda()) {
                $withPhoto++;
            } else {
                $withoutPhoto++;
            }
        }
        
        $stats = [
            'total_mahasiswa' => $mahasiswas->count(),
            'with_photo' => $withPhoto,
            'without_photo' => $withoutPhoto,
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

            $previewData = [];
            foreach (array_slice($data, 0, 20) as $item) {
                $attr = $item['attributes'] ?? [];
                $nim = $attr['nim'] ?? null;
                
                if (!$nim) continue;

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

            $totalData = count($data);
            $existingCount = Mahasiswa::whereIn('npm', array_column(array_map(function($item) {
                return $item['attributes'] ?? [];
            }, $data), 'nim'))->count();

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
        $skipPhoto = !$request->boolean('download_photo', false); // Default: download photo
        
        $data = session()->get('siakad_preview_data');
        
        if (empty($data) || !is_array($data)) {
            return redirect()->route('admin.siakad-sync.index')
                ->with('error', 'Data preview tidak ditemukan. Silakan lakukan preview ulang.');
        }
        
        $jobId = Str::uuid()->toString();
        
        \Log::info('Siakad Sync: Dispatching job', [
            'job_id' => $jobId,
            'periode' => $periode,
            'data_count' => count($data),
            'download_photo' => !$skipPhoto,
        ]);
        
        // Dispatch ke queue (background) dengan auto-retry
        SyncSiakadJob::dispatch($jobId, $periode, $data, $skipPhoto);
        
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
}
