<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LayoutWisuda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LayoutWisudaController extends Controller
{
    public function index()
    {
        $layout = LayoutWisuda::first();
        return view('admin.layout-wisuda.index', compact('layout'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pdf' => ['required', 'file', 'mimes:pdf', 'max:512000'],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        $disk = Storage::disk('public');
        
        // Create layout-wisuda directory if not exists
        if (!$disk->exists('layout-wisuda')) {
            $disk->makeDirectory('layout-wisuda');
        }

        $layout = LayoutWisuda::first();
        $file = $request->file('pdf');

        // Delete old file if exists
        if ($layout && $layout->filename) {
            if ($disk->exists('layout-wisuda/' . $layout->filename)) {
                $disk->delete('layout-wisuda/' . $layout->filename);
            }
        }

        // Generate unique filename
        $filename = 'layout-' . time() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('layout-wisuda', $filename, 'public');

        $data = [
            'filename' => $filename,
            'original_filename' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
        ];

        if ($request->filled('title')) {
            $data['title'] = $request->title;
        }

        if ($layout) {
            $layout->update($data);
        } else {
            LayoutWisuda::create($data);
        }

        return redirect()
            ->route('admin.layout-wisuda.index')
            ->with('success', 'Layout wisuda berhasil diupload.');
    }

    public function destroy(LayoutWisuda $layout)
    {
        $disk = Storage::disk('public');
        
        if ($disk->exists('layout-wisuda/' . $layout->filename)) {
            $disk->delete('layout-wisuda/' . $layout->filename);
        }

        $layout->delete();

        return redirect()
            ->route('admin.layout-wisuda.index')
            ->with('success', 'Layout wisuda berhasil dihapus.');
    }
}
