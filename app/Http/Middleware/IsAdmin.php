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
        // Cek apakah user sudah login dan role-nya BUKAN admin
        if (Auth::check() && strtolower(Auth::user()->role) !== 'admin') {
            return redirect()->route('rider.map')->with('error', 'Akses ditolak. Anda tidak memiliki izin ke area Admin.');
        }

        return $next($request);
    }
}