<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PrestasiSiswa;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user || !($user instanceof \App\Models\User) || !method_exists($user, 'anak')) {
            // Fallback jika bukan wali atau tidak ada relasi anak
            $anak = collect();
        } else {
            $anak = $user->anak()->with(['kelas', 'prestasi'])->get();
        }
        
        // Statistik prestasi semua siswa (bukan hanya anak)
        $totalPrestasi = PrestasiSiswa::count();
        $prestasiDiterima = PrestasiSiswa::where('status', 'diterima')->count();
        $prestasiMenunggu = PrestasiSiswa::whereIn('status', ['draft', 'menunggu_validasi'])->count();
        $prestasiDitolak = PrestasiSiswa::where('status', 'ditolak')->count();
        
        // Prestasi terbaru semua siswa (5 terakhir)
        $prestasiTerbaru = PrestasiSiswa::with(['siswa.kelas', 'kategori', 'tingkat', 'ekskul'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Grafik tren prestasi semua siswa per bulan (6 bulan terakhir)
        $prestasiPerBulan = \App\Models\PrestasiSiswa::selectRaw('DATE_FORMAT(tanggal_prestasi, "%Y-%m") as bulan, COUNT(*) as total')
            ->where('tanggal_prestasi', '>=', now()->subMonths(6))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // Donut chart prestasi semua siswa per kategori
        $prestasiPerKategori = \App\Models\PrestasiSiswa::selectRaw('kategori_prestasi.nama_kategori as kategori, COUNT(*) as total')
            ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
            ->groupBy('kategori_prestasi.id', 'kategori_prestasi.nama_kategori')
            ->get();

        return view('wali.dashboard', compact(
            'anak', 
            'totalPrestasi', 
            'prestasiDiterima', 
            'prestasiMenunggu', 
            'prestasiDitolak',
            'prestasiTerbaru',
            'prestasiPerBulan',
            'prestasiPerKategori'
        ));
    }
}
