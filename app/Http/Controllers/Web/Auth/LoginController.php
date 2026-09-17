<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showForm()
    {
        if (Auth::guard('anggota')->check()) {
            return redirect()->route('home');
        }
        if (Auth::guard('pegawai')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'identifier' => ['required'],
            'password'   => ['required'],
            'role'       => ['required', 'in:siswa,petugas'],
        ]);

        \Log::info('[Login] Attempt', [
            'identifier' => $credentials['identifier'],
            'role' => $credentials['role'],
        ]);

        if ($credentials['role'] === 'siswa') {
            if (Auth::guard('anggota')->attempt(['nis' => $credentials['identifier'], 'password' => $credentials['password']])) {
                $request->session()->regenerate();
                \Log::info('[Login] Siswa success', ['user' => Auth::guard('anggota')->user()]);
                return redirect()->intended(route('home'));
            }
            \Log::warning('[Login] Siswa failed');
        }

        if ($credentials['role'] === 'petugas') {
            if (Auth::guard('pegawai')->attempt(['id_pegawai' => $credentials['identifier'], 'password' => $credentials['password']])) {
                $request->session()->regenerate();
                \Log::info('[Login] Pegawai success', ['user' => Auth::guard('pegawai')->user()]);
                return redirect()->intended(route('admin.dashboard'));
            }
            \Log::warning('[Login] Pegawai failed');
        }

        return back()->withErrors([
            'login' => 'NIS/ID atau password salah.',
        ])->onlyInput('identifier');
    }

    public function logout(Request $request)
    {
        Auth::guard('anggota')->logout();
        Auth::guard('pegawai')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
