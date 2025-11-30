<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Cek apakah role user saat ini ada di daftar role yang diizinkan
        if (!in_array($request->user()->role, $roles)) {
            // Kalau tidak punya izin, tampilkan Error 403 (Forbidden)
            abort(403, 'MAAF! Anda tidak punya akses ke halaman ini.');
        }

        return $next($request);
    }
}