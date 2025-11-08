<?php

use App\Http\Controllers\BukuWisudaController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\StudentAuthController;
use App\Livewire\Scanner;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'welcome'])->name('welcome');

// Public pages
Route::get('/data-wisudawan', App\Livewire\DataWisudawan::class)->name('data-wisudawan');
Route::get('/alur-wisuda', [PageController::class, 'alurWisuda'])->name('alur-wisuda');
Route::get('/buku-wisuda', App\Livewire\BukuWisuda::class)->name('buku-wisuda');
Route::get('/help-desk', [PageController::class, 'helpDesk'])->name('help-desk');

// Invitation routes with rate limiting (10 requests per minute per IP)
Route::middleware(['throttle:invitation'])->group(function () {
    Route::get('/invitation/{token}', [InvitationController::class, 'show'])
        ->name('invitation.show');
    Route::get('/invitation/{token}/download', [InvitationController::class, 'downloadPDF'])
        ->name('invitation.download');
});

// Scanner route - protected with authentication and rate limiting (30 requests per minute per user)
Route::get('/scanner', Scanner::class)
    ->middleware(['auth', 'throttle:scanner'])
    ->name('scanner');

// Buku Wisuda PDF serving routes - protected with student authentication
Route::middleware('auth:mahasiswa')->group(function () {
    Route::get('/buku-wisuda/viewer/{id}', App\Livewire\BukuWisudaViewer::class)->name('buku-wisuda.viewer');
    Route::get('/buku-wisuda/pdf/{id}', [BukuWisudaController::class, 'getPdf'])
        ->name('buku-wisuda.get-pdf');
    Route::get('/buku-wisuda/download/{id}', [BukuWisudaController::class, 'download'])
        ->name('buku-wisuda.download');
});

// Buku Wisuda admin routes - protected with admin authentication (auto-registered by Filament)
Route::middleware('auth')->group(function () {
    Route::get('/admin/buku-wisuda/viewer/{id}', App\Livewire\BukuWisudaAdminViewer::class)->name('buku-wisuda.admin-viewer');
    Route::get('/admin/buku-wisuda/pdf/{id}', [BukuWisudaController::class, 'getAdminPdf'])
        ->name('buku-wisuda.admin-pdf');
    Route::get('/admin/buku-wisuda/download/{id}', [BukuWisudaController::class, 'downloadAdmin'])
        ->name('buku-wisuda.admin-download');
});

// Student authentication routes
Route::prefix('student')->name('student.')->group(function () {
    // Guest routes (not authenticated)
    Route::middleware('guest:mahasiswa')->group(function () {
        Route::get('/login', App\Livewire\StudentLogin::class)->name('login');
    });

    // Protected routes (authenticated students only)
    Route::middleware('auth:mahasiswa')->group(function () {
        // Apply password change check to all authenticated routes except logout
        Route::middleware('check.mahasiswa.password.change')->group(function () {
            Route::get('/dashboard', App\Livewire\StudentDashboard::class)->name('dashboard');
            Route::middleware('no.cache')->group(function () {
                Route::get('/change-password', App\Livewire\StudentChangePassword::class)->name('change-password');
            });
        });

        Route::post('/logout', [StudentAuthController::class, 'logout'])->name('logout');
    });
});

// Filament routes are auto-registered by the Filament package
// Admin panel accessible at /admin
