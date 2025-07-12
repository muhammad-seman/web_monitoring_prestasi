<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Tampilkan form login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Proses login pengguna: bisa email/username.
     */
    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string', // input bisa username/email
            'password' => 'required|string',
        ]);

        // Cek: input email atau username
        $login_type = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [
            $login_type => $request->login,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redirect sesuai role
            $user = Auth::user();
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('admin.dashboard')->with('success', 'Selamat datang di Dashboard Admin');
                case 'guru':
                    return redirect()->route('guru.dashboard')->with('success', 'Selamat datang di Dashboard Guru');
                case 'siswa':
                    // Pastikan route siswa.dashboard sudah ada
                    return redirect()->route('siswa.dashboard')->with('success', 'Selamat datang di Dashboard Siswa');
                case 'wali':
                    // Pastikan route wali.dashboard sudah ada
                    return redirect()->route('wali.dashboard')->with('success', 'Selamat datang di Dashboard Wali');
                case 'kepala_sekolah':
                    // Pastikan route kepala_sekolah.dashboard sudah ada
                    return redirect()->route('kepala_sekolah.dashboard')->with('success', 'Selamat datang di Dashboard Kepala Sekolah');
                default:
                    Auth::logout();
                    return redirect()->route('login')->withErrors(['login' => 'Role tidak dikenal.']);
            }
        }

        return back()->withErrors([
            'login' => 'Username/email atau password salah.',
        ])->onlyInput('login');
    }

    /**
     * Logout pengguna.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('status', 'Anda berhasil logout');
    }
}
