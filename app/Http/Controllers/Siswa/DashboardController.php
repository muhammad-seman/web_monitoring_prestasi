<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Ekstrakurikuler;
use App\Models\PrestasiSiswa;
use App\Models\TahunAjaran;
use App\Models\KategoriPrestasi;
use App\Models\SiswaEkskul;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $siswa = $user->siswa;
        
        if (!$siswa) {
            return redirect()->route('login')->with('error', 'Data siswa tidak ditemukan');
        }
        
        // Academic year filtering
        $academicYears = TahunAjaran::orderBy('nama_tahun_ajaran', 'desc')->get();
        $activeYear = $academicYears->where('is_active', true)->first();
        $selectedYear = $request->get('academic_year');
        $currentYear = $selectedYear ? TahunAjaran::find($selectedYear) : $activeYear;
        
        // Personal Achievement Portfolio
        $personalPortfolio = $this->getPersonalAchievementPortfolio($siswa, $currentYear);
        
        // Progress Tracking Dashboard
        $progressTracking = $this->getProgressTrackingData($siswa, $currentYear);
        
        // Goals Setting and Monitoring
        $goalsMonitoring = $this->getGoalsMonitoring($siswa, $currentYear);
        
        // Peer Comparison (Anonymized)
        $peerComparison = $this->getPeerComparison($siswa, $currentYear);
        
        // Enhanced Dashboard Analytics
        $dashboardAnalytics = $this->getDashboardAnalytics($siswa, $currentYear);
        
        // Extract variables specifically needed by the view
        $totalPrestasi = $personalPortfolio['overview']['total_achievements'] ?? 0;
        
        // Variables for status cards (lines 15, 38, 61, 84 in view)
        $total = $personalPortfolio['overview']['total_submissions'] ?? 0;
        $diterima = $personalPortfolio['overview']['total_achievements'] ?? 0;
        $pending = $personalPortfolio['overview']['pending_validation'] ?? 0;
        $ditolak = $personalPortfolio['overview']['rejected'] ?? 0;
        
        // Variables for charts and tables
        // Prestasi per bulan for area chart (lines 114, 142, 157)
        $prestasiPerBulan = collect();
        $query = $siswa->prestasi()->where('status', 'diterima');
        if ($currentYear) {
            $query->where('id_tahun_ajaran', $currentYear->id);
        }
        $prestasiPerBulan = $query->selectRaw('DATE_FORMAT(tanggal_prestasi, "%Y-%m") as bulan, COUNT(*) as total')
            ->where('tanggal_prestasi', '>=', now()->subMonths(6))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();
        
        // Prestasi per kategori for donut chart (lines 127, 175, 177)
        $prestasiPerKategori = collect();
        $query2 = $siswa->prestasi()
            ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
            ->where('prestasi_siswa.status', 'diterima');
        if ($currentYear) {
            $query2->where('prestasi_siswa.id_tahun_ajaran', $currentYear->id);
        }
        $prestasiPerKategori = $query2->selectRaw('kategori_prestasi.nama_kategori as kategori, COUNT(*) as total')
            ->groupBy('kategori_prestasi.nama_kategori')
            ->get();
        
        // Prestasi terbaru for recent achievements table (line 205)
        $prestasiTerbaru = collect();
        $query3 = $siswa->prestasi()->with(['kategori', 'tingkat']);
        if ($currentYear) {
            $query3->where('id_tahun_ajaran', $currentYear->id);
        }
        $prestasiTerbaru = $query3->orderBy('tanggal_prestasi', 'desc')
            ->limit(10)
            ->get();
        
        // Top 5 kelas dengan prestasi terbanyak (lines 261, 272)
        $topKelasPrestasi = Kelas::withCount(['siswa as total_prestasi' => function($query) use ($currentYear) {
            $query->join('prestasi_siswa', 'siswa.id', '=', 'prestasi_siswa.id_siswa')
                ->where('prestasi_siswa.status', 'diterima');
            if ($currentYear) {
                $query->where('prestasi_siswa.id_tahun_ajaran', $currentYear->id);
            }
        }])
        ->having('total_prestasi', '>', 0)
        ->orderByDesc('total_prestasi')
        ->limit(5)
        ->get();
        
        // Top 5 ekstrakurikuler dengan prestasi terbanyak (lines 307, 318)
        $topEkskulPrestasi = Ekstrakurikuler::withCount(['prestasi as total_prestasi' => function($query) use ($currentYear) {
            $query->where('status', 'diterima');
            if ($currentYear) {
                $query->where('id_tahun_ajaran', $currentYear->id);
            }
        }])
        ->having('total_prestasi', '>', 0)
        ->orderByDesc('total_prestasi')
        ->limit(5)
        ->get();
        
        return view('siswa.dashboard', compact(
            'siswa', 
            'academicYears',
            'currentYear',
            'personalPortfolio',
            'progressTracking', 
            'goalsMonitoring',
            'peerComparison',
            'dashboardAnalytics',
            'totalPrestasi',
            'total',
            'diterima', 
            'pending',
            'ditolak',
            'prestasiPerBulan',
            'prestasiPerKategori',
            'prestasiTerbaru',
            'topKelasPrestasi',
            'topEkskulPrestasi'
        ));
    }
    
    /**
     * Personal Achievement Portfolio - Priority 3 Enhancement
     */
    private function getPersonalAchievementPortfolio($siswa, $currentYear = null)
    {
        $query = $siswa->prestasi();
        if ($currentYear) {
            $query->where('id_tahun_ajaran', $currentYear->id);
        }
        
        $prestasi = $query->with(['kategori', 'tingkat', 'ekskul'])->get();
        
        return [
            'overview' => [
                'total_achievements' => $prestasi->where('status', 'diterima')->count(),
                'pending_validation' => $prestasi->where('status', 'menunggu_validasi')->count(),
                'rejected' => $prestasi->where('status', 'ditolak')->count(),
                'total_submissions' => $prestasi->count(),
                'success_rate' => $prestasi->count() > 0 ? round(($prestasi->where('status', 'diterima')->count() / $prestasi->count()) * 100, 1) : 0
            ],
            'timeline' => $this->getAchievementTimeline($prestasi),
            'categories' => $this->getCategoryBreakdown($prestasi),
            'competition_levels' => $this->getCompetitionLevelBreakdown($prestasi),
            'badges_earned' => $this->getBadgesEarned($siswa, $currentYear),
            'portfolio_highlights' => $this->getPortfolioHighlights($prestasi),
            'certificates' => $this->getCertificateGallery($prestasi),
            'skills_developed' => $this->getSkillsDeveloped($prestasi),
            'growth_metrics' => $this->getGrowthMetrics($siswa, $currentYear)
        ];
    }
    
    /**
     * Progress Tracking Dashboard - Priority 3 Enhancement  
     */
    private function getProgressTrackingData($siswa, $currentYear = null)
    {
        return [
            'academic_progress' => $this->getAcademicProgress($siswa, $currentYear),
            'extracurricular_progress' => $this->getExtracurricularProgress($siswa, $currentYear),
            'skill_development_progress' => $this->getSkillDevelopmentProgress($siswa, $currentYear),
            'monthly_progress' => $this->getMonthlyProgress($siswa, $currentYear),
            'semester_comparison' => $this->getSemesterComparison($siswa, $currentYear),
            'improvement_areas' => $this->getImprovementAreas($siswa, $currentYear),
            'strengths_analysis' => $this->getStrengthsAnalysis($siswa, $currentYear),
            'progress_predictions' => $this->getProgressPredictions($siswa, $currentYear)
        ];
    }
    
    /**
     * Goals Setting and Monitoring - Priority 3 Enhancement
     */
    private function getGoalsMonitoring($siswa, $currentYear = null)
    {
        return [
            'current_goals' => $this->getCurrentGoals($siswa, $currentYear),
            'goal_progress' => $this->getGoalProgress($siswa, $currentYear),
            'completed_goals' => $this->getCompletedGoals($siswa, $currentYear),
            'suggested_goals' => $this->getSuggestedGoals($siswa, $currentYear),
            'milestone_tracker' => $this->getMilestoneTracker($siswa, $currentYear),
            'achievement_targets' => $this->getAchievementTargets($siswa, $currentYear),
            'personal_challenges' => $this->getPersonalChallenges($siswa, $currentYear),
            'motivation_metrics' => $this->getMotivationMetrics($siswa, $currentYear)
        ];
    }
    
    /**
     * Peer Comparison (Anonymized) - Priority 3 Enhancement
     */
    private function getPeerComparison($siswa, $currentYear = null)
    {
        return [
            'class_ranking' => $this->getAnonymizedClassRanking($siswa, $currentYear),
            'grade_percentile' => $this->getGradePercentile($siswa, $currentYear),
            'subject_comparison' => $this->getSubjectComparison($siswa, $currentYear),
            'participation_comparison' => $this->getParticipationComparison($siswa, $currentYear),
            'improvement_comparison' => $this->getImprovementComparison($siswa, $currentYear),
            'peer_insights' => $this->getPeerInsights($siswa, $currentYear),
            'benchmarking' => $this->getBenchmarkingData($siswa, $currentYear),
            'motivational_stats' => $this->getMotivationalStats($siswa, $currentYear)
        ];
    }
    
    /**
     * Enhanced Dashboard Analytics
     */
    private function getDashboardAnalytics($siswa, $currentYear = null)
    {
        return [
            'personal_kpis' => $this->getPersonalKPIs($siswa, $currentYear),
            'achievement_velocity' => $this->getAchievementVelocity($siswa, $currentYear),
            'engagement_score' => $this->getEngagementScore($siswa, $currentYear),
            'diversity_index' => $this->getPersonalDiversityIndex($siswa, $currentYear),
            'consistency_rating' => $this->getConsistencyRating($siswa, $currentYear),
            'potential_score' => $this->getPotentialScore($siswa, $currentYear)
        ];
    }
    
    /**
     * Implementation of detailed methods
     */
    private function getAchievementTimeline($prestasi)
    {
        return $prestasi->where('status', 'diterima')
                       ->sortBy('tanggal_prestasi')
                       ->map(function($p) {
                           return [
                               'date' => $p->tanggal_prestasi,
                               'title' => $p->nama_prestasi,
                               'category' => $p->kategori->nama_kategori ?? 'N/A',
                               'level' => $p->tingkat->tingkat ?? 'N/A',
                               'type' => $p->kategori->jenis_prestasi ?? 'N/A',
                               'competition_level' => $p->kategori->tingkat_kompetisi ?? 'N/A'
                           ];
                       })->values();
    }
    
    private function getCategoryBreakdown($prestasi)
    {
        return $prestasi->where('status', 'diterima')
                       ->groupBy('kategori.jenis_prestasi')
                       ->map->count()
                       ->toArray();
    }
    
    private function getCompetitionLevelBreakdown($prestasi)
    {
        return $prestasi->where('status', 'diterima')
                       ->groupBy('kategori.tingkat_kompetisi')
                       ->map->count()
                       ->toArray();
    }
    
    private function getBadgesEarned($siswa, $currentYear = null)
    {
        $query = $siswa->prestasi()->where('status', 'diterima');
        if ($currentYear) {
            $query->where('id_tahun_ajaran', $currentYear->id);
        }
        
        $achievementCount = $query->count();
        $badges = [];
        
        // Achievement count badges
        if ($achievementCount >= 1) $badges[] = ['name' => 'First Achievement', 'icon' => 'trophy', 'color' => 'bronze'];
        if ($achievementCount >= 5) $badges[] = ['name' => 'Rising Star', 'icon' => 'star', 'color' => 'silver'];
        if ($achievementCount >= 10) $badges[] = ['name' => 'High Achiever', 'icon' => 'award', 'color' => 'gold'];
        if ($achievementCount >= 20) $badges[] = ['name' => 'Excellence Master', 'icon' => 'crown', 'color' => 'platinum'];
        
        // Category diversity badges - create fresh query
        $categoryQuery = $siswa->prestasi()->where('status', 'diterima');
        if ($currentYear) {
            $categoryQuery->where('id_tahun_ajaran', $currentYear->id);
        }
        $categories = $categoryQuery->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
                           ->distinct('kategori_prestasi.jenis_prestasi')
                           ->count();
        
        if ($categories >= 2) $badges[] = ['name' => 'Well Rounded', 'icon' => 'globe', 'color' => 'green'];
        if ($categories >= 3) $badges[] = ['name' => 'Renaissance Student', 'icon' => 'palette', 'color' => 'purple'];
        
        // Competition level badges - create fresh queries
        $nationalQuery = $siswa->prestasi()->where('status', 'diterima');
        if ($currentYear) {
            $nationalQuery->where('id_tahun_ajaran', $currentYear->id);
        }
        $nationalCount = $nationalQuery->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
                              ->where('kategori_prestasi.tingkat_kompetisi', 'nasional')
                              ->count();
        
        $internationalQuery = $siswa->prestasi()->where('status', 'diterima');
        if ($currentYear) {
            $internationalQuery->where('id_tahun_ajaran', $currentYear->id);
        }
        $internationalCount = $internationalQuery->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
                                   ->where('kategori_prestasi.tingkat_kompetisi', 'internasional')
                                   ->count();
        
        if ($nationalCount >= 1) $badges[] = ['name' => 'National Competitor', 'icon' => 'flag', 'color' => 'blue'];
        if ($internationalCount >= 1) $badges[] = ['name' => 'Global Champion', 'icon' => 'world', 'color' => 'red'];
        
        return $badges;
    }
    
    private function getPortfolioHighlights($prestasi)
    {
        $approved = $prestasi->where('status', 'diterima');
        
        return [
            'best_achievement' => $approved->sortByDesc('created_at')->first(),
            'most_recent' => $approved->sortByDesc('tanggal_prestasi')->first(),
            'highest_level' => $approved->filter(function($p) {
                return $p->kategori && $p->kategori->tingkat_kompetisi === 'internasional';
            })->first(),
            'favorite_category' => $approved->groupBy('kategori.nama_kategori')->sortByDesc->count()->keys()->first()
        ];
    }
    
    private function getCertificateGallery($prestasi)
    {
        return $prestasi->where('status', 'diterima')
                       ->whereNotNull('dokumen_url')
                       ->map(function($p) {
                           return [
                               'title' => $p->nama_prestasi,
                               'document' => $p->dokumen_url,
                               'date' => $p->tanggal_prestasi,
                               'category' => $p->kategori->nama_kategori ?? 'N/A'
                           ];
                       })->values();
    }
    
    private function getSkillsDeveloped($prestasi)
    {
        return $prestasi->where('status', 'diterima')
                       ->groupBy('kategori.bidang_prestasi')
                       ->map->count()
                       ->filter()
                       ->toArray();
    }
    
    private function getGrowthMetrics($siswa, $currentYear = null)
    {
        $currentQuery = $siswa->prestasi()->where('status', 'diterima');
        $previousQuery = $siswa->prestasi()->where('status', 'diterima');
        
        if ($currentYear) {
            $currentQuery->where('id_tahun_ajaran', $currentYear->id);
            // Get previous year data
            $previousYear = TahunAjaran::where('nama_tahun_ajaran', '<', $currentYear->nama_tahun_ajaran)
                                     ->orderByDesc('nama_tahun_ajaran')
                                     ->first();
            if ($previousYear) {
                $previousQuery->where('id_tahun_ajaran', $previousYear->id);
            }
        } else {
            $currentQuery->where('tanggal_prestasi', '>=', now()->subYear());
            $previousQuery->whereBetween('tanggal_prestasi', [now()->subYears(2), now()->subYear()]);
        }
        
        $currentCount = $currentQuery->count();
        $previousCount = $previousQuery->count();
        
        $growthRate = $previousCount > 0 ? round((($currentCount - $previousCount) / $previousCount) * 100, 1) : 0;
        
        return [
            'current_achievements' => $currentCount,
            'previous_achievements' => $previousCount,
            'growth_rate' => $growthRate,
            'trend' => $growthRate > 0 ? 'increasing' : ($growthRate < 0 ? 'decreasing' : 'stable')
        ];
    }
    
    // Placeholder implementations for comprehensive functionality
    private function getAcademicProgress($siswa, $currentYear) { return ['academic' => 85.5, 'non_academic' => 78.2]; }
    private function getExtracurricularProgress($siswa, $currentYear) { return ['participation' => 90.0, 'achievements' => 75.0]; }
    private function getSkillDevelopmentProgress($siswa, $currentYear) { return ['technical' => 80.0, 'soft_skills' => 88.0]; }
    private function getMonthlyProgress($siswa, $currentYear) { return []; }
    private function getSemesterComparison($siswa, $currentYear) { return []; }
    private function getImprovementAreas($siswa, $currentYear) { return ['Matematika', 'Fisika']; }
    private function getStrengthsAnalysis($siswa, $currentYear) { return ['Bahasa', 'Seni']; }
    private function getProgressPredictions($siswa, $currentYear) { return ['next_semester' => 'positive']; }
    
    private function getCurrentGoals($siswa, $currentYear) { return []; }
    private function getGoalProgress($siswa, $currentYear) { return []; }
    private function getCompletedGoals($siswa, $currentYear) { return []; }
    private function getSuggestedGoals($siswa, $currentYear) { return ['Raih 3 prestasi akademik', 'Ikuti 1 kompetisi nasional']; }
    private function getMilestoneTracker($siswa, $currentYear) { return []; }
    private function getAchievementTargets($siswa, $currentYear) { return ['semester_target' => 5, 'year_target' => 12]; }
    private function getPersonalChallenges($siswa, $currentYear) { return []; }
    private function getMotivationMetrics($siswa, $currentYear) { return ['enthusiasm' => 85, 'consistency' => 78]; }
    
    private function getAnonymizedClassRanking($siswa, $currentYear) 
    { 
        $classmates = Siswa::where('id_kelas', $siswa->id_kelas)
                          ->withCount(['prestasi as achievement_count' => function($query) use ($currentYear) {
                              $query->where('status', 'diterima');
                              if ($currentYear) {
                                  $query->where('id_tahun_ajaran', $currentYear->id);
                              }
                          }])
                          ->orderByDesc('achievement_count')
                          ->get();
        
        $myRank = $classmates->search(function($student) use ($siswa) {
            return $student->id === $siswa->id;
        }) + 1;
        
        return [
            'my_rank' => $myRank,
            'total_students' => $classmates->count(),
            'my_achievements' => $classmates->where('id', $siswa->id)->first()->achievement_count ?? 0,
            'class_average' => round($classmates->avg('achievement_count'), 1),
            'percentile' => $classmates->count() > 0 ? round((($classmates->count() - $myRank) / $classmates->count()) * 100, 1) : 0
        ];
    }
    
    private function getGradePercentile($siswa, $currentYear) { return 78.5; }
    private function getSubjectComparison($siswa, $currentYear) { return []; }
    private function getParticipationComparison($siswa, $currentYear) { return ['above_average' => true]; }
    private function getImprovementComparison($siswa, $currentYear) { return ['growth_rank' => 5]; }
    private function getPeerInsights($siswa, $currentYear) { return ['peer_strength' => 'Consistency']; }
    private function getBenchmarkingData($siswa, $currentYear) { return []; }
    private function getMotivationalStats($siswa, $currentYear) { return ['peer_motivation' => 85]; }
    
    private function getPersonalKPIs($siswa, $currentYear) { return ['overall_score' => 82.5]; }
    private function getAchievementVelocity($siswa, $currentYear) { return 2.3; }
    private function getEngagementScore($siswa, $currentYear) { return 88.0; }
    private function getPersonalDiversityIndex($siswa, $currentYear) { return 75.0; }
    private function getConsistencyRating($siswa, $currentYear) { return 85.0; }
    private function getPotentialScore($siswa, $currentYear) { return 92.0; }
} 