<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Kunci pembatasan berdasarkan email dan alamat IP.
        $throttleKey = strtolower(trim($credentials['email']))
            . '|'
            . $request->ip();

        // Batasi maksimal 5 percobaan dalam 60 detik.
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()
                ->withErrors([
                    'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
                ])
                ->onlyInput('email');
        }

        if (!Auth::attempt($credentials)) {
            RateLimiter::hit($throttleKey, 60);

            return back()
                ->withErrors([
                    'email' => 'Email atau password salah.',
                ])
                ->onlyInput('email');
        }

        // Login berhasil: hapus hitungan gagal dan regenerasi sesi.
        RateLimiter::clear($throttleKey);

        $request->session()->regenerate();

        $user = Auth::user();

        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'petugas' => redirect()->route('petugas.dashboard'),
            'pimpinan' => redirect()->route('pimpinan.dashboard'),

            default => $this->invalidRole($request),
        };
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function invalidRole(Request $request): RedirectResponse
    {
        Auth::logout();

        // Bersihkan sesi apabila role pengguna tidak valid.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->withErrors([
                'email' => 'Role pengguna tidak valid.',
            ]);
    }
}
