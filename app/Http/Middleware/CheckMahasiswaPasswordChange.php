<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMahasiswaPasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check if mahasiswa is authenticated
        if (auth('mahasiswa')->check()) {
            $mahasiswa = auth('mahasiswa')->user();

            // Check if password has never been changed (password_changed_at is NULL)
            if (!$mahasiswa->hasChangedPassword()) {
                // Exclude the change password route to prevent infinite redirects
                if (!$request->is('student/change-password')) {
                    return redirect()->route('student.change-password');
                }
            }
        }

        return $next($request);
    }
}
