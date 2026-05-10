<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BukuWisuda;

class BukuWisudaAdminViewer extends Component
{
    public $bukuWisuda;
    public $pdfUrl;
    public $downloadUrl;
    public $slug;

    public function mount($slug)
    {
        // Get the buku wisuda by slug
        $this->bukuWisuda = BukuWisuda::where('slug', $slug)->firstOrFail();

        if ($this->bukuWisuda) {
            // Generate PDF URL for inline viewing (using public route)
            $this->pdfUrl = route('buku-wisuda.get-pdf', ['slug' => $this->bukuWisuda->slug]);
            // Generate download URL (using public route)
            $this->downloadUrl = route('buku-wisuda.download', ['slug' => $this->bukuWisuda->slug]);
        }
    }

    public function render()
    {
        return view('livewire.buku-wisuda-admin-viewer')
            ->layout('layouts.app');
    }
}
