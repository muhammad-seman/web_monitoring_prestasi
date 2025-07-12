<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Ekstrakurikuler;
use App\Models\SiswaEkskul;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EkstrakurikulerController extends Controller
{
    /**
     * Display a listing of ekstrakurikuler (read-only untuk guru).
     */
    public function index()
    {
        $user = Auth::user();
        
        // Ambil kelas yang diampu guru
        $kelasGuru = Kelas::where('id_wali_kelas', $user->id)->pluck('id');
        
        // Ambil semua ekstrakurikuler
        $ekstrakurikuler = Ekstrakurikuler::orderBy('nama')->get();
        
        // Untuk setiap ekstrakurikuler, hitung jumlah siswa dari kelas yang diampu guru
        foreach ($ekstrakurikuler as $ekskul) {
            $ekskul->jumlah_siswa_kelas = SiswaEkskul::where('id_ekskul', $ekskul->id)
                ->whereHas('siswa', function($q) use ($kelasGuru) {
                    $q->whereIn('id_kelas', $kelasGuru);
                })
                ->count();
        }

        return view('guru.ekstrakurikuler.index', compact('ekstrakurikuler'));
    }

    /**
     * Display the specified ekstrakurikuler dengan detail siswa peserta dari kelas yang diampu.
     */
    public function show(Ekstrakurikuler $ekstrakurikuler)
    {
        $user = Auth::user();
        
        // Ambil kelas yang diampu guru
        $kelasGuru = Kelas::where('id_wali_kelas', $user->id)->pluck('id');
        
        // Ambil siswa peserta dari kelas yang diampu guru
        $siswaPeserta = SiswaEkskul::with(['siswa.kelas'])
            ->where('id_ekskul', $ekstrakurikuler->id)
            ->whereHas('siswa', function($q) use ($kelasGuru) {
                $q->whereIn('id_kelas', $kelasGuru);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('guru.ekstrakurikuler.show', compact('ekstrakurikuler', 'siswaPeserta'));
    }
}
