<?php

namespace App\Http\Controllers\Kepala;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\PrestasiSiswa;
use App\Models\Kelas;
use App\Models\Ekstrakurikuler;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $prestasiPerBulan = PrestasiSiswa::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as bulan, COUNT(*) as total')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // Top 5 Kelas dengan Prestasi Terbanyak
        $topKelasPrestasi = \App\Models\Kelas::select('kelas.nama_kelas', DB::raw('count(prestasi_siswa.id) as total_prestasi'))
            ->leftJoin('siswa', 'kelas.id', '=', 'siswa.id_kelas')
            ->leftJoin('prestasi_siswa', 'siswa.id', '=', 'prestasi_siswa.id_siswa')
            ->groupBy('kelas.id', 'kelas.nama_kelas')
            ->orderBy('total_prestasi', 'desc')
            ->limit(5)
            ->get();

        // Top 5 Ekstrakurikuler dengan Anggota Terbanyak
        $topEkskul = \App\Models\Ekstrakurikuler::select('ekstrakurikuler.nama', DB::raw('count(siswa_ekskul.id_siswa) as total_anggota'))
            ->leftJoin('siswa_ekskul', 'ekstrakurikuler.id', '=', 'siswa_ekskul.id_ekskul')
            ->groupBy('ekstrakurikuler.id', 'ekstrakurikuler.nama')
            ->orderBy('total_anggota', 'desc')
            ->limit(5)
            ->get();

        // Aktivitas Terbaru (opsional)
        $aktivitasTerbaru = class_exists('App\\Models\\ActivityLog') ? \App\Models\ActivityLog::with('user')->orderBy('created_at', 'desc')->limit(10)->get() : collect();

        // Prestasi Terbaru (opsional)
        $prestasiTerbaru = \App\Models\PrestasiSiswa::with(['siswa', 'kategoriPrestasi', 'tingkatPenghargaan'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
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
            'prestasiPerBulan',
            'topKelasPrestasi',
            'topEkskul',
            'aktivitasTerbaru',
            'prestasiTerbaru'
        ));
    }
} 