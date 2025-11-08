<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BukuWisuda;

class BukuWisudaAdminViewer extends Component
{
    public $bukuWisuda;
    public $pdfUrl;
    public $downloadUrl;
    public $id;

    public function mount($id)
    {
        // Get the buku wisuda by ID
        $this->bukuWisuda = BukuWisuda::findOrFail($id);

        if ($this->bukuWisuda) {
            // Generate PDF URL for inline viewing (using admin route)
            $this->pdfUrl = route('buku-wisuda.admin-pdf', ['id' => $this->bukuWisuda->id]);
            // Generate download URL (using admin route)
            $this->downloadUrl = route('buku-wisuda.admin-download', ['id' => $this->bukuWisuda->id]);
        }
    }

    public function render()
    {
        return view('livewire.buku-wisuda-admin-viewer')
            ->layout('layouts.app');
    }
}
