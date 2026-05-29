<?php

namespace App\Livewire;

use Livewire\Component;

class HelpDesk extends Component
{
    public $pdfUrl;
    public $downloadUrl;

    public function mount()
    {
        $this->pdfUrl = asset('storage/LAYOUT WISUDA KE-23 USB YPKP 2025 GEL 1.pdf');
        $this->downloadUrl = asset('storage/LAYOUT WISUDA KE-23 USB YPKP 2025 GEL 1.pdf');
    }

    public function render()
    {
        return view('livewire.help-desk')
            ->layout('layouts.public');
    }
}
