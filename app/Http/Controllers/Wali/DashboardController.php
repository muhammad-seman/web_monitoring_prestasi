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
        
        // Statistik prestasi anak-anak
        $totalPrestasi = 0;
        $prestasiDiterima = 0;
        $prestasiMenunggu = 0;
        $prestasiDitolak = 0;
        
        foreach ($anak as $siswa) {
            $totalPrestasi += $siswa->prestasi->count();
            $prestasiDiterima += $siswa->prestasi->where('status', 'diterima')->count();
            $prestasiMenunggu += $siswa->prestasi->whereIn('status', ['draft', 'menunggu_validasi'])->count();
            $prestasiDitolak += $siswa->prestasi->where('status', 'ditolak')->count();
        }
        
        // Prestasi terbaru (5 terakhir)
        $prestasiTerbaru = PrestasiSiswa::whereIn('id_siswa', $anak->pluck('id'))
            ->with(['siswa', 'kategori', 'tingkat', 'ekskul'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Grafik tren prestasi anak per bulan (6 bulan terakhir)
        $prestasiPerBulan = \App\Models\PrestasiSiswa::selectRaw('DATE_FORMAT(tanggal_prestasi, "%Y-%m") as bulan, COUNT(*) as total')
            ->whereIn('id_siswa', $anak->pluck('id'))
            ->where('tanggal_prestasi', '>=', now()->subMonths(6))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // Donut chart prestasi anak per kategori
        $prestasiPerKategori = \App\Models\PrestasiSiswa::selectRaw('kategori_prestasi.nama_kategori as kategori, COUNT(*) as total')
            ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
            ->whereIn('id_siswa', $anak->pluck('id'))
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
