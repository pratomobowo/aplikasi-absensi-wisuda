<?php

namespace App\Livewire;

use App\Services\ActivityLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
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

    public bool $showPassword = false;

    public function togglePasswordVisibility()
    {
        $this->showPassword = !$this->showPassword;
    }

    public function login()
    {
        $this->validate();

        $rateLimitKey = $this->rateLimitKey();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $this->addError('npm', "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.");
            return;
        }

        // Check if user exists and has a password set
        $mahasiswa = \App\Models\Mahasiswa::where('npm', $this->npm)->first();
        if (!$mahasiswa || !$mahasiswa->password) {
            RateLimiter::hit($rateLimitKey, 60);
            $this->addError('npm', 'NPM atau password salah.');
            return;
        }

        if (Auth::guard('mahasiswa')->attempt([
            'npm' => $this->npm,
            'password' => $this->password
        ], $this->remember)) {
            session()->regenerate();
            RateLimiter::clear($rateLimitKey);

            // Log login activity
            $user = Auth::guard('mahasiswa')->user();
            ActivityLogService::logLogin($user?->nama ?? $user?->npm ?? 'Unknown');

            return $this->redirect('/student/dashboard', navigate: true);
        }

        RateLimiter::hit($rateLimitKey, 60);
        $this->addError('npm', 'NPM atau password salah.');
    }

    private function rateLimitKey(): string
    {
        return 'student-login:' . strtolower($this->npm) . '|' . request()->ip();
    }

    #[Layout('layouts.student')]
    public function render()
    {
        return view('livewire.student-login')->title('Login Mahasiswa');
    }
}
