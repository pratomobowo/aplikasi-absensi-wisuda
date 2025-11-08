<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentAuthController extends Controller
{
    /**
     * Handle student logout.
     */
    public function logout(Request $request)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $mahasiswaNama = $mahasiswa?->nama ?? $mahasiswa?->npm ?? 'Unknown';

        // Log logout activity
        ActivityLogService::logLogout($mahasiswaNama);

        Auth::guard('mahasiswa')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('student.login');
    }
}
