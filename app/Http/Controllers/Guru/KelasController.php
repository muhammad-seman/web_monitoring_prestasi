<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kelas;

class KelasController extends Controller
{
    /**
     * Tampilkan daftar kelas yang diampu oleh guru (sebagai wali kelas).
     * Guru hanya bisa melihat kelas yang menjadi wali.
     */
    public function index()
    {
        $user = Auth::user();
        // Ambil semua kelas di mana user adalah wali kelas
        $kelas = Kelas::where('id_wali_kelas', $user->id)->get();

        return view('guru.kelas.index', [
            'kelas' => $kelas,
        ]);
    }
}
