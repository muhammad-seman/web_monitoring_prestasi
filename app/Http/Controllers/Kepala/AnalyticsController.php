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

class AnalyticsController extends Controller
{
    /**
     * Main analytics dashboard for principal
     */
    public function index()
    {
        $academicYears = TahunAjaran::orderBy('nama_tahun_ajaran', 'desc')->get();
        $activeYear = $academicYears->where('is_active', true)->first();
        
        return view('kepala.analytics.index', compact('academicYears', 'activeYear'));
    }
    
    /**
     * School-wide performance analytics
     */
    public function schoolPerformance(Request $request)
    {
        $selectedYear = $request->get('academic_year');
        $currentYear = $selectedYear ? TahunAjaran::find($selectedYear) : TahunAjaran::where('is_active', true)->first();
        
        $analytics = [
            'overview_metrics' => $this->getSchoolOverviewMetrics($currentYear),
            'grade_level_analysis' => $this->getGradeLevelAnalysis($currentYear),
            'subject_performance' => $this->getSubjectPerformanceAnalysis($currentYear),
            'competition_analysis' => $this->getCompetitionLevelAnalysis($currentYear),
            'achievement_trends' => $this->getAchievementTrends($currentYear),
            'performance_distribution' => $this->getPerformanceDistribution($currentYear)
        ];
        
        return response()->json([
            'success' => true,
            'analytics' => $analytics,
            'current_year' => $currentYear
        ]);
    }
    
    /**
     * Detailed teacher effectiveness analysis
     */
    public function teacherAnalysis(Request $request)
    {
        $selectedYear = $request->get('academic_year');
        $currentYear = $selectedYear ? TahunAjaran::find($selectedYear) : TahunAjaran::where('is_active', true)->first();
        
        $analysis = [
            'teacher_performance_matrix' => $this->getTeacherPerformanceMatrix($currentYear),
            'class_effectiveness' => $this->getClassEffectivenessAnalysis($currentYear),
            'workload_distribution' => $this->getTeacherWorkloadAnalysis($currentYear),
            'collaboration_metrics' => $this->getTeacherCollaborationMetrics($currentYear),
            'development_needs' => $this->getTeacherDevelopmentNeeds($currentYear),
            'recognition_recommendations' => $this->getTeacherRecognitionRecommendations($currentYear)
        ];
        
        return response()->json([
            'success' => true,
            'analysis' => $analysis,
            'current_year' => $currentYear
        ]);
    }
    
    /**
     * Department-wise strategic analysis
     */
    public function departmentAnalysis(Request $request)
    {
        $selectedYear = $request->get('academic_year');
        $department = $request->get('department');
        $currentYear = $selectedYear ? TahunAjaran::find($selectedYear) : TahunAjaran::where('is_active', true)->first();
        
        $analysis = [
            'department_overview' => $this->getDepartmentOverview($department, $currentYear),
            'subject_breakdown' => $this->getSubjectBreakdown($department, $currentYear),
            'resource_utilization' => $this->getDepartmentResourceUtilization($department, $currentYear),
            'teacher_expertise' => $this->getDepartmentTeacherExpertise($department, $currentYear),
            'improvement_opportunities' => $this->getDepartmentImprovementOpportunities($department, $currentYear),
            'strategic_recommendations' => $this->getDepartmentStrategicRecommendations($department, $currentYear)
        ];
        
        return response()->json([
            'success' => true,
            'analysis' => $analysis,
            'department' => $department,
            'current_year' => $currentYear
        ]);
    }
    
    /**
     * Multi-dimensional comparative analysis
     */
    public function comparativeAnalysis(Request $request)
    {
        $analysisType = $request->get('type', 'year_over_year');
        $selectedYear = $request->get('academic_year');
        $currentYear = $selectedYear ? TahunAjaran::find($selectedYear) : TahunAjaran::where('is_active', true)->first();
        
        switch ($analysisType) {
            case 'year_over_year':
                $analysis = $this->getYearOverYearComparison($currentYear);
                break;
            case 'grade_comparison':
                $analysis = $this->getGradeComparison($currentYear);
                break;
            case 'department_comparison':
                $analysis = $this->getDepartmentComparison($currentYear);
                break;
            case 'teacher_comparison':
                $analysis = $this->getTeacherComparison($currentYear);
                break;
            case 'seasonal_comparison':
                $analysis = $this->getSeasonalComparison($currentYear);
                break;
            default:
                $analysis = $this->getYearOverYearComparison($currentYear);
        }
        
        return response()->json([
            'success' => true,
            'analysis' => $analysis,
            'type' => $analysisType,
            'current_year' => $currentYear
        ]);
    }
    
    /**
     * Strategic forecasting and predictions
     */
    public function strategicForecasting(Request $request)
    {
        $forecastType = $request->get('type', 'achievement_projection');
        $timeHorizon = $request->get('horizon', 12); // months
        $selectedYear = $request->get('academic_year');
        $currentYear = $selectedYear ? TahunAjaran::find($selectedYear) : TahunAjaran::where('is_active', true)->first();
        
        $forecasting = [
            'achievement_projections' => $this->getAchievementProjections($currentYear, $timeHorizon),
            'resource_planning' => $this->getResourcePlanningForecasts($currentYear, $timeHorizon),
            'enrollment_trends' => $this->getEnrollmentTrendForecasts($currentYear, $timeHorizon),
            'budget_implications' => $this->getBudgetImplicationForecasts($currentYear, $timeHorizon),
            'risk_analysis' => $this->getRiskAnalysisForecasts($currentYear, $timeHorizon),
            'opportunity_identification' => $this->getOpportunityForecasts($currentYear, $timeHorizon)
        ];
        
        return response()->json([
            'success' => true,
            'forecasting' => $forecasting,
            'time_horizon' => $timeHorizon,
            'current_year' => $currentYear
        ]);
    }
    
    /**
     * Implementation of detailed analytics methods
     */
    private function getSchoolOverviewMetrics($currentYear = null)
    {
        $baseQuery = PrestasiSiswa::query();
        if ($currentYear) {
            $baseQuery->where('id_tahun_ajaran', $currentYear->id);
        }
        
        return [
            'total_students' => Siswa::count(),
            'active_students' => Siswa::whereHas('prestasi', function($q) use ($currentYear) {
                if ($currentYear) $q->where('id_tahun_ajaran', $currentYear->id);
            })->count(),
            'total_achievements' => $baseQuery->where('status', 'diterima')->count(),
            'total_submissions' => $baseQuery->count(),
            'approval_rate' => $this->calculateApprovalRate($baseQuery),
            'participation_rate' => $this->calculateParticipationRate($currentYear),
            'diversity_index' => $this->calculateAchievementDiversityIndex($currentYear),
            'excellence_score' => $this->calculateSchoolExcellenceScore($currentYear)
        ];
    }
    
    private function getGradeLevelAnalysis($currentYear = null)
    {
        $gradeLevels = ['X', 'XI', 'XII'];
        $analysis = [];
        
        foreach ($gradeLevels as $grade) {
            $kelasIds = Kelas::where('nama_kelas', 'like', $grade . '%')->pluck('id');
            $siswaIds = Siswa::whereIn('id_kelas', $kelasIds)->pluck('id');
            
            $achievements = PrestasiSiswa::whereIn('id_siswa', $siswaIds)->where('status', 'diterima');
            if ($currentYear) {
                $achievements->where('id_tahun_ajaran', $currentYear->id);
            }
            
            $analysis[$grade] = [
                'total_students' => $siswaIds->count(),
                'active_students' => Siswa::whereIn('id', $siswaIds)->whereHas('prestasi', function($q) use ($currentYear) {
                    $q->where('status', 'diterima');
                    if ($currentYear) $q->where('id_tahun_ajaran', $currentYear->id);
                })->count(),
                'total_achievements' => $achievements->count(),
                'average_per_student' => $siswaIds->count() > 0 ? round($achievements->count() / $siswaIds->count(), 2) : 0,
                'participation_rate' => $this->calculateGradeParticipationRate($grade, $currentYear),
                'top_categories' => $this->getGradeTopCategories($grade, $currentYear),
                'performance_trend' => $this->getGradePerformanceTrend($grade, $currentYear)
            ];
        }
        
        return $analysis;
    }
    
    private function getSubjectPerformanceAnalysis($currentYear = null)
    {
        $subjects = KategoriPrestasi::select('bidang_prestasi', DB::raw('COUNT(prestasi_siswa.id) as total_achievements'))
            ->leftJoin('prestasi_siswa', function($join) use ($currentYear) {
                $join->on('kategori_prestasi.id', '=', 'prestasi_siswa.id_kategori_prestasi')
                     ->where('prestasi_siswa.status', '=', 'diterima');
                if ($currentYear) {
                    $join->where('prestasi_siswa.id_tahun_ajaran', '=', $currentYear->id);
                }
            })
            ->whereNotNull('kategori_prestasi.bidang_prestasi')
            ->groupBy('kategori_prestasi.bidang_prestasi')
            ->orderByDesc('total_achievements')
            ->get();
        
        $analysis = [];
        foreach ($subjects as $subject) {
            $analysis[$subject->bidang_prestasi] = [
                'total_achievements' => $subject->total_achievements,
                'participation_rate' => $this->getSubjectParticipationRate($subject->bidang_prestasi, $currentYear),
                'competition_levels' => $this->getSubjectCompetitionLevels($subject->bidang_prestasi, $currentYear),
                'teacher_involvement' => $this->getSubjectTeacherInvolvement($subject->bidang_prestasi, $currentYear),
                'growth_trend' => $this->getSubjectGrowthTrend($subject->bidang_prestasi, $currentYear)
            ];
        }
        
        return $analysis;
    }
    
    private function getCompetitionLevelAnalysis($currentYear = null)
    {
        $levels = ['sekolah', 'kabupaten', 'provinsi', 'nasional', 'internasional'];
        $analysis = [];
        
        foreach ($levels as $level) {
            $achievements = PrestasiSiswa::join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
                ->where('kategori_prestasi.tingkat_kompetisi', $level)
                ->where('prestasi_siswa.status', 'diterima');
            
            if ($currentYear) {
                $achievements->where('prestasi_siswa.id_tahun_ajaran', $currentYear->id);
            }
            
            $analysis[$level] = [
                'total_achievements' => $achievements->count(),
                'unique_students' => $achievements->distinct('prestasi_siswa.id_siswa')->count(),
                'subject_diversity' => $achievements->distinct('kategori_prestasi.bidang_prestasi')->count(),
                'monthly_distribution' => $this->getLevelMonthlyDistribution($level, $currentYear),
                'success_rate' => $this->getLevelSuccessRate($level, $currentYear)
            ];
        }
        
        return $analysis;
    }
    
    private function getAchievementTrends($currentYear = null)
    {
        $query = PrestasiSiswa::select(
                DB::raw('DATE_FORMAT(tanggal_prestasi, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "diterima" THEN 1 ELSE 0 END) as approved'),
                DB::raw('COUNT(DISTINCT id_siswa) as unique_students'))
            ->where('tanggal_prestasi', '>=', now()->subMonths(18));
        
        if ($currentYear) {
            $query->where('id_tahun_ajaran', $currentYear->id);
        }
        
        return $query->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function($item) {
                return [
                    'month' => $item->month,
                    'total' => $item->total,
                    'approved' => $item->approved,
                    'unique_students' => $item->unique_students,
                    'approval_rate' => $item->total > 0 ? round(($item->approved / $item->total) * 100, 1) : 0
                ];
            });
    }
    
    private function getPerformanceDistribution($currentYear = null)
    {
        // Performance distribution across different metrics
        $students = Siswa::withCount(['prestasi as achievement_count' => function($query) use ($currentYear) {
            $query->where('status', 'diterima');
            if ($currentYear) {
                $query->where('id_tahun_ajaran', $currentYear->id);
            }
        }])->get();
        
        $distribution = [
            'no_achievements' => $students->where('achievement_count', 0)->count(),
            'low_performers' => $students->whereBetween('achievement_count', [1, 2])->count(),
            'average_performers' => $students->whereBetween('achievement_count', [3, 5])->count(),
            'high_performers' => $students->whereBetween('achievement_count', [6, 10])->count(),
            'exceptional_performers' => $students->where('achievement_count', '>', 10)->count()
        ];
        
        return $distribution;
    }
    
    private function getTeacherPerformanceMatrix($currentYear = null)
    {
        return User::where('role', 'guru')
            ->select('users.*')
            ->withCount(['createdPrestasi as achievements_created' => function($query) use ($currentYear) {
                if ($currentYear) $query->where('id_tahun_ajaran', $currentYear->id);
            }])
            ->withCount(['validatedPrestasi as achievements_validated' => function($query) use ($currentYear) {
                $query->where('status', 'diterima');
                if ($currentYear) $query->where('id_tahun_ajaran', $currentYear->id);
            }])
            ->with('kelasDiampu')
            ->get()
            ->map(function($teacher) use ($currentYear) {
                return [
                    'id' => $teacher->id,
                    'name' => $teacher->name,
                    'achievements_created' => $teacher->achievements_created,
                    'achievements_validated' => $teacher->achievements_validated,
                    'classes_managed' => $teacher->kelasDiampu->count(),
                    'effectiveness_score' => $this->calculateTeacherEffectiveness($teacher, $currentYear),
                    'collaboration_score' => $this->calculateCollaborationScore($teacher, $currentYear),
                    'innovation_index' => $this->calculateInnovationIndex($teacher, $currentYear)
                ];
            });
    }
    
    // Helper calculation methods
    private function calculateApprovalRate($query)
    {
        $total = $query->count();
        $approved = $query->where('status', 'diterima')->count();
        return $total > 0 ? round(($approved / $total) * 100, 1) : 0;
    }
    
    private function calculateParticipationRate($currentYear = null)
    {
        $totalStudents = Siswa::count();
        $activeStudents = Siswa::whereHas('prestasi', function($query) use ($currentYear) {
            if ($currentYear) {
                $query->where('id_tahun_ajaran', $currentYear->id);
            }
        })->count();
        
        return $totalStudents > 0 ? round(($activeStudents / $totalStudents) * 100, 1) : 0;
    }
    
    private function calculateAchievementDiversityIndex($currentYear = null)
    {
        // Calculate diversity of achievement categories
        $query = PrestasiSiswa::where('status', 'diterima');
        if ($currentYear) {
            $query->where('id_tahun_ajaran', $currentYear->id);
        }
        
        $totalCategories = KategoriPrestasi::count();
        $usedCategories = $query->distinct('id_kategori_prestasi')->count();
        
        return $totalCategories > 0 ? round(($usedCategories / $totalCategories) * 100, 1) : 0;
    }
    
    private function calculateSchoolExcellenceScore($currentYear = null)
    {
        $kpis = [
            'participation_rate' => $this->calculateParticipationRate($currentYear),
            'diversity_index' => $this->calculateAchievementDiversityIndex($currentYear),
            'approval_rate' => 85, // Placeholder - would calculate from actual data
            'international_achievements' => $this->getInternationalAchievementPercentage($currentYear)
        ];
        
        return round(collect($kpis)->avg(), 1);
    }
    
    private function getInternationalAchievementPercentage($currentYear = null)
    {
        $totalQuery = PrestasiSiswa::where('status', 'diterima');
        $internationalQuery = PrestasiSiswa::where('status', 'diterima')
            ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
            ->where('kategori_prestasi.tingkat_kompetisi', 'internasional');
        
        if ($currentYear) {
            $totalQuery->where('id_tahun_ajaran', $currentYear->id);
            $internationalQuery->where('prestasi_siswa.id_tahun_ajaran', $currentYear->id);
        }
        
        $total = $totalQuery->count();
        $international = $internationalQuery->count();
        
        return $total > 0 ? round(($international / $total) * 100, 1) : 0;
    }
    
    // Placeholder methods for complex calculations - would be fully implemented
    private function getGradeParticipationRate($grade, $currentYear) { return 75.5; }
    private function getGradeTopCategories($grade, $currentYear) { return ['Matematika', 'IPA']; }
    private function getGradePerformanceTrend($grade, $currentYear) { return 'increasing'; }
    private function getSubjectParticipationRate($subject, $currentYear) { return 60.0; }
    private function getSubjectCompetitionLevels($subject, $currentYear) { return ['nasional' => 5, 'provinsi' => 10]; }
    private function getSubjectTeacherInvolvement($subject, $currentYear) { return 3; }
    private function getSubjectGrowthTrend($subject, $currentYear) { return 'stable'; }
    private function getLevelMonthlyDistribution($level, $currentYear) { return []; }
    private function getLevelSuccessRate($level, $currentYear) { return 80.0; }
    private function calculateTeacherEffectiveness($teacher, $currentYear) { return 85.5; }
    private function calculateCollaborationScore($teacher, $currentYear) { return 92.0; }
    private function calculateInnovationIndex($teacher, $currentYear) { return 78.5; }
    
    // Additional placeholder methods for comprehensive functionality
    private function getClassEffectivenessAnalysis($currentYear) { return []; }
    private function getTeacherWorkloadAnalysis($currentYear) { return []; }
    private function getTeacherCollaborationMetrics($currentYear) { return []; }
    private function getTeacherDevelopmentNeeds($currentYear) { return []; }
    private function getTeacherRecognitionRecommendations($currentYear) { return []; }
    private function getDepartmentOverview($department, $currentYear) { return []; }
    private function getSubjectBreakdown($department, $currentYear) { return []; }
    private function getDepartmentResourceUtilization($department, $currentYear) { return []; }
    private function getDepartmentTeacherExpertise($department, $currentYear) { return []; }
    private function getDepartmentImprovementOpportunities($department, $currentYear) { return []; }
    private function getDepartmentStrategicRecommendations($department, $currentYear) { return []; }
    private function getYearOverYearComparison($currentYear) { return []; }
    private function getGradeComparison($currentYear) { return []; }
    private function getDepartmentComparison($currentYear) { return []; }
    private function getTeacherComparison($currentYear) { return []; }
    private function getSeasonalComparison($currentYear) { return []; }
    private function getAchievementProjections($currentYear, $timeHorizon) { return []; }
    private function getResourcePlanningForecasts($currentYear, $timeHorizon) { return []; }
    private function getEnrollmentTrendForecasts($currentYear, $timeHorizon) { return []; }
    private function getBudgetImplicationForecasts($currentYear, $timeHorizon) { return []; }
    private function getRiskAnalysisForecasts($currentYear, $timeHorizon) { return []; }
    private function getOpportunityForecasts($currentYear, $timeHorizon) { return []; }
}