<?php

namespace App\Livewire;

use App\Models\Mahasiswa;
use App\Models\GraduationEvent;
use Livewire\Component;
use Livewire\WithPagination;

class BukuWisudaDigital extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedEvent = '';
    public $viewMode = 'flipbook'; // 'flipbook' or 'grid'
    public $currentPage = 1;
    public $totalPages = 1;

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedEvent' => ['except' => ''],
        'viewMode' => ['except' => 'flipbook'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
        $this->currentPage = 1;
    }

    public function updatingSelectedEvent()
    {
        $this->resetPage();
        $this->currentPage = 1;
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
        $this->currentPage = 1;
    }

    public function goToPage($page)
    {
        if ($page >= 1 && $page <= $this->totalPages) {
            $this->currentPage = $page;
        }
    }

    public function nextPage()
    {
        if ($this->currentPage < $this->totalPages) {
            $this->currentPage++;
        }
    }

    public function previousPage()
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
        }
    }

    public function getMahasiswasProperty()
    {
        $query = Mahasiswa::query();

        if ($this->selectedEvent) {
            $query->whereHas('graduationTickets', function ($q) {
                $q->whereHas('graduationEvent', function ($eq) {
                    $eq->where('id', $this->selectedEvent);
                });
            });
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('npm', 'like', '%' . $this->search . '%')
                    ->orWhere('program_studi', 'like', '%' . $this->search . '%');
            });
        }

        return $query->orderBy('nama')->get();
    }

    public function getEventsProperty()
    {
        return GraduationEvent::where('status', 'completed')
            ->orWhere('status', 'active')
            ->orderBy('date', 'desc')
            ->get();
    }

    public function getYudisiumColor($yudisium)
    {
        return match(strtolower($yudisium)) {
            'cum laude', 'dengan pujian' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'magna cum laude' => 'bg-amber-100 text-amber-800 border-amber-200',
            'summa cum laude' => 'bg-orange-100 text-orange-800 border-orange-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }

    public function getYudisiumIcon($yudisium)
    {
        return match(strtolower($yudisium)) {
            'cum laude', 'dengan pujian', 'magna cum laude', 'summa cum laude' => '⭐',
            default => '🎓',
        };
    }

    public function render()
    {
        $mahasiswas = $this->mahasiswas;
        $events = $this->events;
        
        // Calculate total pages for flipbook (2 items per page for desktop, 1 for mobile)
        $itemsPerPage = 2; // Desktop shows 2 per spread
        $this->totalPages = max(1, ceil($mahasiswas->count() / $itemsPerPage));
        
        // Ensure current page is valid
        if ($this->currentPage > $this->totalPages) {
            $this->currentPage = $this->totalPages;
        }

        return view('livewire.buku-wisuda-digital', [
            'mahasiswas' => $mahasiswas,
            'events' => $events,
            'totalPages' => $this->totalPages,
        ])->layout('layouts.public')->title('Buku Wisuda Digital');
    }
}
