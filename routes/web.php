<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BukuWisudaController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GraduationEventController;
use App\Http\Controllers\Admin\GraduationTicketController;
use App\Http\Controllers\Admin\KonsumsiController;
use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Admin\SiakadSyncController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\BukuWisudaController as PublicBukuWisudaController;
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

// Buku Wisuda PDF serving routes - public (no authentication required)
Route::get('/buku-wisuda/viewer/{slug}', App\Livewire\BukuWisudaViewer::class)->name('buku-wisuda.viewer');
Route::get('/buku-wisuda/pdf/{slug}', [PublicBukuWisudaController::class, 'getPdf'])
    ->name('buku-wisuda.get-pdf');
Route::get('/buku-wisuda/download/{slug}', [PublicBukuWisudaController::class, 'download'])
    ->name('buku-wisuda.download');

// Buku Wisuda admin routes - protected with admin authentication
Route::middleware(['auth', 'admin.only'])->group(function () {
    Route::get('/admin/buku-wisuda/viewer/{slug}', App\Livewire\BukuWisudaAdminViewer::class)->name('buku-wisuda.admin-viewer');
    Route::get('/admin/buku-wisuda/pdf/{slug}', [PublicBukuWisudaController::class, 'getAdminPdf'])
        ->name('buku-wisuda.admin-pdf');
    Route::get('/admin/buku-wisuda/download/{slug}', [PublicBukuWisudaController::class, 'downloadAdmin'])
        ->name('buku-wisuda.admin-download');
});

// Student authentication routes
Route::prefix('student')->name('student.')->group(function () {
    // Guest routes (not authenticated)
    Route::middleware('guest:mahasiswa')->group(function () {
        Route::get('/login', App\Livewire\StudentLogin::class)
            ->middleware('throttle:student-login')
            ->name('login');
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

// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Auth routes
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login']);
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

    // Protected admin routes
    Route::middleware(['auth', 'admin.only'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Mahasiswa
        Route::get('/mahasiswa/export', [MahasiswaController::class, 'export'])->name('mahasiswa.export');
        Route::get('/mahasiswa/template', [MahasiswaController::class, 'downloadTemplate'])->name('mahasiswa.template');
        Route::post('/mahasiswa/import', [MahasiswaController::class, 'import'])->name('mahasiswa.import');
        Route::post('/mahasiswa/{mahasiswa}/reset-password', [MahasiswaController::class, 'resetPassword'])->name('mahasiswa.reset-password');
        Route::delete('/mahasiswa/bulk-delete', [MahasiswaController::class, 'bulkDelete'])->name('mahasiswa.bulk-delete');
        Route::resource('mahasiswa', MahasiswaController::class);

        // Graduation Events
        Route::post('/graduation-events/{graduation_event}/set-active', [GraduationEventController::class, 'setActive'])->name('graduation-events.set-active');
        Route::post('/graduation-events/{graduation_event}/set-status', [GraduationEventController::class, 'setStatus'])->name('graduation-events.set-status');
        Route::post('/graduation-events/{graduation_event}/generate-tickets', [GraduationEventController::class, 'generateTickets'])->name('graduation-events.generate-tickets');
        Route::get('/graduation-events/{graduation_event}/export-tickets', [GraduationEventController::class, 'exportTickets'])->name('graduation-events.export-tickets');
        Route::resource('graduation-events', GraduationEventController::class);

        // Graduation Tickets
        Route::post('/graduation-tickets/bulk-create', [GraduationTicketController::class, 'bulkCreate'])->name('graduation-tickets.bulk-create');
        Route::resource('graduation-tickets', GraduationTicketController::class);

        // Buku Wisuda
        Route::get('/buku-wisuda/preview/{event}', [BukuWisudaController::class, 'preview'])->name('buku-wisuda.preview');
        Route::post('/buku-wisuda/generate/{event}', [BukuWisudaController::class, 'generate'])->name('buku-wisuda.generate');
        Route::patch('/buku-wisuda/{bukuWisuda}/publish', [BukuWisudaController::class, 'publish'])->name('buku-wisuda.publish');
        Route::resource('buku-wisuda', BukuWisudaController::class);

        // Attendance
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');

        // Konsumsi
        Route::post('/konsumsi/{ticket}/toggle', [KonsumsiController::class, 'toggle'])->name('konsumsi.toggle');
        Route::post('/konsumsi/bulk-mark-received', [KonsumsiController::class, 'bulkMarkReceived'])->name('konsumsi.bulk-mark-received');
        Route::post('/konsumsi/bulk-mark-not-received', [KonsumsiController::class, 'bulkMarkNotReceived'])->name('konsumsi.bulk-mark-not-received');
        Route::get('/konsumsi', [KonsumsiController::class, 'index'])->name('konsumsi.index');

        // Users
        Route::resource('users', UserController::class);

        // Activity Logs
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/activity-logs/{activity_log}', [ActivityLogController::class, 'show'])->name('activity-logs.show');

        // Siakad Sync
        Route::get('/siakad-sync', [SiakadSyncController::class, 'index'])->name('siakad-sync.index');
        Route::post('/siakad-sync/preview', [SiakadSyncController::class, 'preview'])->name('siakad-sync.preview');
        Route::post('/siakad-sync/sync', [SiakadSyncController::class, 'sync'])->name('siakad-sync.sync');
        Route::get('/siakad-sync/progress/{job_id}', [SiakadSyncController::class, 'progress'])->name('siakad-sync.progress');
        
        // Download Foto
        Route::get('/siakad-sync/foto', [SiakadSyncController::class, 'photoIndex'])->name('siakad-sync.photo');
        Route::post('/siakad-sync/foto/download', [SiakadSyncController::class, 'downloadPhotos'])->name('siakad-sync.photo.download');
        Route::post('/siakad-sync/foto/preview', [SiakadSyncController::class, 'previewPhoto'])->name('siakad-sync.photo.preview');
    });
});