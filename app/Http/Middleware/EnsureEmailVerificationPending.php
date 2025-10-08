<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailVerificationPending
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah ada email yang sedang diverifikasi
        if (!session()->has('pending_verification_email')) {
            return redirect('/login')->with('error', 'Tidak ada proses verifikasi email yang aktif.');
        }

        return $next($request);
    }
}
