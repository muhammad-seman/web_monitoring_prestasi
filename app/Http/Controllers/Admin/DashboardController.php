<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Siswa;
use App\Models\PrestasiSiswa;
use App\Models\Kelas;
use App\Models\Ekstrakurikuler;
use App\Models\ActivityLog;
use App\Models\TahunAjaran;
use App\Models\KategoriPrestasi;
use App\Models\KenaikanKelas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Get current academic year
            $currentTahunAjaran = TahunAjaran::getActiveTahunAjaran();
            
            // Statistik Pengguna
            $totalSiswa = Siswa::count();
            $totalGuru = User::where('role', 'guru')->count();
            $totalWaliKelas = User::where('role', 'wali_kelas')->count();
            $totalKepalaSekolah = User::where('role', 'kepala_sekolah')->count();
            $totalAdmin = User::where('role', 'admin')->count();

            // Enhanced Statistik Prestasi with current academic year filter
            $prestasiQuery = PrestasiSiswa::query();
            if ($currentTahunAjaran) {
                $prestasiQuery->where('id_tahun_ajaran', $currentTahunAjaran->id);
            }

            $totalPrestasi = PrestasiSiswa::count(); // All time
            $totalPrestasiCurrentYear = $prestasiQuery->count(); // Current year
            $prestasiTervalidasi = (clone $prestasiQuery)->where('status', 'diterima')->count();
            $prestasiPending = (clone $prestasiQuery)->where('status', 'menunggu_validasi')->count();
            $prestasiDitolak = (clone $prestasiQuery)->where('status', 'ditolak')->count();

            // Enhanced Prestasi by Type (Academic/Non-Academic)
            $prestasiAkademik = PrestasiSiswa::whereHas('kategoriPrestasi', function($q) {
                $q->where('jenis_prestasi', 'akademik');
            });
            if ($currentTahunAjaran) {
                $prestasiAkademik->where('id_tahun_ajaran', $currentTahunAjaran->id);
            }
            $prestasiAkademikCount = $prestasiAkademik->where('status', 'diterima')->count();

            $prestasiNonAkademik = PrestasiSiswa::whereHas('kategoriPrestasi', function($q) {
                $q->where('jenis_prestasi', 'non_akademik');
            });
            if ($currentTahunAjaran) {
                $prestasiNonAkademik->where('id_tahun_ajaran', $currentTahunAjaran->id);
            }
            $prestasiNonAkademikCount = $prestasiNonAkademik->where('status', 'diterima')->count();

            // Statistik Kelas with Class Progression Info
            $totalKelas = Kelas::count();
            $rataRataSiswaPerKelas = $totalKelas > 0 ? round($totalSiswa / $totalKelas, 1) : 0;
            
            // Class Progression Statistics
            $kelasXICount = Siswa::whereHas('kelas', function($q) {
                $q->where('nama_kelas', 'like', '%XI%');
            })->count();
            
            $kelasXIICount = Siswa::whereHas('kelas', function($q) {
                $q->where('nama_kelas', 'like', '%XII%');
            })->count();

            $kenaikanKelasStats = [];
            if ($currentTahunAjaran) {
                $kenaikanKelasStats = [
                    'pending' => KenaikanKelas::where('tahun_ajaran_id', $currentTahunAjaran->id)->where('status', 'pending')->count(),
                    'naik' => KenaikanKelas::where('tahun_ajaran_id', $currentTahunAjaran->id)->where('status', 'naik')->count(),
                    'tidak_naik' => KenaikanKelas::where('tahun_ajaran_id', $currentTahunAjaran->id)->where('status', 'tidak_naik')->count(),
                ];
            }

            // Enhanced Ekstrakurikuler Statistics with Period Tracking
            $totalEkskul = Ekstrakurikuler::count();
            $totalAnggotaEkskulQuery = DB::table('siswa_ekskul');
            if ($currentTahunAjaran) {
                $totalAnggotaEkskulQuery->where('tahun_ajaran', $currentTahunAjaran->nama_tahun_ajaran);
            }
            $totalAnggotaEkskul = $totalAnggotaEkskulQuery->where('status_keaktifan', 'aktif')->count();

            // Enhanced Prestasi per Kategori with Competition Level
            $prestasiPerKategori = PrestasiSiswa::select(
                    'kategori_prestasi.nama_kategori as kategori', 
                    'kategori_prestasi.jenis_prestasi',
                    'kategori_prestasi.tingkat_kompetisi',
                    DB::raw('count(*) as total')
                )
                ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
                ->where('prestasi_siswa.status', 'diterima')
                ->when($currentTahunAjaran, function($q) use ($currentTahunAjaran) {
                    return $q->where('prestasi_siswa.id_tahun_ajaran', $currentTahunAjaran->id);
                })
                ->groupBy('kategori_prestasi.id', 'kategori_prestasi.nama_kategori', 'kategori_prestasi.jenis_prestasi', 'kategori_prestasi.tingkat_kompetisi')
                ->orderBy('total', 'desc')
                ->get();

            // Competition Level Distribution
            $prestasiPerTingkatKompetisi = PrestasiSiswa::select(
                    'kategori_prestasi.tingkat_kompetisi as tingkat', 
                    DB::raw('count(*) as total')
                )
                ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
                ->where('prestasi_siswa.status', 'diterima')
                ->when($currentTahunAjaran, function($q) use ($currentTahunAjaran) {
                    return $q->where('prestasi_siswa.id_tahun_ajaran', $currentTahunAjaran->id);
                })
                ->groupBy('kategori_prestasi.tingkat_kompetisi')
                ->orderByRaw("FIELD(kategori_prestasi.tingkat_kompetisi, 'internasional', 'nasional', 'provinsi', 'kabupaten', 'sekolah')")
                ->get();

            // Traditional Prestasi per Tingkat (Award Level) - keeping for compatibility
            $prestasiPerTingkat = PrestasiSiswa::select('tingkat_penghargaan.tingkat as tingkat', DB::raw('count(*) as total'))
                ->join('tingkat_penghargaan', 'prestasi_siswa.id_tingkat_penghargaan', '=', 'tingkat_penghargaan.id')
                ->where('prestasi_siswa.status', 'diterima')
                ->when($currentTahunAjaran, function($q) use ($currentTahunAjaran) {
                    return $q->where('prestasi_siswa.id_tahun_ajaran', $currentTahunAjaran->id);
                })
                ->groupBy('tingkat_penghargaan.id', 'tingkat_penghargaan.tingkat')
                ->get();

            // Enhanced Prestasi per Bulan with Current Academic Year focus
            $prestasiPerBulan = PrestasiSiswa::select(
                    DB::raw('DATE_FORMAT(tanggal_prestasi, "%Y-%m") as bulan'),
                    DB::raw('count(*) as total')
                )
                ->where('prestasi_siswa.status', 'diterima')
                ->when($currentTahunAjaran, function($q) use ($currentTahunAjaran) {
                    return $q->where('prestasi_siswa.id_tahun_ajaran', $currentTahunAjaran->id)
                             ->where('tanggal_prestasi', '>=', $currentTahunAjaran->tanggal_mulai)
                             ->where('tanggal_prestasi', '<=', $currentTahunAjaran->tanggal_selesai);
                }, function($q) {
                    return $q->where('tanggal_prestasi', '>=', now()->subMonths(6));
                })
                ->groupBy('bulan')
                ->orderBy('bulan')
                ->get();

            // Multi-year comparison data (last 3 years)
            $multiYearComparison = TahunAjaran::select('nama_tahun_ajaran')
                ->withCount(['prestasi as total_prestasi' => function($query) {
                    $query->where('status', 'diterima');
                }])
                ->orderBy('nama_tahun_ajaran', 'desc')
                ->limit(3)
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
                // User Statistics
                'totalSiswa',
                'totalGuru',
                'totalWaliKelas',
                'totalKepalaSekolah',
                'totalAdmin',
                
                // Enhanced Achievement Statistics
                'totalPrestasi',
                'totalPrestasiCurrentYear',
                'prestasiTervalidasi',
                'prestasiPending',
                'prestasiDitolak',
                'prestasiAkademikCount',
                'prestasiNonAkademikCount',
                
                // Class and Progression Statistics
                'totalKelas',
                'rataRataSiswaPerKelas',
                'kelasXICount',
                'kelasXIICount',
                'kenaikanKelasStats',
                
                // Extracurricular Statistics
                'totalEkskul',
                'totalAnggotaEkskul',
                
                // Enhanced Analytics Data
                'prestasiPerKategori',
                'prestasiPerTingkat',
                'prestasiPerTingkatKompetisi',
                'prestasiPerBulan',
                'multiYearComparison',
                
                // Activity and Recent Data
                'aktivitasTerbaru',
                'prestasiTerbaru',
                'topKelasPrestasi',
                'topEkskul',
                
                // Academic Year Context
                'currentTahunAjaran'
            ));

        } catch (\Exception $e) {
            Log::error('Dashboard Admin Error: ' . $e->getMessage());
            return view('admin.dashboard', [
                // User Statistics
                'totalSiswa' => 0,
                'totalGuru' => 0,
                'totalWaliKelas' => 0,
                'totalKepalaSekolah' => 0,
                'totalAdmin' => 0,
                
                // Enhanced Achievement Statistics
                'totalPrestasi' => 0,
                'totalPrestasiCurrentYear' => 0,
                'prestasiTervalidasi' => 0,
                'prestasiPending' => 0,
                'prestasiDitolak' => 0,
                'prestasiAkademikCount' => 0,
                'prestasiNonAkademikCount' => 0,
                
                // Class and Progression Statistics
                'totalKelas' => 0,
                'rataRataSiswaPerKelas' => 0,
                'kelasXICount' => 0,
                'kelasXIICount' => 0,
                'kenaikanKelasStats' => ['pending' => 0, 'naik' => 0, 'tidak_naik' => 0],
                
                // Extracurricular Statistics
                'totalEkskul' => 0,
                'totalAnggotaEkskul' => 0,
                
                // Enhanced Analytics Data
                'prestasiPerKategori' => collect(),
                'prestasiPerTingkat' => collect(),
                'prestasiPerTingkatKompetisi' => collect(),
                'prestasiPerBulan' => collect(),
                'multiYearComparison' => collect(),
                
                // Activity and Recent Data
                'aktivitasTerbaru' => collect(),
                'prestasiTerbaru' => collect(),
                'topKelasPrestasi' => collect(),
                'topEkskul' => collect(),
                
                // Academic Year Context
                'currentTahunAjaran' => null
            ]);
        }
    }
}
