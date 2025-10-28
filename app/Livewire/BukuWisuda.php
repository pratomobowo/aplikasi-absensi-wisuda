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
        $query = GraduationEvent::query()
            ->withCount('graduationTickets')
            ->orderBy('date', 'desc');

        // Search filter
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $events = $query->paginate(10);

        return view('livewire.buku-wisuda', [
            'events' => $events,
        ])->layout('layouts.public')->title('Buku Wisuda');
    }
}
