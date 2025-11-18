<?php

namespace App\Livewire;

use App\Models\GraduationEvent;
use Livewire\Component;
use Livewire\WithPagination;

class BukuWisuda extends Component
{
    use WithPagination;

    protected $layout = null;

    public $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Display coming soon page
        return view('livewire.buku-wisuda-coming-soon')
            ->layout('layouts.public')
            ->title('Buku Wisuda');
    }
}
