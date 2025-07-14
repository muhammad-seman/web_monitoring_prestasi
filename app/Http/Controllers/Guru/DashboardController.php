<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\PrestasiSiswa;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Kelas yang diampu
        $kelas = Kelas::where('id_wali_kelas', $user->id)->get();
        $kelasIds = $kelas->pluck('id');
        // Siswa di kelas yang diampu
        $siswa = Siswa::whereIn('id_kelas', $kelasIds)->get();
        $siswaCount = $siswa->count();
        // Statistik prestasi siswa di kelas yang diampu
        $prestasi = PrestasiSiswa::whereIn('id_siswa', $siswa->pluck('id'))->get();
        $totalPrestasi = $prestasi->count();
        $prestasiDiterima = $prestasi->where('status', 'diterima')->count();
        $prestasiPending = $prestasi->where('status', 'menunggu_validasi')->count();
        $prestasiDitolak = $prestasi->where('status', 'ditolak')->count();
        // Grafik tren prestasi siswa per bulan (6 bulan terakhir)
        $prestasiPerBulan = PrestasiSiswa::selectRaw('DATE_FORMAT(tanggal_prestasi, "%Y-%m") as bulan, COUNT(*) as total')
            ->whereIn('id_siswa', $siswa->pluck('id'))
            ->where('tanggal_prestasi', '>=', now()->subMonths(6))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();
        // Donut chart prestasi siswa per kategori
        $prestasiPerKategori = PrestasiSiswa::selectRaw('kategori_prestasi.nama_kategori as kategori, COUNT(*) as total')
            ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
            ->whereIn('id_siswa', $siswa->pluck('id'))
            ->groupBy('kategori_prestasi.id', 'kategori_prestasi.nama_kategori')
            ->get();
        // Prestasi terbaru siswa di kelas yang diampu (limit 5)
        $prestasiTerbaru = PrestasiSiswa::whereIn('id_siswa', $siswa->pluck('id'))
            ->with(['siswa', 'kategori', 'tingkat', 'ekskul'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        return view('guru.dashboard', compact(
            'kelas', 'siswa', 'siswaCount', 'totalPrestasi', 'prestasiDiterima', 'prestasiPending', 'prestasiDitolak',
            'prestasiPerBulan', 'prestasiPerKategori', 'prestasiTerbaru'
        ));
    }
}
