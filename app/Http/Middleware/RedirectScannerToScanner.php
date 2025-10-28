<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectScannerToScanner
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If user is authenticated and is a scanner
        if (auth()->check() && auth()->user()->role === 'scanner') {
            // Allow access to /scanner, logout, and login routes
            if (!$request->is('scanner') && 
                !$request->is('admin/logout') && 
                !$request->is('admin/login') &&
                !$request->is('livewire/*')) {
                return redirect('/scanner');
            }
        }

        return $next($request);
    }
}
