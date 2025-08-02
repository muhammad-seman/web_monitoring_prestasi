<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Ekstrakurikuler;
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
        
        // Total prestasi semua siswa untuk percentage calculation
        $totalPrestasi = \App\Models\PrestasiSiswa::count();

        // Grafik tren prestasi semua siswa per bulan (6 bulan terakhir)
        $prestasiPerBulan = \App\Models\PrestasiSiswa::selectRaw('DATE_FORMAT(tanggal_prestasi, "%Y-%m") as bulan, COUNT(*) as total')
            ->where('tanggal_prestasi', '>=', now()->subMonths(6))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // Donut chart prestasi semua siswa per kategori
        $prestasiPerKategori = \App\Models\PrestasiSiswa::select('kategori_prestasi.nama_kategori as kategori', DB::raw('count(*) as total'))
            ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
            ->groupBy('kategori_prestasi.id', 'kategori_prestasi.nama_kategori')
            ->get();

        // Prestasi terbaru semua siswa (5 terakhir)
        $prestasiTerbaru = \App\Models\PrestasiSiswa::with(['siswa.kelas', 'kategori', 'tingkat', 'ekskul'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

        // Top 5 Kelas dengan Prestasi Terbanyak
        $topKelasPrestasi = Kelas::select('kelas.nama_kelas', DB::raw('count(prestasi_siswa.id) as total_prestasi'))
            ->leftJoin('siswa', 'kelas.id', '=', 'siswa.id_kelas')
            ->leftJoin('prestasi_siswa', 'siswa.id', '=', 'prestasi_siswa.id_siswa')
            ->groupBy('kelas.id', 'kelas.nama_kelas')
            ->orderBy('total_prestasi', 'desc')
            ->limit(5)
            ->get();

        // Top 5 Ekstrakurikuler dengan Prestasi Terbanyak
        $topEkskulPrestasi = Ekstrakurikuler::select('ekstrakurikuler.nama', DB::raw('count(prestasi_siswa.id) as total_prestasi'))
            ->leftJoin('prestasi_siswa', 'ekstrakurikuler.id', '=', 'prestasi_siswa.id_ekskul')
            ->groupBy('ekstrakurikuler.id', 'ekstrakurikuler.nama')
            ->orderBy('total_prestasi', 'desc')
            ->limit(5)
            ->get();

        return view('siswa.dashboard', compact('siswa', 'total', 'diterima', 'pending', 'ditolak', 'ekskulList', 'prestasiPerBulan', 'prestasiPerKategori', 'prestasiTerbaru', 'topKelasPrestasi', 'topEkskulPrestasi', 'totalPrestasi'));
    }
} 