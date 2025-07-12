<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\Kelas;

class SiswaController extends Controller
{
    /**
     * Tampilkan daftar siswa di kelas yang diampu oleh guru.
     */
    public function index()
    {
        // Ambil user yang login (guru)
        $user = Auth::user();

        // Cari semua kelas di mana user adalah wali kelas
        $kelas = Kelas::where('id_wali_kelas', $user->id)->get();

        $siswa = collect();
        if ($kelas->count() > 0) {
            // Ambil semua siswa dari semua kelas yang diampu
            foreach ($kelas as $kls) {
                $siswa = $siswa->concat($kls->siswa()->orderBy('nama')->get());
            }
            // Urutkan siswa berdasarkan nama setelah digabung
            $siswa = $siswa->sortBy('nama')->values();
        }

        return view('guru.siswa.index', [
            'siswa' => $siswa,
            'kelas' => $kelas,
        ]);
    }

    /**
     * Cetak data siswa di kelas yang diampu guru (PDF/print-friendly).
     */
    public function cetak()
    {
        $user = Auth::user();
        $kelas = Kelas::where('id_wali_kelas', $user->id)->get();
        
        $siswa = collect();
        if ($kelas->count() > 0) {
            // Ambil semua siswa dari semua kelas yang diampu
            foreach ($kelas as $kls) {
                $siswa = $siswa->concat($kls->siswa()->orderBy('nama')->get());
            }
            // Urutkan siswa berdasarkan nama setelah digabung
            $siswa = $siswa->sortBy('nama')->values();
        }

        return view('guru.siswa.cetak', [
            'siswa' => $siswa,
            'kelas' => $kelas,
        ]);
    }
}
