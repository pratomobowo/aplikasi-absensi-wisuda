<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BukuWisuda;
use App\Models\Mahasiswa;
use App\Models\GraduationEvent;
use Illuminate\Support\Facades\Storage;

class BukuWisudaWebViewer extends Component
{
    use WithPagination;

    protected $layout = null;

    public $slug;
    public $bukuWisuda;
    public $event;
    
    public $search = '';
    public $selectedProdi = '';
    public $sidebarOpen = true;
    public $highlightedNpm = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function mount($slug)
    {
        $this->slug = $slug;
        $this->bukuWisuda = BukuWisuda::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();
        $this->event = $this->bukuWisuda->graduationEvent;
    }

    public function getInitialPagesProperty()
    {
        return $this->bukuWisuda->initial_pages ?? [];
    }

    public function getMahasiswasProperty()
    {
        return Mahasiswa::query()
            ->whereHas('graduationTickets', function ($q) {
                $q->where('graduation_event_id', $this->event->id);
            })
            ->orderBy('program_studi', 'asc')
            ->orderBy('nama', 'asc')
            ->get();
    }

    public function getGroupedMahasiswasProperty()
    {
        $grouped = [];
        foreach ($this->mahasiswas as $mhs) {
            $prodi = $mhs->program_studi ?? 'Lainnya';
            if (!isset($grouped[$prodi])) {
                $grouped[$prodi] = [];
            }
            $grouped[$prodi][] = $mhs;
        }
        return $grouped;
    }

    public function getProdiListProperty()
    {
        return array_keys($this->groupedMahasiswas);
    }

    public function getTotalPagesProperty()
    {
        return count($this->initialPages) + count($this->groupedMahasiswas);
    }

    public function getCurrentPageType($pageIndex)
    {
        if ($pageIndex < count($this->initialPages)) {
            return 'initial';
        }
        return 'student';
    }

    public function getProdiForPage($pageIndex)
    {
        $studentPageIndex = $pageIndex - count($this->initialPages);
        $prodis = array_keys($this->groupedMahasiswas);
        return $prodis[$studentPageIndex] ?? null;
    }

    public function updatedSearch($value)
    {
        if (empty($value)) {
            $this->highlightedNpm = '';
            return;
        }

        $searchLower = strtolower(trim($value));
        foreach ($this->groupedMahasiswas as $prodi => $students) {
            foreach ($students as $student) {
                if (str_contains(strtolower($student->nama), $searchLower) || 
                    str_contains(strtolower($student->npm), $searchLower)) {
                    $this->highlightedNpm = $student->npm;
                    $this->dispatch('scrollToStudent', ['npm' => $student->npm]);
                    return;
                }
            }
        }
    }

    public function clearSearch()
    {
        $this->search = '';
        $this->highlightedNpm = '';
    }

    public function toggleSidebar()
    {
        $this->sidebarOpen = !$this->sidebarOpen;
    }

    public function getPageNumber($prodi)
    {
        $prodis = array_keys($this->groupedMahasiswas);
        $index = array_search($prodi, $prodis);
        return count($this->initialPages) + $index; // 0-indexed
    }

    public function scrollToPage($pageIndex)
    {
        $this->dispatch('scrollToPage', ['pageIndex' => (int)$pageIndex]);
    }

    public function render()
    {
        return view('livewire.buku-wisuda-web-viewer', [
            'initialPages' => $this->initialPages,
            'groupedMahasiswas' => $this->groupedMahasiswas,
            'prodiList' => $this->prodiList,
            'totalPages' => $this->totalPages,
        ])->layout('layouts.buku-wisuda')->title('Buku Wisuda - ' . ($this->event->name ?? 'Digital'));
    }
}
