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
        // Format kelas with academic year for better clarity
        $kelas = \App\Models\Kelas::orderBy('tahun_ajaran', 'desc')
            ->orderBy('nama_kelas')
            ->get()
            ->map(function($kelasItem) {
                $kelasItem->display_name = $kelasItem->nama_kelas . 
                    ($kelasItem->tahun_ajaran ? ' - ' . $kelasItem->tahun_ajaran : '');
                return $kelasItem;
            });
            
        return view('kepala.siswa.index', compact('siswa', 'kelas'));
    }
    
    public function cetak(Request $request)
    {
        $query = Siswa::with('kelas');
        if ($request->filled('kelas_id')) {
            $query->where('id_kelas', $request->kelas_id);
        }
        $siswa = $query->get();
        $kelas = \App\Models\Kelas::orderBy('tahun_ajaran', 'desc')
            ->orderBy('nama_kelas')
            ->get()
            ->map(function($kelasItem) {
                $kelasItem->display_name = $kelasItem->nama_kelas . 
                    ($kelasItem->tahun_ajaran ? ' - ' . $kelasItem->tahun_ajaran : '');
                return $kelasItem;
            });
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('kepala.siswa.cetak', compact('siswa', 'kelas'));
        return $pdf->stream('daftar-siswa-kepala-' . now()->format('Ymd-His') . '.pdf');
    }
} 