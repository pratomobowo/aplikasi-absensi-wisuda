<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

class StudentDashboard extends Component
{
    use WithFileUploads;

    public $foto;
    public $mahasiswa;
    public $activeMenu = 'informasi'; // Default active menu
    public $undanganWisuda;

    public function mount()
    {
        $this->mahasiswa = Auth::guard('mahasiswa')->user();

        if (!$this->mahasiswa) {
            return redirect()->route('student.login');
        }

        // Load undangan wisuda if exists
        $this->loadUndangan();
    }

    public function loadUndangan()
    {
        // Get the latest graduation ticket for this mahasiswa
        $this->undanganWisuda = $this->mahasiswa->graduationTickets()->latest()->first();
    }

    public function setActiveMenu($menu)
    {
        $this->activeMenu = $menu;
    }

    public function uploadFoto()
    {
        $this->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'foto.required' => 'Foto wajib dipilih',
            'foto.image' => 'File harus berupa gambar',
            'foto.mimes' => 'Format foto harus JPG, JPEG, atau PNG',
            'foto.max' => 'Ukuran foto maksimal 2MB',
        ]);

        // Delete old photo if exists
        if ($this->mahasiswa->foto_wisuda) {
            Storage::disk('graduation_photos')->delete($this->mahasiswa->foto_wisuda);
        }

        // Get file extension
        $extension = $this->foto->getClientOriginalExtension();

        // Create filename with NPM
        $filename = $this->mahasiswa->npm . '.' . $extension;

        // Store the file
        $this->foto->storeAs('', $filename, 'graduation_photos');

        // Update database
        $this->mahasiswa->update([
            'foto_wisuda' => $filename
        ]);

        // Refresh mahasiswa data
        $this->mahasiswa->refresh();

        // Reset file input
        $this->reset('foto');

        session()->flash('success', 'Foto wisuda berhasil diupload!');
    }

    public function deleteFoto()
    {
        if ($this->mahasiswa->foto_wisuda) {
            // Delete file from storage
            Storage::disk('graduation_photos')->delete($this->mahasiswa->foto_wisuda);

            // Update database
            $this->mahasiswa->update([
                'foto_wisuda' => null
            ]);

            // Refresh mahasiswa data
            $this->mahasiswa->refresh();

            session()->flash('success', 'Foto wisuda berhasil dihapus!');
        }
    }

    #[Layout('layouts.student')]
    public function render()
    {
        return view('livewire.student-dashboard')->title('Dashboard Mahasiswa');
    }
}
