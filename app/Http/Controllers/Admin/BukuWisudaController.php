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
        $grouped = $this->bukuWisudaService->groupByJurusan($preview['mahasiswa']);
        
        return view('admin.buku-wisuda.preview', array_merge($preview, [
            'bukuWisuda' => $bukuWisuda,
            'event' => $event,
            'grouped' => $grouped,
        ]));
    }

    public function uploadCoverAndSpeeches(Request $request, BukuWisuda $bukuWisuda)
    {
        $validated = $request->validate([
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:10240'],
            'sambutan_rektor' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:10240'],
            'sambutan_wakil_rektor_1' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:10240'],
            'sambutan_wakil_rektor_2' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:10240'],
            'sambutan_wakil_rektor_3' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:10240'],
        ]);

        $disk = Storage::disk('public');
        $updateData = [];

        $fields = [
            'cover_image',
            'sambutan_rektor',
            'sambutan_wakil_rektor_1',
            'sambutan_wakil_rektor_2',
            'sambutan_wakil_rektor_3',
        ];

        foreach ($fields as $field) {
            if ($request->hasFile($field)) {
                // Delete old file if exists
                if ($bukuWisuda->$field && $disk->exists('buku-wisuda/' . $bukuWisuda->$field)) {
                    $disk->delete('buku-wisuda/' . $bukuWisuda->$field);
                }

                $file = $request->file($field);
                $filename = $field . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('buku-wisuda', $filename, 'public');
                $updateData[$field] = basename($path);
            }
        }

        if (!empty($updateData)) {
            $bukuWisuda->update($updateData);
        }

        return redirect()
            ->route('admin.buku-wisuda.preview', $bukuWisuda->graduation_event_id)
            ->with('success', 'Cover dan sambutan berhasil diupload.');
    }

    public function uploadInitialPages(Request $request, int $id)
    {
        $request->validate([
            'initial_pages' => ['required', 'array', 'min:1'],
            'initial_pages.*' => ['required', 'file', 'mimes:png,webp,jpeg,jpg', 'max:512000'],
        ]);

        $bukuWisuda = BukuWisuda::findOrFail($id);
        $disk = Storage::disk('public');
        $uploadedFiles = [];

        foreach ($request->file('initial_pages') as $index => $file) {
            $extension = $file->getClientOriginalExtension();
            $filename = 'initial_page_' . ($index + 1) . '_' . time() . '.' . $extension;
            $path = $file->storeAs('buku-wisuda', $filename, 'public');
            $uploadedFiles[] = basename($path);
        }

        $existingPages = $bukuWisuda->initial_pages ?? [];
        $allPages = array_merge($existingPages, $uploadedFiles);

        $bukuWisuda->update(['initial_pages' => array_values($allPages)]);

        return redirect()
            ->route('admin.buku-wisuda.preview', $bukuWisuda->graduation_event_id)
            ->with('success', count($uploadedFiles) . ' halaman awal berhasil diupload. Total: ' . count($allPages) . ' halaman.');
    }

    public function deleteInitialPage(Request $request, int $id)
    {
        $bukuWisuda = BukuWisuda::findOrFail($id);
        $filename = $request->input('filename');

        if (!$filename) {
            return redirect()->back()->with('error', 'Nama file tidak valid.');
        }

        $disk = Storage::disk('public');
        if ($disk->exists('buku-wisuda/' . $filename)) {
            $disk->delete('buku-wisuda/' . $filename);
        }

        $pages = $bukuWisuda->initial_pages ?? [];
        $pages = array_values(array_filter($pages, fn($p) => $p !== $filename));
        $bukuWisuda->update(['initial_pages' => $pages ?: null]);

        return redirect()
            ->route('admin.buku-wisuda.preview', $bukuWisuda->graduation_event_id)
            ->with('success', 'Halaman berhasil dihapus. Total: ' . count($pages) . ' halaman.');
    }

    public function reorderInitialPages(Request $request, int $id)
    {
        $bukuWisuda = BukuWisuda::findOrFail($id);
        $order = $request->input('order', []);

        $pages = $bukuWisuda->initial_pages ?? [];
        $reordered = [];

        foreach ($order as $filename) {
            if (in_array($filename, $pages)) {
                $reordered[] = $filename;
            }
        }

        $bukuWisuda->update(['initial_pages' => $reordered ?: null]);

        return redirect()
            ->route('admin.buku-wisuda.preview', $bukuWisuda->graduation_event_id)
            ->with('success', 'Urutan halaman berhasil diperbarui.');
    }

    public function deleteCoverOrSpeech(Request $request, int $id)
    {
        $field = $request->input('field');
        $allowedFields = [
            'cover_image',
            'sambutan_rektor',
            'sambutan_wakil_rektor_1',
            'sambutan_wakil_rektor_2',
            'sambutan_wakil_rektor_3',
        ];

        if (!in_array($field, $allowedFields)) {
            return redirect()->back()->with('error', 'Field tidak valid.');
        }

        if ($bukuWisuda->$field) {
            $disk = Storage::disk('public');
            if ($disk->exists('buku-wisuda/' . $bukuWisuda->$field)) {
                $disk->delete('buku-wisuda/' . $bukuWisuda->$field);
            }
            $bukuWisuda->update([$field => null]);
        }

        return redirect()
            ->route('admin.buku-wisuda.preview', $bukuWisuda->graduation_event_id)
            ->with('success', 'File berhasil dihapus.');
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