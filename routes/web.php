<?php

use App\Http\Controllers\InvitationController;
use App\Http\Controllers\PageController;
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

// Filament routes are auto-registered by the Filament package
// Admin panel accessible at /admin
