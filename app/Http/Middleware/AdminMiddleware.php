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
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Cek apakah user memiliki role admin
        if (Auth::user()->role === 'admin') {
            return $next($request);
        }

        /**
         * 3. Jika bukan admin, kita gunakan abort(403) untuk mencegah Infinite Loop.
         * Ini akan menghentikan request dan menampilkan halaman "Access Denied".
         * Jangan arahkan (redirect) kembali ke dashboard jika rute dashboard 
         * juga diproteksi oleh middleware ini.
         */
        abort(403, 'Akses ditolak! Halaman ini hanya untuk Administrator PT Saltek.');
    }
}