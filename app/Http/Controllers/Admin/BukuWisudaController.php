<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BukuWisuda;
use App\Models\GraduationEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BukuWisudaController extends Controller
{
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

        return view('admin.buku-wisuda.index', compact('bukuWisudas', 'events'));
    }

    public function create()
    {
        $events = GraduationEvent::where('status', '!=', 'completed')->pluck('name', 'id');
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
            'filename' => $filename,
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'download_count' => 0,
            'uploaded_at' => now(),
        ]);

        return redirect()->route('admin.buku-wisuda.index')->with('success', 'Buku wisuda berhasil diupload.');
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