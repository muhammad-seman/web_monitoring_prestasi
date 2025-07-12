<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PrestasiSiswa;
use App\Models\KategoriPrestasi;
use App\Models\TingkatPenghargaan;
use App\Models\Ekstrakurikuler;
use Barryvdh\DomPDF\Facade\Pdf;

class PrestasiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Ambil ID anak-anak dari wali ini
        $anakIds = $user->anak()->pluck('id');
        
        $query = PrestasiSiswa::whereIn('id_siswa', $anakIds)
            ->with(['siswa', 'kategori', 'tingkat', 'ekskul', 'creator', 'validator'])
            ->orderByDesc('created_at');

        // Filter berdasarkan kategori
        if ($request->filled('kategori')) {
            $query->where('id_kategori_prestasi', $request->kategori);
        }
        
        // Filter berdasarkan tingkat
        if ($request->filled('tingkat')) {
            $query->where('id_tingkat_penghargaan', $request->tingkat);
        }
        
        // Filter berdasarkan ekskul
        if ($request->filled('ekskul')) {
            $query->where('id_ekskul', $request->ekskul);
        }
        
        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter berdasarkan tanggal
        if ($request->filled('from')) {
            $query->whereDate('tanggal_prestasi', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('tanggal_prestasi', '<=', $request->to);
        }

        $prestasi = $query->paginate(10)->appends($request->except('page'));
        
        // Data untuk filter
        $kategori = KategoriPrestasi::pluck('nama_kategori', 'id');
        $tingkat = TingkatPenghargaan::pluck('tingkat', 'id');
        $ekskul = Ekstrakurikuler::pluck('nama', 'id');
        
        return view('wali.prestasi.index', compact('prestasi', 'kategori', 'tingkat', 'ekskul'));
    }
    
    public function cetak(Request $request)
    {
        $user = Auth::user();
        $anakIds = $user->anak()->pluck('id');
        
        $query = PrestasiSiswa::whereIn('id_siswa', $anakIds)
            ->with(['siswa', 'kategori', 'tingkat']);

        if ($request->filled('kategori')) {
            $query->where('id_kategori_prestasi', $request->kategori);
        }
        if ($request->filled('from')) {
            $query->whereDate('tanggal_prestasi', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('tanggal_prestasi', '<=', $request->to);
        }

        $data['prestasi'] = $query->orderByDesc('tanggal_prestasi')->get();

        $pdf = Pdf::loadView('wali.prestasi.cetak', $data);
        return $pdf->stream('rekap-prestasi-anak-' . now()->format('Ymd-His') . '.pdf');
    }
}
