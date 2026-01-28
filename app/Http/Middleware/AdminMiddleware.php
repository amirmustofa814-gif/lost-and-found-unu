<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek: Apakah user login DAN role-nya 'admin'?
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request); // Silakan masuk
        }

        // Kalau bukan admin, lempar error 403 atau arahkan ke dashboard biasa
        return redirect('/dashboard')->with('error', 'Anda bukan Admin!');
    }
}