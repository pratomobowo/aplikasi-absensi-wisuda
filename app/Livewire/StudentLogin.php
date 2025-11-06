<?php

namespace App\Livewire;

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

        if (Auth::guard('mahasiswa')->attempt([
            'npm' => $this->npm,
            'password' => $this->password
        ], $this->remember)) {
            session()->regenerate();
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
