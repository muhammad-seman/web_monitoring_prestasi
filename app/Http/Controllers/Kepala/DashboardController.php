<?php

namespace App\Http\Controllers\Kepala;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\PrestasiSiswa;
use App\Models\Kelas;
use App\Models\Ekstrakurikuler;
use App\Models\User;
use App\Models\TahunAjaran;
use App\Models\KategoriPrestasi;
use App\Models\SiswaEkskul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get academic years for filtering
        $academicYears = TahunAjaran::orderBy('nama_tahun_ajaran', 'desc')->get();
        $activeYear = $academicYears->where('is_active', true)->first();
        $selectedYear = $request->get('academic_year');
        $currentYear = $selectedYear ? TahunAjaran::find($selectedYear) : $activeYear;
        
        // Executive KPIs - Strategic Performance Indicators
        $executiveKPIs = $this->getExecutiveKPIs($currentYear);
        
        // Multi-year Comparative Analysis
        $multiYearAnalysis = $this->getMultiYearComparison();
        
        // School-wide Performance Metrics
        $schoolPerformance = $this->getSchoolWidePerformance($currentYear);
        
        // Department/Grade Level Analysis
        $departmentAnalysis = $this->getDepartmentAnalysis($currentYear);
        
        // Teacher Performance Overview
        $teacherPerformance = $this->getTeacherPerformanceOverview($currentYear);
        
        // Strategic Planning Insights
        $strategicInsights = $this->getStrategicPlanningInsights($currentYear);
        
        // Resource Utilization Metrics
        $resourceMetrics = $this->getResourceUtilizationMetrics($currentYear);
        
        // Trend Analysis for Executive Decision Making
        $trendAnalysis = $this->getExecutiveTrendAnalysis($currentYear);
        
        // Extract variables needed by the view
        $totalPrestasi = $executiveKPIs['total_achievements'] ?? 0;
        $totalSiswa = Siswa::count();
        $totalKelas = Kelas::count();
        $totalEkstrakurikuler = Ekstrakurikuler::count();
        
        // Prestasi status counts
        $prestasiQuery = PrestasiSiswa::query();
        if ($currentYear) {
            $prestasiQuery->where('id_tahun_ajaran', $currentYear->id);
        }
        
        $prestasiPending = (clone $prestasiQuery)->where('status', 'menunggu_validasi')->count();
        $prestasiApproved = (clone $prestasiQuery)->where('status', 'diterima')->count();
        $prestasiRejected = (clone $prestasiQuery)->where('status', 'ditolak')->count();
        
        // Prestasi by tingkat
        $prestasiTingkat = PrestasiSiswa::select('tingkat_penghargaan.tingkat', DB::raw('count(*) as total'))
            ->join('tingkat_penghargaan', 'prestasi_siswa.id_tingkat_penghargaan', '=', 'tingkat_penghargaan.id')
            ->where('prestasi_siswa.status', 'diterima');
        if ($currentYear) {
            $prestasiTingkat->where('prestasi_siswa.id_tahun_ajaran', $currentYear->id);
        }
        $prestasiTingkat = $prestasiTingkat->groupBy('tingkat_penghargaan.id', 'tingkat_penghargaan.tingkat')
            ->orderBy('total', 'desc')
            ->get();
            
        // Prestasi by kategori
        $prestasiKategori = PrestasiSiswa::select('kategori_prestasi.nama_kategori', DB::raw('count(*) as total'))
            ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
            ->where('prestasi_siswa.status', 'diterima');
        if ($currentYear) {
            $prestasiKategori->where('prestasi_siswa.id_tahun_ajaran', $currentYear->id);
        }
        $prestasiKategori = $prestasiKategori->groupBy('kategori_prestasi.id', 'kategori_prestasi.nama_kategori')
            ->orderBy('total', 'desc')
            ->get();
            
        // Prestasi per bulan (6 bulan terakhir) - FIXING LINE 168 ERROR
        $prestasiPerBulan = PrestasiSiswa::select(
                DB::raw('DATE_FORMAT(tanggal_prestasi, "%Y-%m") as bulan'),
                DB::raw('count(*) as total')
            )
            ->where('prestasi_siswa.status', 'diterima')
            ->where('tanggal_prestasi', '>=', now()->subMonths(6))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();
            
        // Top 5 Kelas dengan Prestasi Terbanyak
        $topKelasPrestasi = Kelas::select('kelas.nama_kelas', DB::raw('count(prestasi_siswa.id) as total_prestasi'))
            ->leftJoin('siswa', 'kelas.id', '=', 'siswa.id_kelas')
            ->leftJoin('prestasi_siswa', function($join) use ($currentYear) {
                $join->on('siswa.id', '=', 'prestasi_siswa.id_siswa')
                     ->where('prestasi_siswa.status', '=', 'diterima');
                if ($currentYear) {
                    $join->where('prestasi_siswa.id_tahun_ajaran', '=', $currentYear->id);
                }
            })
            ->groupBy('kelas.id', 'kelas.nama_kelas')
            ->orderBy('total_prestasi', 'desc')
            ->limit(5)
            ->get();
            
        // Top 5 Ekstrakurikuler dengan Anggota Terbanyak
        $topEkskul = Ekstrakurikuler::select('ekstrakurikuler.nama', DB::raw('count(siswa_ekskul.id_siswa) as total_anggota'))
            ->leftJoin('siswa_ekskul', 'ekstrakurikuler.id', '=', 'siswa_ekskul.id_ekskul')
            ->where('siswa_ekskul.status_keaktifan', 'aktif')
            ->groupBy('ekstrakurikuler.id', 'ekstrakurikuler.nama')
            ->orderBy('total_anggota', 'desc')
            ->limit(5)
            ->get();
            
        // Aktivitas Terbaru (10 aktivitas terakhir)
        $aktivitasTerbaru = \App\Models\ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        // Prestasi Terbaru (5 prestasi terakhir yang diterima)
        $prestasiTerbaru = PrestasiSiswa::with(['siswa', 'kategoriPrestasi', 'tingkatPenghargaan'])
            ->where('status', 'diterima')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('kepala.dashboard', compact(
            'academicYears',
            'currentYear', 
            'executiveKPIs',
            'multiYearAnalysis',
            'schoolPerformance',
            'departmentAnalysis',
            'teacherPerformance',
            'strategicInsights',
            'resourceMetrics',
            'trendAnalysis',
            'totalPrestasi',
            'totalSiswa',
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
    
    /**
     * Get Executive KPIs - Key strategic performance indicators
     */
    private function getExecutiveKPIs($currentYear = null)
    {
        // Base query for current year filtering
        $achievementQuery = PrestasiSiswa::query();
        if ($currentYear) {
            $achievementQuery->where('id_tahun_ajaran', $currentYear->id);
        }
        
        // Total achievements and growth metrics
        $totalAchievements = $achievementQuery->where('status', 'diterima')->count();
        $totalSubmissions = $achievementQuery->count();
        $approvalRate = $totalSubmissions > 0 ? round(($totalAchievements / $totalSubmissions) * 100, 1) : 0;
        
        // Student participation metrics
        $totalStudents = Siswa::count();
        $activeStudents = Siswa::whereHas('prestasi', function($query) use ($currentYear) {
            $query->where('status', 'diterima');
            if ($currentYear) {
                $query->where('id_tahun_ajaran', $currentYear->id);
            }
        })->count();
        $participationRate = $totalStudents > 0 ? round(($activeStudents / $totalStudents) * 100, 1) : 0;
        
        // Academic vs Non-Academic balance
        $academicQuery = PrestasiSiswa::query();
        if ($currentYear) {
            $academicQuery->where('id_tahun_ajaran', $currentYear->id);
        }
        $academicAchievements = $academicQuery
            ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
            ->where('kategori_prestasi.jenis_prestasi', 'akademik')
            ->where('prestasi_siswa.status', 'diterima')
            ->count();
        
        $nonAcademicQuery = PrestasiSiswa::query();
        if ($currentYear) {
            $nonAcademicQuery->where('id_tahun_ajaran', $currentYear->id);
        }
        $nonAcademicAchievements = $nonAcademicQuery
            ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
            ->where('kategori_prestasi.jenis_prestasi', 'non_akademik')
            ->where('prestasi_siswa.status', 'diterima')
            ->count();
        
        // Competition level distribution
        $nationalQuery = PrestasiSiswa::query();
        if ($currentYear) {
            $nationalQuery->where('id_tahun_ajaran', $currentYear->id);
        }
        $nationalLevel = $nationalQuery
            ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
            ->where('kategori_prestasi.tingkat_kompetisi', 'nasional')
            ->where('prestasi_siswa.status', 'diterima')
            ->count();
        
        $internationalQuery = PrestasiSiswa::query();
        if ($currentYear) {
            $internationalQuery->where('id_tahun_ajaran', $currentYear->id);
        }
        $internationalLevel = $internationalQuery
            ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
            ->where('kategori_prestasi.tingkat_kompetisi', 'internasional')
            ->where('prestasi_siswa.status', 'diterima')
            ->count();
        
        // Teacher effectiveness - count teachers who have students with prestasi
        $totalTeachers = User::where('role', 'guru')->count();
        
        // Get active teachers based on login activity or simply assume all are active for now
        // In a real system, you might track teacher activity differently
        $activeTeachers = User::where('role', 'guru')
            ->where('status', 'active')
            ->count();
        
        // If activeTeachers is 0, set it to totalTeachers for calculation purposes
        if ($activeTeachers == 0) {
            $activeTeachers = $totalTeachers;
        }
        
        // Extracurricular engagement
        $totalEkskul = Ekstrakurikuler::count();
        $ekskulParticipation = SiswaEkskul::distinct('id_siswa')->count();
        
        return [
            'total_achievements' => $totalAchievements,
            'approval_rate' => $approvalRate,
            'participation_rate' => $participationRate,
            'academic_achievements' => $academicAchievements,
            'non_academic_achievements' => $nonAcademicAchievements,
            'national_level' => $nationalLevel,
            'international_level' => $internationalLevel,
            'teacher_engagement_rate' => $totalTeachers > 0 ? round(($activeTeachers / $totalTeachers) * 100, 1) : 0,
            'extracurricular_participation' => $totalStudents > 0 ? round(($ekskulParticipation / $totalStudents) * 100, 1) : 0,
            'achievement_per_student' => $totalStudents > 0 ? round($totalAchievements / $totalStudents, 2) : 0
        ];
    }
    
    /**
     * Multi-year comparison for strategic planning
     */
    private function getMultiYearComparison()
    {
        $years = TahunAjaran::orderBy('nama_tahun_ajaran')->get();
        $comparison = [];
        
        foreach ($years as $year) {
            $achievements = PrestasiSiswa::where('id_tahun_ajaran', $year->id)
                ->where('status', 'diterima')
                ->count();
            
            $students = Siswa::count(); // Assuming student count is relatively stable
            
            $academicCount = PrestasiSiswa::where('id_tahun_ajaran', $year->id)
                ->where('status', 'diterima')
                ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
                ->where('kategori_prestasi.jenis_prestasi', 'akademik')
                ->count();
            
            $comparison[] = [
                'year' => $year->nama_tahun_ajaran,
                'total_achievements' => $achievements,
                'achievement_per_student' => $students > 0 ? round($achievements / $students, 2) : 0,
                'academic_percentage' => $achievements > 0 ? round(($academicCount / $achievements) * 100, 1) : 0,
                'is_active' => $year->is_active
            ];
        }
        
        // Calculate growth trends
        for ($i = 1; $i < count($comparison); $i++) {
            $current = $comparison[$i]['total_achievements'];
            $previous = $comparison[$i-1]['total_achievements'];
            $comparison[$i]['growth_rate'] = $previous > 0 ? round((($current - $previous) / $previous) * 100, 1) : 0;
        }
        
        return $comparison;
    }
    
    /**
     * School-wide performance metrics
     */
    private function getSchoolWidePerformance($currentYear = null)
    {
        // Grade level performance
        $gradeLevels = ['X', 'XI', 'XII'];
        $gradePerformance = [];
        
        foreach ($gradeLevels as $grade) {
            $kelasIds = Kelas::where('nama_kelas', 'like', $grade . '%')->pluck('id');
            $siswaIds = Siswa::whereIn('id_kelas', $kelasIds)->pluck('id');
            
            $achievements = PrestasiSiswa::whereIn('id_siswa', $siswaIds)
                ->where('status', 'diterima');
            
            if ($currentYear) {
                $achievements->where('id_tahun_ajaran', $currentYear->id);
            }
            
            $gradePerformance[$grade] = [
                'total_students' => $siswaIds->count(),
                'total_achievements' => $achievements->count(),
                'average_per_student' => $siswaIds->count() > 0 ? round($achievements->count() / $siswaIds->count(), 2) : 0
            ];
        }
        
        // Class effectiveness ranking
        $classRankings = Kelas::select('kelas.*', DB::raw('COUNT(prestasi_siswa.id) as total_prestasi'))
            ->leftJoin('siswa', 'kelas.id', '=', 'siswa.id_kelas')
            ->leftJoin('prestasi_siswa', function($join) use ($currentYear) {
                $join->on('siswa.id', '=', 'prestasi_siswa.id_siswa')
                     ->where('prestasi_siswa.status', '=', 'diterima');
                if ($currentYear) {
                    $join->where('prestasi_siswa.id_tahun_ajaran', '=', $currentYear->id);
                }
            })
            ->with('waliKelas')
            ->groupBy('kelas.id')
            ->orderByDesc('total_prestasi')
            ->limit(10)
            ->get();
        
        // Department/Subject area analysis
        $subjectAnalysis = KategoriPrestasi::select('kategori_prestasi.bidang_prestasi', DB::raw('COUNT(prestasi_siswa.id) as total'))
            ->leftJoin('prestasi_siswa', function($join) use ($currentYear) {
                $join->on('kategori_prestasi.id', '=', 'prestasi_siswa.id_kategori_prestasi')
                     ->where('prestasi_siswa.status', '=', 'diterima');
                if ($currentYear) {
                    $join->where('prestasi_siswa.id_tahun_ajaran', '=', $currentYear->id);
                }
            })
            ->whereNotNull('kategori_prestasi.bidang_prestasi')
            ->groupBy('kategori_prestasi.bidang_prestasi')
            ->orderByDesc('total')
            ->get();
        
        return [
            'grade_performance' => $gradePerformance,
            'class_rankings' => $classRankings,
            'subject_analysis' => $subjectAnalysis
        ];
    }
    
    /**
     * Department-level analysis for strategic planning
     */
    private function getDepartmentAnalysis($currentYear = null)
    {
        // Academic departments
        $departments = [
            'MIPA' => ['Matematika', 'IPA', 'Fisika', 'Kimia', 'Biologi'],
            'IPS' => ['IPS', 'Sejarah', 'Geografi', 'Ekonomi', 'Sosiologi'],
            'Bahasa' => ['Bahasa Indonesia', 'Bahasa Inggris', 'Sastra'],
            'Seni' => ['Seni Musik', 'Seni Rupa', 'Seni Tari', 'Teater'],
            'Olahraga' => ['Olahraga', 'Kesehatan']
        ];
        
        $departmentStats = [];
        
        foreach ($departments as $deptName => $subjects) {
            $achievements = PrestasiSiswa::whereHas('kategoriPrestasi', function($query) use ($subjects) {
                $query->whereIn('bidang_prestasi', $subjects);
            })->where('status', 'diterima');
            
            if ($currentYear) {
                $achievements->where('id_tahun_ajaran', $currentYear->id);
            }
            
            $departmentStats[$deptName] = [
                'total_achievements' => $achievements->count(),
                'subjects' => $subjects,
                'percentage' => 0 // Will be calculated after getting totals
            ];
        }
        
        $totalDeptAchievements = collect($departmentStats)->sum('total_achievements');
        
        // Calculate percentages
        foreach ($departmentStats as $dept => &$stats) {
            $stats['percentage'] = $totalDeptAchievements > 0 ? 
                round(($stats['total_achievements'] / $totalDeptAchievements) * 100, 1) : 0;
        }
        
        return $departmentStats;
    }
    
    /**
     * Teacher performance overview for HR insights
     */
    private function getTeacherPerformanceOverview($currentYear = null)
    {
        $teachers = User::where('role', 'guru')
            ->withCount(['createdPrestasi as total_created' => function($query) use ($currentYear) {
                if ($currentYear) {
                    $query->where('id_tahun_ajaran', $currentYear->id);
                }
            }])
            ->withCount(['validatedPrestasi as total_validated' => function($query) use ($currentYear) {
                $query->where('status', 'diterima');
                if ($currentYear) {
                    $query->where('id_tahun_ajaran', $currentYear->id);
                }
            }])
            ->get();
        
        $performanceMetrics = [
            'total_teachers' => $teachers->count(),
            'active_teachers' => $teachers->where('total_created', '>', 0)->count(),
            'high_performers' => $teachers->where('total_validated', '>=', 10)->count(),
            'average_achievements_per_teacher' => $teachers->count() > 0 ? round($teachers->sum('total_validated') / $teachers->count(), 1) : 0,
            'top_performers' => $teachers->sortByDesc('total_validated')->take(5)
        ];
        
        return $performanceMetrics;
    }
    
    /**
     * Strategic planning insights
     */
    private function getStrategicPlanningInsights($currentYear = null)
    {
        // Growth opportunities identification
        $lowPerformingAreas = KategoriPrestasi::select('kategori_prestasi.*', DB::raw('COUNT(prestasi_siswa.id) as achievement_count'))
            ->leftJoin('prestasi_siswa', function($join) use ($currentYear) {
                $join->on('kategori_prestasi.id', '=', 'prestasi_siswa.id_kategori_prestasi')
                     ->where('prestasi_siswa.status', '=', 'diterima');
                if ($currentYear) {
                    $join->where('prestasi_siswa.id_tahun_ajaran', '=', $currentYear->id);
                }
            })
            ->groupBy('kategori_prestasi.id')
            ->havingRaw('COUNT(prestasi_siswa.id) < 5')
            ->orderBy('achievement_count')
            ->limit(5)
            ->get();
        
        // Resource allocation recommendations
        $underutilizedEkskul = Ekstrakurikuler::select('ekstrakurikuler.*', 
                DB::raw('COUNT(siswa_ekskul.id_siswa) as member_count'),
                DB::raw('COUNT(prestasi_siswa.id) as achievement_count'))
            ->leftJoin('siswa_ekskul', 'ekstrakurikuler.id', '=', 'siswa_ekskul.id_ekskul')
            ->leftJoin('prestasi_siswa', function($join) use ($currentYear) {
                $join->on('ekstrakurikuler.id', '=', 'prestasi_siswa.id_ekskul')
                     ->where('prestasi_siswa.status', '=', 'diterima');
                if ($currentYear) {
                    $join->where('prestasi_siswa.id_tahun_ajaran', '=', $currentYear->id);
                }
            })
            ->groupBy('ekstrakurikuler.id')
            ->havingRaw('COUNT(siswa_ekskul.id_siswa) > 10 AND COUNT(prestasi_siswa.id) < 3')
            ->get();
        
        // Success patterns
        $successPatterns = $this->identifySuccessPatterns($currentYear);
        
        return [
            'growth_opportunities' => $lowPerformingAreas,
            'underutilized_resources' => $underutilizedEkskul,
            'success_patterns' => $successPatterns,
            'recommendations' => $this->generateStrategicRecommendations($currentYear)
        ];
    }
    
    /**
     * Resource utilization metrics
     */
    private function getResourceUtilizationMetrics($currentYear = null)
    {
        // Extracurricular utilization
        $ekskulUtilization = Ekstrakurikuler::select('ekstrakurikuler.nama',
                DB::raw('COUNT(DISTINCT siswa_ekskul.id_siswa) as members'),
                DB::raw('COUNT(prestasi_siswa.id) as achievements'))
            ->leftJoin('siswa_ekskul', 'ekstrakurikuler.id', '=', 'siswa_ekskul.id_ekskul')
            ->leftJoin('prestasi_siswa', function($join) use ($currentYear) {
                $join->on('ekstrakurikuler.id', '=', 'prestasi_siswa.id_ekskul')
                     ->where('prestasi_siswa.status', '=', 'diterima');
                if ($currentYear) {
                    $join->where('prestasi_siswa.id_tahun_ajaran', '=', $currentYear->id);
                }
            })
            ->groupBy('ekstrakurikuler.id', 'ekstrakurikuler.nama')
            ->orderByDesc('achievements')
            ->get();
        
        // Teacher workload distribution
        $teacherWorkload = User::where('role', 'guru')
            ->select('users.nama', 
                DB::raw('COUNT(DISTINCT kelas.id) as classes_managed'),
                DB::raw('COUNT(prestasi_siswa.id) as achievements_processed'))
            ->leftJoin('kelas', 'users.id', '=', 'kelas.id_wali_kelas')
            ->leftJoin('siswa', 'kelas.id', '=', 'siswa.id_kelas')
            ->leftJoin('prestasi_siswa', function($join) use ($currentYear) {
                $join->on('siswa.id', '=', 'prestasi_siswa.id_siswa')
                     ->where('prestasi_siswa.created_by', '=', DB::raw('users.id'));
                if ($currentYear) {
                    $join->where('prestasi_siswa.id_tahun_ajaran', '=', $currentYear->id);
                }
            })
            ->groupBy('users.id', 'users.nama')
            ->orderByDesc('achievements_processed')
            ->get();
        
        return [
            'extracurricular_utilization' => $ekskulUtilization,
            'teacher_workload' => $teacherWorkload,
            'resource_efficiency_score' => $this->calculateResourceEfficiency($currentYear)
        ];
    }
    
    /**
     * Executive trend analysis for strategic decision making
     */
    private function getExecutiveTrendAnalysis($currentYear = null)
    {
        // Monthly achievement trends (last 12 months)
        $monthlyTrends = PrestasiSiswa::select(
                DB::raw('DATE_FORMAT(tanggal_prestasi, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total_achievements'),
                DB::raw('SUM(CASE WHEN status = "diterima" THEN 1 ELSE 0 END) as approved'),
                DB::raw('AVG(CASE WHEN status = "diterima" THEN 1 ELSE 0 END) * 100 as approval_rate'))
            ->where('tanggal_prestasi', '>=', now()->subMonths(12));
        
        if ($currentYear) {
            $monthlyTrends->where('id_tahun_ajaran', $currentYear->id);
        }
        
        $monthlyTrends = $monthlyTrends->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Seasonal patterns
        $seasonalAnalysis = $this->getSeasonalPatterns($currentYear);
        
        // Predictive insights
        $predictions = $this->generatePredictiveInsights($monthlyTrends);
        
        return [
            'monthly_trends' => $monthlyTrends,
            'seasonal_patterns' => $seasonalAnalysis,
            'predictions' => $predictions,
            'trend_indicators' => $this->calculateTrendIndicators($monthlyTrends)
        ];
    }
    
    /**
     * Helper methods for complex calculations
     */
    private function identifySuccessPatterns($currentYear = null)
    {
        // Identify high-performing class characteristics
        $successfulClasses = Kelas::select('kelas.*', 
                DB::raw('COUNT(prestasi_siswa.id) as total_achievements'),
                DB::raw('COUNT(DISTINCT siswa.id) as total_students'))
            ->leftJoin('siswa', 'kelas.id', '=', 'siswa.id_kelas')
            ->leftJoin('prestasi_siswa', function($join) use ($currentYear) {
                $join->on('siswa.id', '=', 'prestasi_siswa.id_siswa')
                     ->where('prestasi_siswa.status', '=', 'diterima');
                if ($currentYear) {
                    $join->where('prestasi_siswa.id_tahun_ajaran', '=', $currentYear->id);
                }
            })
            ->groupBy('kelas.id')
            ->havingRaw('COUNT(prestasi_siswa.id) >= 10')
            ->get();
        
        return [
            'high_performing_classes' => $successfulClasses,
            'common_success_factors' => $this->analyzeSuccessFactors($successfulClasses)
        ];
    }
    
    private function generateStrategicRecommendations($currentYear = null)
    {
        $recommendations = [];
        
        // Based on participation rates
        $participationRate = $this->getExecutiveKPIs($currentYear)['participation_rate'];
        if ($participationRate < 70) {
            $recommendations[] = 'Tingkatkan program motivasi siswa untuk meningkatkan partisipasi dalam kompetisi';
        }
        
        // Based on academic balance
        $kpis = $this->getExecutiveKPIs($currentYear);
        $academicRatio = $kpis['academic_achievements'] / max(1, $kpis['non_academic_achievements']);
        if ($academicRatio > 2) {
            $recommendations[] = 'Perkuat program ekstrakurikuler untuk menyeimbangkan prestasi akademik dan non-akademik';
        } elseif ($academicRatio < 0.5) {
            $recommendations[] = 'Fokuskan program pengembangan prestasi akademik melalui olimpiade dan kompetisi sains';
        }
        
        return $recommendations;
    }
    
    private function calculateResourceEfficiency($currentYear = null)
    {
        // Simple efficiency calculation based on achievements per resource
        $totalEkskul = Ekstrakurikuler::count();
        $totalTeachers = User::where('role', 'guru')->count();
        $totalAchievements = PrestasiSiswa::where('status', 'diterima');
        
        if ($currentYear) {
            $totalAchievements->where('id_tahun_ajaran', $currentYear->id);
        }
        
        $totalAchievements = $totalAchievements->count();
        $totalResources = $totalEkskul + $totalTeachers;
        
        return $totalResources > 0 ? round($totalAchievements / $totalResources, 2) : 0;
    }
    
    private function getSeasonalPatterns($currentYear = null)
    {
        // Analyze seasonal achievement patterns
        $patterns = PrestasiSiswa::select(
                DB::raw('MONTH(tanggal_prestasi) as month'),
                DB::raw('COUNT(*) as achievements'))
            ->where('status', 'diterima');
        
        if ($currentYear) {
            $patterns->where('id_tahun_ajaran', $currentYear->id);
        }
        
        return $patterns->groupBy('month')
            ->orderBy('month')
            ->get()
            ->mapWithKeys(function($item) {
                $monthNames = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ];
                return [$monthNames[$item->month] => $item->achievements];
            });
    }
    
    private function generatePredictiveInsights($monthlyTrends)
    {
        if ($monthlyTrends->count() < 3) {
            return ['insufficient_data' => true];
        }
        
        $recent = $monthlyTrends->take(-3);
        $avgRecent = $recent->avg('total_achievements');
        $avgAll = $monthlyTrends->avg('total_achievements');
        
        $trend = $avgRecent > $avgAll ? 'increasing' : ($avgRecent < $avgAll ? 'decreasing' : 'stable');
        
        return [
            'trend' => $trend,
            'projected_next_month' => round($avgRecent),
            'confidence' => 'medium'
        ];
    }
    
    private function calculateTrendIndicators($monthlyTrends)
    {
        if ($monthlyTrends->count() < 2) {
            return ['insufficient_data' => true];
        }
        
        $latest = $monthlyTrends->last();
        $previous = $monthlyTrends->slice(-2, 1)->first();
        
        $growth = $previous->total_achievements > 0 ? 
            round((($latest->total_achievements - $previous->total_achievements) / $previous->total_achievements) * 100, 1) : 0;
        
        return [
            'month_over_month_growth' => $growth,
            'current_approval_rate' => round($latest->approval_rate, 1),
            'momentum' => abs($growth) > 10 ? 'high' : (abs($growth) > 5 ? 'medium' : 'low')
        ];
    }
    
    private function analyzeSuccessFactors($successfulClasses)
    {
        // Analyze common characteristics of successful classes
        $factors = [];
        
        foreach ($successfulClasses as $class) {
            // You could add more sophisticated analysis here
            $factors[] = "Kelas {$class->nama_kelas} dengan wali kelas yang aktif";
        }
        
        return $factors;
    }
} 