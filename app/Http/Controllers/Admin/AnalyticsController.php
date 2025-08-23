<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PrestasiSiswa;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\KategoriPrestasi;
use App\Models\Kelas;
use App\Models\Ekstrakurikuler;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        return view('admin.analytics.index');
    }

    public function multiYearComparison()
    {
        try {
            // Get all academic years
            $tahunAjarans = TahunAjaran::orderBy('nama_tahun_ajaran')->get();
            
            // Multi-year achievement comparison
            $multiYearData = [];
            foreach ($tahunAjarans as $tahun) {
                $totalPrestasi = PrestasiSiswa::where('id_tahun_ajaran', $tahun->id)->count();
                $prestasiAkademik = PrestasiSiswa::whereHas('kategoriPrestasi', function($q) {
                    $q->where('jenis_prestasi', 'akademik');
                })->where('id_tahun_ajaran', $tahun->id)->count();
                
                $prestasiNonAkademik = PrestasiSiswa::whereHas('kategoriPrestasi', function($q) {
                    $q->where('jenis_prestasi', 'non_akademik');
                })->where('id_tahun_ajaran', $tahun->id)->count();

                $multiYearData[] = [
                    'tahun' => $tahun->nama_tahun_ajaran,
                    'total' => $totalPrestasi,
                    'akademik' => $prestasiAkademik,
                    'non_akademik' => $prestasiNonAkademik
                ];
            }

            // Achievement by competition level per year
            $competitionLevelData = [];
            foreach ($tahunAjarans as $tahun) {
                $levels = PrestasiSiswa::select('kategori_prestasi.tingkat_kompetisi', DB::raw('count(*) as total'))
                    ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
                    ->where('prestasi_siswa.id_tahun_ajaran', $tahun->id)
                    ->where('prestasi_siswa.status', 'diterima')
                    ->groupBy('kategori_prestasi.tingkat_kompetisi')
                    ->pluck('total', 'tingkat_kompetisi')
                    ->toArray();
                
                $competitionLevelData[$tahun->nama_tahun_ajaran] = $levels;
            }

            return response()->json([
                'multiYearData' => $multiYearData,
                'competitionLevelData' => $competitionLevelData
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function individualStudentAnalysis($siswaId)
    {
        try {
            $siswa = Siswa::with(['kelas', 'prestasi.kategoriPrestasi', 'prestasi.tingkatPenghargaan', 'prestasi.tahunAjaran'])
                ->findOrFail($siswaId);

            // Achievement timeline
            $achievementTimeline = $siswa->prestasi()
                ->with(['kategoriPrestasi', 'tingkatPenghargaan', 'tahunAjaran'])
                ->where('status', 'diterima')
                ->orderBy('tanggal_prestasi')
                ->get()
                ->map(function($prestasi) {
                    return [
                        'date' => $prestasi->tanggal_prestasi,
                        'title' => $prestasi->nama_prestasi,
                        'category' => $prestasi->kategoriPrestasi->nama_kategori,
                        'level' => $prestasi->tingkatPenghargaan->tingkat,
                        'academic_year' => $prestasi->tahunAjaran ? $prestasi->tahunAjaran->nama_tahun_ajaran : null,
                        'competition_level' => $prestasi->kategoriPrestasi->tingkat_kompetisi
                    ];
                });

            // Achievement by category
            $achievementsByCategory = $siswa->prestasi()
                ->select('kategori_prestasi.nama_kategori', 'kategori_prestasi.jenis_prestasi', DB::raw('count(*) as total'))
                ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
                ->where('prestasi_siswa.status', 'diterima')
                ->groupBy('kategori_prestasi.id', 'kategori_prestasi.nama_kategori', 'kategori_prestasi.jenis_prestasi')
                ->get();

            // Academic performance (if available)
            $academicPerformance = $siswa->prestasi()
                ->where('rata_rata_nilai', '>', 0)
                ->where('status', 'diterima')
                ->orderBy('tanggal_prestasi')
                ->get(['tanggal_prestasi', 'rata_rata_nilai', 'nama_prestasi']);

            // Competition level distribution
            $competitionLevelDistribution = $siswa->prestasi()
                ->select('kategori_prestasi.tingkat_kompetisi', DB::raw('count(*) as total'))
                ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
                ->where('prestasi_siswa.status', 'diterima')
                ->groupBy('kategori_prestasi.tingkat_kompetisi')
                ->get();

            // Class ranking comparison
            $classmates = Siswa::where('id_kelas', $siswa->id_kelas)
                ->with('prestasi')
                ->get()
                ->map(function($student) {
                    return [
                        'nama' => $student->nama,
                        'total_prestasi' => $student->prestasi->where('status', 'diterima')->count(),
                        'is_current' => $student->id === request()->route('siswaId')
                    ];
                })
                ->sortByDesc('total_prestasi')
                ->values();

            return response()->json([
                'student_info' => [
                    'nama' => $siswa->nama,
                    'nisn' => $siswa->nisn,
                    'kelas' => $siswa->kelas->nama_kelas ?? null,
                    'total_prestasi' => $siswa->prestasi->where('status', 'diterima')->count()
                ],
                'achievement_timeline' => $achievementTimeline,
                'achievements_by_category' => $achievementsByCategory,
                'academic_performance' => $academicPerformance,
                'competition_level_distribution' => $competitionLevelDistribution,
                'class_ranking' => $classmates
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function schoolPerformanceAnalysis()
    {
        try {
            $currentYear = TahunAjaran::getActiveTahunAjaran();
            
            // Overall school statistics
            $totalStudents = Siswa::count();
            $totalAchievements = PrestasiSiswa::where('status', 'diterima')->count();
            $currentYearAchievements = PrestasiSiswa::where('status', 'diterima')
                ->when($currentYear, function($q) use ($currentYear) {
                    return $q->where('id_tahun_ajaran', $currentYear->id);
                })->count();

            // Top performing classes
            $topClasses = Kelas::select(
                    'kelas.nama_kelas',
                    DB::raw('COUNT(prestasi_siswa.id) as total_prestasi'),
                    DB::raw('COUNT(DISTINCT siswa.id) as total_siswa'),
                    DB::raw('ROUND(COUNT(prestasi_siswa.id) / COUNT(DISTINCT siswa.id), 2) as avg_prestasi_per_siswa')
                )
                ->leftJoin('siswa', 'kelas.id', '=', 'siswa.id_kelas')
                ->leftJoin('prestasi_siswa', function($join) {
                    $join->on('siswa.id', '=', 'prestasi_siswa.id_siswa')
                         ->where('prestasi_siswa.status', '=', 'diterima');
                })
                ->groupBy('kelas.id', 'kelas.nama_kelas')
                ->orderBy('total_prestasi', 'desc')
                ->limit(10)
                ->get();

            // Category performance analysis
            $categoryPerformance = KategoriPrestasi::select(
                    'kategori_prestasi.nama_kategori',
                    'kategori_prestasi.jenis_prestasi',
                    'kategori_prestasi.bidang_prestasi',
                    DB::raw('COUNT(prestasi_siswa.id) as total_prestasi')
                )
                ->leftJoin('prestasi_siswa', function($join) {
                    $join->on('kategori_prestasi.id', '=', 'prestasi_siswa.id_kategori_prestasi')
                         ->where('prestasi_siswa.status', '=', 'diterima');
                })
                ->where('kategori_prestasi.is_active', true)
                ->groupBy('kategori_prestasi.id', 'kategori_prestasi.nama_kategori', 'kategori_prestasi.jenis_prestasi', 'kategori_prestasi.bidang_prestasi')
                ->orderBy('total_prestasi', 'desc')
                ->get();

            // Monthly achievement trends (current academic year)
            $monthlyTrends = [];
            if ($currentYear) {
                $monthlyTrends = PrestasiSiswa::select(
                        DB::raw('DATE_FORMAT(tanggal_prestasi, "%Y-%m") as bulan'),
                        DB::raw('COUNT(*) as total')
                    )
                    ->where('id_tahun_ajaran', $currentYear->id)
                    ->where('status', 'diterima')
                    ->where('tanggal_prestasi', '>=', $currentYear->tanggal_mulai)
                    ->where('tanggal_prestasi', '<=', $currentYear->tanggal_selesai)
                    ->groupBy('bulan')
                    ->orderBy('bulan')
                    ->get();
            }

            // Competition level achievements
            $competitionLevelAchievements = PrestasiSiswa::select(
                    'kategori_prestasi.tingkat_kompetisi',
                    DB::raw('COUNT(*) as total')
                )
                ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
                ->where('prestasi_siswa.status', 'diterima')
                ->when($currentYear, function($q) use ($currentYear) {
                    return $q->where('prestasi_siswa.id_tahun_ajaran', $currentYear->id);
                })
                ->groupBy('kategori_prestasi.tingkat_kompetisi')
                ->orderBy('total', 'desc')
                ->get();

            return response()->json([
                'overview' => [
                    'total_students' => $totalStudents,
                    'total_achievements' => $totalAchievements,
                    'current_year_achievements' => $currentYearAchievements,
                    'current_academic_year' => $currentYear ? $currentYear->format_tahun : null
                ],
                'top_classes' => $topClasses,
                'category_performance' => $categoryPerformance,
                'monthly_trends' => $monthlyTrends,
                'competition_level_achievements' => $competitionLevelAchievements
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function extracurricularAnalysis()
    {
        try {
            $currentYear = TahunAjaran::getActiveTahunAjaran();
            $currentAcademicYear = $currentYear ? $currentYear->nama_tahun_ajaran : null;

            // Extracurricular participation analysis
            $ekstrakurrikulerStats = Ekstrakurikuler::select(
                    'ekstrakurikuler.nama',
                    'ekstrakurikuler.pembina',
                    DB::raw('COUNT(DISTINCT siswa_ekskul.id_siswa) as total_anggota'),
                    DB::raw('COUNT(DISTINCT CASE WHEN siswa_ekskul.status_keaktifan = "aktif" THEN siswa_ekskul.id_siswa END) as anggota_aktif'),
                    DB::raw('COUNT(DISTINCT prestasi_siswa.id) as total_prestasi')
                )
                ->leftJoin('siswa_ekskul', function($join) use ($currentAcademicYear) {
                    $join->on('ekstrakurikuler.id', '=', 'siswa_ekskul.id_ekskul');
                    if ($currentAcademicYear) {
                        $join->where('siswa_ekskul.tahun_ajaran', '=', $currentAcademicYear);
                    }
                })
                ->leftJoin('prestasi_siswa', function($join) {
                    $join->on('ekstrakurikuler.id', '=', 'prestasi_siswa.id_ekskul')
                         ->where('prestasi_siswa.status', '=', 'diterima');
                })
                ->groupBy('ekstrakurikuler.id', 'ekstrakurikuler.nama', 'ekstrakurikuler.pembina')
                ->orderBy('total_prestasi', 'desc')
                ->get();

            // Period-based participation tracking
            $participationByPeriod = DB::table('siswa_ekskul')
                ->select('tahun_ajaran', 'semester', DB::raw('COUNT(DISTINCT id_siswa) as total_participants'))
                ->whereNotNull('tahun_ajaran')
                ->groupBy('tahun_ajaran', 'semester')
                ->orderBy('tahun_ajaran', 'desc')
                ->orderBy('semester')
                ->get();

            // Extracurricular achievements by category
            $ekstrakurrikulerAchievements = PrestasiSiswa::select(
                    'ekstrakurikuler.nama as ekskul_nama',
                    'kategori_prestasi.nama_kategori',
                    'kategori_prestasi.tingkat_kompetisi',
                    DB::raw('COUNT(*) as total')
                )
                ->join('ekstrakurikuler', 'prestasi_siswa.id_ekskul', '=', 'ekstrakurikuler.id')
                ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
                ->where('prestasi_siswa.status', 'diterima')
                ->groupBy('ekstrakurikuler.id', 'ekstrakurikuler.nama', 'kategori_prestasi.id', 'kategori_prestasi.nama_kategori', 'kategori_prestasi.tingkat_kompetisi')
                ->orderBy('total', 'desc')
                ->get();

            return response()->json([
                'ekstrakurikuler_stats' => $ekstrakurrikulerStats,
                'participation_by_period' => $participationByPeriod,
                'ekstrakurikuler_achievements' => $ekstrakurrikulerAchievements,
                'current_academic_year' => $currentAcademicYear
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getStudentsList()
    {
        try {
            $students = Siswa::select('id', 'nama', 'nisn')
                ->with(['kelas:id,nama_kelas'])
                ->withCount(['prestasi as total_prestasi' => function($query) {
                    $query->where('status', 'diterima');
                }])
                ->orderBy('nama')
                ->get()
                ->map(function($student) {
                    return [
                        'id' => $student->id,
                        'nama' => $student->nama,
                        'nisn' => $student->nisn,
                        'kelas' => $student->kelas->nama_kelas ?? 'Belum ada kelas',
                        'total_prestasi' => $student->total_prestasi
                    ];
                });

            return response()->json(['students' => $students]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
