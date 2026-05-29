<?php

namespace App\Livewire;

use App\Models\LayoutWisuda;
use Livewire\Component;

class HelpDesk extends Component
{
    public $pdfUrl;
    public $downloadUrl;
    public $title;

    public function mount()
    {
        $layout = LayoutWisuda::first();
        
        if ($layout) {
            $this->pdfUrl = $layout->url;
            $this->downloadUrl = $layout->url;
            $this->title = $layout->title;
        } else {
            $this->pdfUrl = null;
            $this->downloadUrl = null;
            $this->title = 'Layout Wisuda';
        }
    }

    public function render()
    {
        return view('livewire.help-desk')
            ->layout('layouts.public');
    }
}
