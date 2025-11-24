<?php

namespace App\Livewire;

use App\Models\BukuWisuda as BukuWisudaModel;
use App\Models\GraduationEvent;
use Livewire\Component;
use Livewire\WithPagination;

class BukuWisuda extends Component
{
    use WithPagination;

    protected $layout = null;

    public $search = '';
    public $selectedEvent = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedEvent' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedEvent()
    {
        $this->resetPage();
    }

    public function render()
    {
        $events = GraduationEvent::where('is_active', true)
            ->orHas('bukuWisuda')
            ->orderBy('created_at', 'desc')
            ->get();

        $query = BukuWisudaModel::query();

        // Filter by selected event
        if ($this->selectedEvent) {
            $query->where('graduation_event_id', $this->selectedEvent);
        }

        // Search by filename
        if ($this->search) {
            $query->where('filename', 'like', '%' . $this->search . '%')
                ->orWhereHas('graduationEvent', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
        }

        $bukuWisudas = $query->with('graduationEvent')
            ->orderBy('uploaded_at', 'desc')
            ->paginate(12);

        return view('livewire.buku-wisuda')
            ->layout('layouts.public')
            ->with([
                'bukuWisudas' => $bukuWisudas,
                'events' => $events,
            ])
            ->title('Buku Wisuda');
    }
}
