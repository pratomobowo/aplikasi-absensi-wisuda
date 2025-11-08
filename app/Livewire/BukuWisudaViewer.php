<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BukuWisuda;

class BukuWisudaViewer extends Component
{
    public $bukuWisuda;
    public $pdfUrl;
    public $downloadUrl;
    public $id;

    public function mount($id = null)
    {
        // If ID is provided (from route parameter), use it
        if ($id) {
            $this->bukuWisuda = BukuWisuda::findOrFail($id);
        } else {
            // Otherwise get from active graduation event (for dashboard)
            $event = \App\Models\GraduationEvent::active()->first();
            if ($event) {
                $this->bukuWisuda = $event->bukuWisuda()->first();
            }
        }

        if ($this->bukuWisuda) {
            // Generate PDF URL for inline viewing
            $this->pdfUrl = route('buku-wisuda.get-pdf', ['id' => $this->bukuWisuda->id]);
            // Generate download URL
            $this->downloadUrl = route('buku-wisuda.download', ['id' => $this->bukuWisuda->id]);
        }
    }

    public function render()
    {
        return view('livewire.buku-wisuda-viewer')
            ->layout('layouts.app');
    }
}
