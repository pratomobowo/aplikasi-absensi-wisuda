<?php

namespace App\Livewire;

use App\Services\ActivityLogService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

class StudentLogin extends Component
{
    #[Rule('required', message: 'NPM wajib diisi')]
    public string $npm = '';

    #[Rule('required', message: 'Password wajib diisi')]
    public string $password = '';

    public bool $remember = false;

    public function login()
    {
        $this->validate();

        // Check if user exists and has a password set
        $mahasiswa = \App\Models\Mahasiswa::where('npm', $this->npm)->first();
        if (!$mahasiswa || !$mahasiswa->password) {
            $this->addError('npm', 'NPM atau password salah.');
            return;
        }

        if (Auth::guard('mahasiswa')->attempt([
            'npm' => $this->npm,
            'password' => $this->password
        ], $this->remember)) {
            session()->regenerate();

            // Log login activity
            $user = Auth::guard('mahasiswa')->user();
            ActivityLogService::logLogin($user?->nama ?? $user?->npm ?? 'Unknown');

            return $this->redirect('/student/dashboard', navigate: true);
        }

        $this->addError('npm', 'NPM atau password salah.');
    }

    #[Layout('layouts.student')]
    public function render()
    {
        return view('livewire.student-login')->title('Login Mahasiswa');
    }
}
