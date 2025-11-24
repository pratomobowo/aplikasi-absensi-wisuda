<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BukuWisuda;

class BukuWisudaViewer extends Component
{
    public $bukuWisuda;
    public $pdfUrl;
    public $downloadUrl;
    public $slug;

    public function mount($slug = null)
    {
        // If slug is provided (from route parameter), use it
        if ($slug) {
            $this->bukuWisuda = BukuWisuda::where('slug', $slug)->firstOrFail();
        } else {
            // Otherwise get from active graduation event (for dashboard)
            $event = \App\Models\GraduationEvent::active()->first();
            if ($event) {
                $this->bukuWisuda = $event->bukuWisuda()->first();
            }
        }

        if ($this->bukuWisuda) {
            // Generate PDF URL for inline viewing
            $this->pdfUrl = route('buku-wisuda.get-pdf', ['slug' => $this->bukuWisuda->slug]);
            // Generate download URL
            $this->downloadUrl = route('buku-wisuda.download', ['slug' => $this->bukuWisuda->slug]);
        }
    }

    public function render()
    {
        return view('livewire.buku-wisuda-viewer')
            ->layout('layouts.flipbook');
    }
}
