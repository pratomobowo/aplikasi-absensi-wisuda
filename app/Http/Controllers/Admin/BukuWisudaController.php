<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BukuWisuda;
use App\Models\GraduationEvent;
use App\Services\BukuWisudaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BukuWisudaController extends Controller
{
    protected $bukuWisudaService;

    public function __construct(BukuWisudaService $bukuWisudaService)
    {
        $this->bukuWisudaService = $bukuWisudaService;
    }

    public function index(Request $request)
    {
        $query = BukuWisuda::with('graduationEvent')
            ->whereHas('graduationEvent', function ($q) {
                $q->where('status', '!=', 'completed');
            });

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('filename', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('graduation_event_id')) {
            $query->where('graduation_event_id', $request->input('graduation_event_id'));
        }

        $bukuWisudas = $query->latest('uploaded_at')->paginate(15)->withQueryString();
        $events = GraduationEvent::where('status', '!=', 'completed')->pluck('name', 'id');
        
        $eventsWithBuku = BukuWisuda::whereHas('graduationEvent', function ($q) {
            $q->where('status', '!=', 'completed');
        })->pluck('graduation_event_id')->toArray();
        
        $eventsWithoutBuku = GraduationEvent::where('status', '!=', 'completed')
            ->whereNotIn('id', $eventsWithBuku)
            ->get();

        return view('admin.buku-wisuda.index', compact('bukuWisudas', 'events', 'eventsWithoutBuku'));
    }

    public function create()
    {
        $events = GraduationEvent::where('status', '!=', 'completed')->get();
        return view('admin.buku-wisuda.create', compact('events'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'graduation_event_id' => ['required', 'exists:graduation_events,id'],
            'file' => ['required', 'file', 'mimes:pdf', 'max:1024000'],
        ]);

        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        $path = $file->store('uploads', 'buku_wisuda');

        BukuWisuda::create([
            'graduation_event_id' => $data['graduation_event_id'],
            'status' => 'published',
            'filename' => $filename,
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'download_count' => 0,
            'uploaded_at' => now(),
        ]);

        return redirect()->route('admin.buku-wisuda.index')->with('success', 'Buku wisuda berhasil diupload.');
    }

    public function preview(GraduationEvent $event)
    {
        $preview = $this->bukuWisudaService->generatePreview($event);
        $bukuWisuda = BukuWisuda::where('graduation_event_id', $event->id)->first();
        
        return view('admin.buku-wisuda.preview', array_merge($preview, [
            'bukuWisuda' => $bukuWisuda,
            'event' => $event,
        ]));
    }

    public function generate(Request $request, GraduationEvent $event)
    {
        try {
            $bukuWisuda = $this->bukuWisudaService->generatePdf(
                $event,
                $request->user()->name ?? 'Admin'
            );

            return redirect()
                ->route('admin.buku-wisuda.preview', $event)
                ->with('success', 'Buku wisuda berhasil digenerate. Silakan review dan publish jika sudah OK.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.buku-wisuda.preview', $event)
                ->with('error', 'Gagal generate buku wisuda: ' . $e->getMessage());
        }
    }

    public function publish(BukuWisuda $bukuWisuda)
    {
        $this->bukuWisudaService->publish($bukuWisuda);
        
        return redirect()
            ->route('admin.buku-wisuda.preview', $bukuWisuda->graduation_event_id)
            ->with('success', 'Buku wisuda berhasil dipublish.');
    }

    public function edit(BukuWisuda $bukuWisuda)
    {
        $events = GraduationEvent::where('status', '!=', 'completed')->pluck('name', 'id');
        return view('admin.buku-wisuda.edit', compact('bukuWisuda', 'events'));
    }

    public function update(Request $request, BukuWisuda $bukuWisuda)
    {
        $data = $request->validate([
            'graduation_event_id' => ['required', 'exists:graduation_events,id'],
        ]);

        $bukuWisuda->update($data);

        return redirect()->route('admin.buku-wisuda.index')->with('success', 'Buku wisuda berhasil diperbarui.');
    }

    public function destroy(BukuWisuda $bukuWisuda)
    {
        if ($bukuWisuda->file_path) {
            Storage::disk('buku_wisuda')->delete($bukuWisuda->file_path);
        }

        $bukuWisuda->delete();

        return redirect()->route('admin.buku-wisuda.index')->with('success', 'Buku wisuda berhasil dihapus.');
    }
}