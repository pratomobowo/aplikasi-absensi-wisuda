<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust Cloudflare proxies
        $middleware->trustProxies(at: '*');

        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        // Redirect unauthenticated users to the correct guard login.
        $middleware->redirectGuestsTo(function ($request) {
            return $request->is('student') || $request->is('student/*')
                ? route('student.login')
                : '/admin/login';
        });

        // Register aliases for custom middleware
        $middleware->alias([
            'check.mahasiswa.password.change' => \App\Http\Middleware\CheckMahasiswaPasswordChange::class,
            'no.cache' => \App\Http\Middleware\NoCache::class,
            'admin.only' => \App\Http\Middleware\EnsureAdminUser::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
