<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $siswa = $user->siswa;
        $prestasi = $siswa ? $siswa->prestasi : collect();
        $total = $prestasi->count();
        $diterima = $prestasi->where('status', 'diterima')->count();
        $pending = $prestasi->where('status', 'menunggu_validasi')->count();
        $ditolak = $prestasi->where('status', 'ditolak')->count();
        $ekskulList = $siswa ? $siswa->ekstrakurikuler : collect();

        // Grafik tren prestasi pribadi per bulan (6 bulan terakhir)
        $prestasiPerBulan = $siswa ? 
            \App\Models\PrestasiSiswa::selectRaw('DATE_FORMAT(tanggal_prestasi, "%Y-%m") as bulan, COUNT(*) as total')
                ->where('id_siswa', $siswa->id)
                ->where('tanggal_prestasi', '>=', now()->subMonths(6))
                ->groupBy('bulan')
                ->orderBy('bulan')
                ->get() : collect();

        // Donut chart prestasi pribadi per kategori (pakai kategoriPrestasi, bukan kategori)
        $prestasiPerKategori = $siswa ?
            \App\Models\PrestasiSiswa::select('kategori_prestasi.nama_kategori as kategori', DB::raw('count(*) as total'))
                ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
                ->where('id_siswa', $siswa->id)
                ->groupBy('kategori_prestasi.id', 'kategori_prestasi.nama_kategori')
                ->get() : collect();

        // Prestasi terbaru (5 terakhir)
        $prestasiTerbaru = $siswa ?
            \App\Models\PrestasiSiswa::where('id_siswa', $siswa->id)
                ->with(['kategori', 'tingkat', 'ekskul'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get() : collect();

        return view('siswa.dashboard', compact('siswa', 'total', 'diterima', 'pending', 'ditolak', 'ekskulList', 'prestasiPerBulan', 'prestasiPerKategori', 'prestasiTerbaru'));
    }
} 