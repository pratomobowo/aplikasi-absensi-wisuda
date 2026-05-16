<?php

namespace App\Livewire;

use App\Models\BukuWisuda;
use App\Models\Mahasiswa;
use App\Models\GraduationEvent;
use Livewire\Component;
use Livewire\WithPagination;

class BukuWisudaDigital extends Component
{
    use WithPagination;

    public $slug;
    public $bukuWisuda;
    public $event;
    
    public $search = '';
    public $selectedProdi = '';
    public $viewMode = 'flipbook'; // 'flipbook' or 'grid'
    public $currentPage = 1;
    public $totalPages = 1;

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedProdi' => ['except' => ''],
        'viewMode' => ['except' => 'flipbook'],
    ];

    public function mount($slug)
    {
        $this->slug = $slug;
        $this->bukuWisuda = BukuWisuda::where('slug', $slug)
            ->whereIn('status', ['generated', 'published'])
            ->firstOrFail();
        $this->event = $this->bukuWisuda->graduationEvent;
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->currentPage = 1;
    }

    public function updatingSelectedProdi()
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

        // Filter by graduation event
        if ($this->event) {
            $query->whereHas('graduationTickets', function ($q) {
                $q->where('graduation_event_id', $this->event->id);
            });
        }

        // Filter by program studi
        if ($this->selectedProdi) {
            $query->where('program_studi', $this->selectedProdi);
        }

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('npm', 'like', '%' . $this->search . '%')
                    ->orWhere('program_studi', 'like', '%' . $this->search . '%');
            });
        }

        return $query->orderBy('program_studi')->orderBy('nama')->get();
    }

    public function getProgramStudiListProperty()
    {
        return Mahasiswa::whereHas('graduationTickets', function ($q) {
                $q->where('graduation_event_id', $this->event->id);
            })
            ->select('program_studi')
            ->distinct()
            ->orderBy('program_studi')
            ->pluck('program_studi');
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
        $prodiList = $this->programStudiList;
        
        // Calculate total pages for flipbook (2 items per page for desktop, 1 for mobile)
        $itemsPerPage = 2;
        $this->totalPages = max(1, ceil($mahasiswas->count() / $itemsPerPage));
        
        // Ensure current page is valid
        if ($this->currentPage > $this->totalPages) {
            $this->currentPage = $this->totalPages;
        }

        return view('livewire.buku-wisuda-digital', [
            'mahasiswas' => $mahasiswas,
            'prodiList' => $prodiList,
            'bukuWisuda' => $this->bukuWisuda,
            'event' => $this->event,
            'totalPages' => $this->totalPages,
        ])->layout('layouts.public')->title('Buku Wisuda - ' . ($this->event->name ?? 'Digital'));
    }
}
