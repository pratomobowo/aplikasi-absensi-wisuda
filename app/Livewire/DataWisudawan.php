<?php

namespace App\Livewire;

use App\Models\Mahasiswa;
use Livewire\Component;
use Livewire\WithPagination;

class DataWisudawan extends Component
{
    use WithPagination;

    protected $layout = null;

    public $search = '';
    public $programStudi = '';
    public $yudisium = '';
    public $sortIpk = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'programStudi' => ['except' => ''],
        'yudisium' => ['except' => ''],
        'sortIpk' => ['except' => ''],
    ];

    public function paginationView()
    {
        return 'vendor.pagination.tailwind';
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingProgramStudi()
    {
        $this->resetPage();
    }

    public function updatingYudisium()
    {
        $this->resetPage();
    }

    public function updatingSortIpk()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->programStudi = '';
        $this->yudisium = '';
        $this->sortIpk = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = Mahasiswa::query();

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                  ->orWhere('npm', 'like', '%' . $this->search . '%');
            });
        }

        // Program Studi filter
        if ($this->programStudi) {
            $query->where('program_studi', $this->programStudi);
        }

        // Yudisium filter
        if ($this->yudisium) {
            $query->where('yudisium', $this->yudisium);
        }

        // Sort by IPK
        if ($this->sortIpk === 'tertinggi') {
            $query->orderBy('ipk', 'desc')->orderBy('nama', 'asc');
        } elseif ($this->sortIpk === 'terendah') {
            $query->orderBy('ipk', 'asc')->orderBy('nama', 'asc');
        } else {
            $query->orderBy('nama', 'asc');
        }

        $mahasiswa = $query->paginate(20);

        // Get unique program studi for filters
        $programStudiList = Mahasiswa::distinct()->pluck('program_studi')->filter()->sort()->values();

        // Get unique yudisium for filters
        $yudisiumList = Mahasiswa::distinct()->pluck('yudisium')->filter()->sort()->values();

        return view('livewire.data-wisudawan', [
            'mahasiswa' => $mahasiswa,
            'programStudiList' => $programStudiList,
            'yudisiumList' => $yudisiumList,
        ])->layout('layouts.public')->title('Data Wisudawan');
    }
}
