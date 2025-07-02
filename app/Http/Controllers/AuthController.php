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
                case 'kepala':
                    return redirect()->route('kepala.dashboard')->with('success', 'Selamat datang di Dashboard Kepala');
                case 'pegawai':
                    return redirect()->route('pegawai.dashboard')->with('success', 'Selamat datang di Dashboard Pegawai');
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
