<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek: Apakah sudah login? DAN Apakah role-nya 'admin'?
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request); // Silakan masuk
        }

        // Kalau bukan admin, tendang keluar (Error 403 Forbidden)
        abort(403, 'AKSES DITOLAK: Halaman ini khusus Admin.');
    }
}