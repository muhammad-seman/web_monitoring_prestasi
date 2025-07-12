<?php

namespace App\Http\Controllers\Siswa;

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
        $siswa = $user->siswa;
        $query = $siswa ? $siswa->prestasi()->with(['kategoriPrestasi', 'tingkatPenghargaan', 'ekskul']) : PrestasiSiswa::query();
        if ($request->filled('kategori')) {
            $query->where('id_kategori_prestasi', $request->kategori);
        }
        if ($request->filled('tingkat')) {
            $query->where('id_tingkat_penghargaan', $request->tingkat);
        }
        if ($request->filled('ekskul')) {
            $query->where('id_ekskul', $request->ekskul);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from')) {
            $query->whereDate('tanggal_prestasi', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('tanggal_prestasi', '<=', $request->to);
        }
        $prestasi = $query->orderByDesc('tanggal_prestasi')->get();
        $kategori = KategoriPrestasi::pluck('nama_kategori', 'id');
        $tingkat = TingkatPenghargaan::pluck('tingkat', 'id');
        $ekskul = Ekstrakurikuler::pluck('nama', 'id');
        return view('siswa.prestasi.index', compact('prestasi', 'kategori', 'tingkat', 'ekskul'));
    }

    public function cetakSurat($id)
    {
        $user = Auth::user();
        $siswa = $user->siswa;
        $prestasi = PrestasiSiswa::where('id', $id)->where('id_siswa', $siswa->id)->with(['kategoriPrestasi', 'tingkatPenghargaan', 'ekskul'])->firstOrFail();
        $pdf = Pdf::loadView('siswa.prestasi.surat', compact('prestasi', 'siswa'));
        return $pdf->stream('surat-pernyataan-prestasi-'.$prestasi->id.'.pdf');
    }

    public function store(Request $request) { /* ...nanti... */ }
    public function update(Request $request, $id) { /* ...nanti... */ }
    public function destroy($id) { /* ...nanti... */ }
} 