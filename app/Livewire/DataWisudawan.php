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
    public $fakultas = '';
    public $programStudi = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'fakultas' => ['except' => ''],
        'programStudi' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFakultas()
    {
        $this->resetPage();
        $this->programStudi = '';
    }

    public function updatingProgramStudi()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->fakultas = '';
        $this->programStudi = '';
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

        // Fakultas filter
        if ($this->fakultas) {
            $query->where('fakultas', $this->fakultas);
        }

        // Program Studi filter
        if ($this->programStudi) {
            $query->where('prodi', $this->programStudi);
        }

        $mahasiswa = $query->orderBy('nama', 'asc')->paginate(20);

        // Get unique fakultas and program studi for filters
        $fakultasList = Mahasiswa::distinct()->pluck('fakultas')->filter()->sort()->values();
        $programStudiList = Mahasiswa::when($this->fakultas, function ($q) {
            $q->where('fakultas', $this->fakultas);
        })->distinct()->pluck('prodi')->filter()->sort()->values();

        return view('livewire.data-wisudawan', [
            'mahasiswa' => $mahasiswa,
            'fakultasList' => $fakultasList,
            'programStudiList' => $programStudiList,
        ])->layout('layouts.public')->title('Data Wisudawan');
    }
}
