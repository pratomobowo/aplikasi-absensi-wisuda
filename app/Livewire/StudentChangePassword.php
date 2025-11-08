<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;

class StudentChangePassword extends Component
{
    public $current_password = '';
    public $new_password = '';
    public $confirm_password = '';
    public $message = '';
    public $message_type = '';


    public function rules()
    {
        return [
            'current_password' => 'required|min:1',
            'new_password' => 'required|min:8|different:current_password',
            'confirm_password' => 'required|same:new_password',
        ];
    }

    public function messages()
    {
        return [
            'current_password.required' => 'Password saat ini wajib diisi',
            'new_password.required' => 'Password baru wajib diisi',
            'new_password.min' => 'Password baru minimal 8 karakter',
            'new_password.different' => 'Password baru harus berbeda dari password saat ini',
            'confirm_password.required' => 'Konfirmasi password wajib diisi',
            'confirm_password.same' => 'Konfirmasi password tidak cocok dengan password baru',
        ];
    }

    public function changePassword()
    {
        $this->validate();

        $mahasiswa = auth('mahasiswa')->user();

        // Verify current password
        if (!Hash::check($this->current_password, $mahasiswa->password)) {
            $this->message = 'Password saat ini tidak benar';
            $this->message_type = 'error';
            return;
        }

        // Update password and mark as changed
        $mahasiswa->update([
            'password' => $this->new_password,
            'password_changed_at' => now(),
        ]);

        $this->message = 'Password berhasil diubah! Anda akan diarahkan ke dashboard...';
        $this->message_type = 'success';

        // Redirect to dashboard after 2 seconds
        $this->dispatch('passwordChanged');
    }

    public function render()
    {
        return view('livewire.student-change-password')
            ->layout('layouts.student');
    }
}
