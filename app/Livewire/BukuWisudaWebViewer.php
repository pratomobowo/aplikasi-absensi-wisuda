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
            $jenjang = $mhs->jenjang ?? 'S1'; // default to S1 if null
            $prodi = $mhs->program_studi ?? 'Lainnya';
            
            if (!isset($grouped[$jenjang])) {
                $grouped[$jenjang] = [];
            }
            if (!isset($grouped[$jenjang][$prodi])) {
                $grouped[$jenjang][$prodi] = [];
            }
            $grouped[$jenjang][$prodi][] = $mhs;
        }
        
        // Sort by jenjang order: S1, S2, D3, etc.
        ksort($grouped);
        
        // Sort prodi within each jenjang
        foreach ($grouped as $jenjang => $prodis) {
            ksort($grouped[$jenjang]);
        }
        
        return $grouped;
    }

    public function getProdiListProperty()
    {
        // Returns array of ['jenjang' => 'S1', 'prodi' => 'Teknik Informatika']
        $list = [];
        foreach ($this->groupedMahasiswas as $jenjang => $prodis) {
            foreach (array_keys($prodis) as $prodi) {
                $list[] = ['jenjang' => $jenjang, 'prodi' => $prodi];
            }
        }
        return $list;
    }
    
    public function getJenjangListProperty()
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

    public function getPageNumber($jenjang, $prodi)
    {
        $pageIndex = count($this->initialPages);
        
        foreach ($this->groupedMahasiswas as $j => $prodis) {
            if ($j === $jenjang) {
                // Found the jenjang, now find the prodi index
                $prodiIndex = 0;
                foreach (array_keys($prodis) as $p) {
                    if ($p === $prodi) {
                        break;
                    }
                    $prodiIndex++;
                }
                return $pageIndex + $prodiIndex;
            }
            // Add all prodis count for this jenjang
            $pageIndex += count($prodis);
        }
        
        return $pageIndex;
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
            'jenjangList' => $this->jenjangList,
            'totalPages' => $this->totalPages,
        ])->layout('layouts.buku-wisuda')->title('Buku Wisuda - ' . ($this->event->name ?? 'Digital'));
    }
}
