<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;

class ProfilController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $siswa = $user->siswa;
        return view('siswa.profil.index', compact('siswa', 'user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $siswa = $user->siswa;
        $validated = $request->validate([
            'alamat' => 'nullable|string|max:255',
        ]);
        if ($siswa) {
            $siswa->update($validated);
        }
        return redirect()->route('siswa.profil.index')->with('success', 'Profil berhasil diperbarui.');
    }
} 