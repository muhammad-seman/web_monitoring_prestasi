<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Siswa;
use App\Models\PrestasiSiswa;
use App\Models\Kelas;
use App\Models\Ekstrakurikuler;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Statistik Pengguna
            $totalSiswa = Siswa::count();
            $totalGuru = User::where('role', 'guru')->count();
            $totalWaliKelas = User::where('role', 'wali_kelas')->count();
            $totalKepalaSekolah = User::where('role', 'kepala_sekolah')->count();
            $totalAdmin = User::where('role', 'admin')->count();

            // Statistik Prestasi
            $totalPrestasi = PrestasiSiswa::count();
            $prestasiTervalidasi = PrestasiSiswa::where('status', 'diterima')->count();
            $prestasiPending = PrestasiSiswa::where('status', 'menunggu_validasi')->count();
            $prestasiDitolak = PrestasiSiswa::where('status', 'ditolak')->count();

            // Statistik Kelas
            $totalKelas = Kelas::count();
            $rataRataSiswaPerKelas = $totalKelas > 0 ? round($totalSiswa / $totalKelas, 1) : 0;

            // Statistik Ekstrakurikuler
            $totalEkskul = Ekstrakurikuler::count();
            $totalAnggotaEkskul = DB::table('siswa_ekskul')->count();

            // Prestasi per Kategori (nama_kategori)
            $prestasiPerKategori = PrestasiSiswa::select('kategori_prestasi.nama_kategori as kategori', DB::raw('count(*) as total'))
                ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
                ->groupBy('kategori_prestasi.id', 'kategori_prestasi.nama_kategori')
                ->get();

            // Prestasi per Tingkat (tingkat)
            $prestasiPerTingkat = PrestasiSiswa::select('tingkat_penghargaan.tingkat as tingkat', DB::raw('count(*) as total'))
                ->join('tingkat_penghargaan', 'prestasi_siswa.id_tingkat_penghargaan', '=', 'tingkat_penghargaan.id')
                ->groupBy('tingkat_penghargaan.id', 'tingkat_penghargaan.tingkat')
                ->get();

            // Prestasi per Bulan (6 bulan terakhir)
            $prestasiPerBulan = PrestasiSiswa::select(
                    DB::raw('DATE_FORMAT(tanggal_prestasi, "%Y-%m") as bulan'),
                    DB::raw('count(*) as total')
                )
                ->where('tanggal_prestasi', '>=', now()->subMonths(6))
                ->groupBy('bulan')
                ->orderBy('bulan')
                ->get();

            // Aktivitas Terbaru
            $aktivitasTerbaru = ActivityLog::with('user')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // Prestasi Terbaru
            $prestasiTerbaru = PrestasiSiswa::with(['siswa', 'kategoriPrestasi', 'tingkatPenghargaan'])
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

            // Top 5 Ekstrakurikuler dengan Anggota Terbanyak
            $topEkskul = Ekstrakurikuler::select('ekstrakurikuler.nama', DB::raw('count(siswa_ekskul.id_siswa) as total_anggota'))
                ->leftJoin('siswa_ekskul', 'ekstrakurikuler.id', '=', 'siswa_ekskul.id_ekskul')
                ->groupBy('ekstrakurikuler.id', 'ekstrakurikuler.nama')
                ->orderBy('total_anggota', 'desc')
                ->limit(5)
                ->get();

            return view('admin.dashboard', compact(
                'totalSiswa',
                'totalGuru',
                'totalWaliKelas',
                'totalKepalaSekolah',
                'totalAdmin',
                'totalPrestasi',
                'prestasiTervalidasi',
                'prestasiPending',
                'prestasiDitolak',
                'totalKelas',
                'rataRataSiswaPerKelas',
                'totalEkskul',
                'totalAnggotaEkskul',
                'prestasiPerKategori',
                'prestasiPerTingkat',
                'prestasiPerBulan',
                'aktivitasTerbaru',
                'prestasiTerbaru',
                'topKelasPrestasi',
                'topEkskul'
            ));

        } catch (\Exception $e) {
            Log::error('Dashboard Admin Error: ' . $e->getMessage());
            return view('admin.dashboard', [
                'totalSiswa' => 0,
                'totalGuru' => 0,
                'totalWaliKelas' => 0,
                'totalKepalaSekolah' => 0,
                'totalAdmin' => 0,
                'totalPrestasi' => 0,
                'prestasiTervalidasi' => 0,
                'prestasiPending' => 0,
                'prestasiDitolak' => 0,
                'totalKelas' => 0,
                'rataRataSiswaPerKelas' => 0,
                'totalEkskul' => 0,
                'totalAnggotaEkskul' => 0,
                'prestasiPerKategori' => collect(),
                'prestasiPerTingkat' => collect(),
                'prestasiPerBulan' => collect(),
                'aktivitasTerbaru' => collect(),
                'prestasiTerbaru' => collect(),
                'topKelasPrestasi' => collect(),
                'topEkskul' => collect(),
            ]);
        }
    }
}
