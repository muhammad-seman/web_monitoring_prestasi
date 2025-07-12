<?php

namespace App\Http\Controllers\Kepala;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\PrestasiSiswa;
use App\Models\Kelas;
use App\Models\Ekstrakurikuler;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik global sekolah
        $totalSiswa = Siswa::count();
        $totalPrestasi = PrestasiSiswa::count();
        $totalKelas = Kelas::count();
        $totalEkstrakurikuler = Ekstrakurikuler::count();
        
        // Prestasi berdasarkan status
        $prestasiPending = PrestasiSiswa::where('status', 'menunggu_validasi')->count();
        $prestasiApproved = PrestasiSiswa::where('status', 'diterima')->count();
        $prestasiRejected = PrestasiSiswa::where('status', 'ditolak')->count();
        
        // Prestasi berdasarkan tingkat
        $prestasiTingkat = PrestasiSiswa::selectRaw('tingkat_penghargaan.tingkat, COUNT(*) as total')
            ->join('tingkat_penghargaan', 'prestasi_siswa.id_tingkat_penghargaan', '=', 'tingkat_penghargaan.id')
            ->groupBy('tingkat_penghargaan.id', 'tingkat_penghargaan.tingkat')
            ->get();
        
        // Prestasi berdasarkan kategori
        $prestasiKategori = PrestasiSiswa::selectRaw('kategori_prestasi.nama_kategori, COUNT(*) as total')
            ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
            ->groupBy('kategori_prestasi.id', 'kategori_prestasi.nama_kategori')
            ->get();
        
        // Prestasi per bulan (6 bulan terakhir)
        $prestasiPerBulan = PrestasiSiswa::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        return view('kepala.dashboard', compact(
            'totalSiswa',
            'totalPrestasi', 
            'totalKelas',
            'totalEkstrakurikuler',
            'prestasiPending',
            'prestasiApproved',
            'prestasiRejected',
            'prestasiTingkat',
            'prestasiKategori',
            'prestasiPerBulan'
        ));
    }
} 