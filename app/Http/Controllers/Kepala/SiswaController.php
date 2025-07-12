<?php

namespace App\Http\Controllers\Kepala;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Siswa::with('kelas');
        if ($request->filled('kelas_id')) {
            $query->where('id_kelas', $request->kelas_id);
        }
        $siswa = $query->paginate(15);
        $kelas = \App\Models\Kelas::all();
        return view('kepala.siswa.index', compact('siswa', 'kelas'));
    }
    
    public function cetak(Request $request)
    {
        $query = Siswa::with('kelas');
        if ($request->filled('kelas_id')) {
            $query->where('id_kelas', $request->kelas_id);
        }
        $siswa = $query->get();
        $kelas = \App\Models\Kelas::all();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('kepala.siswa.cetak', compact('siswa', 'kelas'));
        return $pdf->stream('daftar-siswa-kepala-' . now()->format('Ymd-His') . '.pdf');
    }
} 