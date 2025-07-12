<?php

namespace App\Http\Controllers\Kepala;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\PrestasiSiswa;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $query = Kelas::withCount('siswa')->with('guru');
        if ($request->filled('tahun_ajaran')) {
            $query->where('tahun_ajaran', $request->tahun_ajaran);
        }
        $kelas = $query->paginate(15);
        $tahunAjaran = Kelas::select('tahun_ajaran')->distinct()->orderBy('tahun_ajaran', 'desc')->pluck('tahun_ajaran');
        return view('kepala.kelas.index', compact('kelas', 'tahunAjaran'));
    }
    
    public function show(Kelas $kelas)
    {
        $kelas->load(['siswa', 'guru']);
        return view('kepala.kelas.show', compact('kelas'));
    }
    
    public function prestasiKelas(Kelas $kelas)
    {
        $prestasi = PrestasiSiswa::whereHas('siswa', function($query) use ($kelas) {
            $query->where('id_kelas', $kelas->id);
        })->with(['siswa', 'kategoriPrestasi', 'tingkatPenghargaan'])->paginate(15);
            
        return view('kepala.kelas.prestasi', compact('kelas', 'prestasi'));
    }
} 