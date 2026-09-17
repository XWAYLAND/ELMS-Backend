<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role)
    {
        if ($role === 'siswa') {
            if (!Auth::guard('anggota')->check()) {
                return redirect()->route('login');
            }
        }

        if ($role === 'petugas') {
            if (!Auth::guard('pegawai')->check()) {
                return redirect()->route('login');
            }
        }

        return $next($request);
    }
}