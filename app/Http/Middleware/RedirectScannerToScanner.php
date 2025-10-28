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
        // Skip middleware for login/logout routes to avoid interfering with authentication
        if ($request->is('admin/login') || $request->is('admin/logout')) {
            return $next($request);
        }

        // If user is authenticated and is a scanner
        if (auth()->check() && auth()->user()->role === 'scanner') {
            // Allow access to /scanner and livewire routes only
            if (!$request->is('scanner') && 
                !$request->is('scanner/*') &&
                !$request->is('livewire/*')) {
                return redirect('/scanner');
            }
        }

        return $next($request);
    }
}
