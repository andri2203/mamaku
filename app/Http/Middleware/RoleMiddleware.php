<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();

        // Kalau user belum login
        if (!$user) {
            return redirect()->route('login');
        }

        // Cek apakah level user ada di roles yang diperbolehkan
        if (!in_array($user->level, $roles)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Check Email Verified
        if (is_null($user->email_verified_at)) {
            return redirect()->route('verify.email')->with('error', 'Silakan verifikasi email Anda terlebih dahulu.');
        }

        return $next($request);
    }
}
