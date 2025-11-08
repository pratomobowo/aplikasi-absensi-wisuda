<?php

namespace App\Http\Controllers;

use App\Models\BukuWisuda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class BukuWisudaController extends Controller
{
    /**
     * Get PDF file for flipbook viewer
     * Only authenticated users can access
     */
    public function getPdf($id)
    {
        // Get buku wisuda
        $buku = BukuWisuda::findOrFail($id);

        // Check if file exists
        if (!Storage::disk('buku_wisuda')->exists($buku->file_path)) {
            abort(404, 'File not found');
        }

        // Get file path
        $filePath = Storage::disk('buku_wisuda')->path($buku->file_path);

        // Record download
        $buku->recordDownload();

        // Return file for streaming
        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $buku->filename . '"',
        ]);
    }

    /**
     * Download PDF file
     */
    public function download($id)
    {
        $buku = BukuWisuda::findOrFail($id);

        if (!Storage::disk('buku_wisuda')->exists($buku->file_path)) {
            abort(404, 'File not found');
        }

        $filePath = Storage::disk('buku_wisuda')->path($buku->file_path);
        $buku->recordDownload();

        return response()->download($filePath, $buku->filename);
    }
}
