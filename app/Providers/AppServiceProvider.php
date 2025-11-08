<?php

namespace App\Providers;

use App\Models\Mahasiswa;
use App\Models\User;
use App\Observers\MahasiswaObserver;
use App\Observers\UserObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();

        // Register model observers
        Mahasiswa::observe(MahasiswaObserver::class);
        User::observe(UserObserver::class);

        $this->configureRateLimiting();
        $this->configureQRCode();
    }

    /**
     * Configure QR code backend to use GD instead of Imagick.
     */
    protected function configureQRCode(): void
    {
        // Force QR code to use GD backend for better compatibility
        config(['simple-qrcode.backend' => 'gd']);
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // Rate limiter for magic link access (10 requests per minute per IP)
        RateLimiter::for('invitation', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        // Rate limiter for scanner API (30 requests per minute per user)
        RateLimiter::for('scanner', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
        });
    }
}
