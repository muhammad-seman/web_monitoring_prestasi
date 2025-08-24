<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\PrestasiSiswa;
use App\Models\TahunAjaran;
use App\Models\KategoriPrestasi;
use Illuminate\Support\Facades\DB;

class PortfolioController extends Controller
{
    /**
     * Display personal achievement portfolio
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $siswa = $user->siswa;
        
        if (!$siswa) {
            return redirect()->route('login')->with('error', 'Data siswa tidak ditemukan');
        }
        
        $academicYears = TahunAjaran::orderBy('nama_tahun_ajaran', 'desc')->get();
        $activeYear = $academicYears->where('is_active', true)->first();
        $selectedYear = $request->get('academic_year');
        $currentYear = $selectedYear ? TahunAjaran::find($selectedYear) : $activeYear;
        
        $portfolioData = [
            'personal_statistics' => $this->getPersonalStatistics($siswa, $currentYear),
            'achievement_gallery' => $this->getAchievementGallery($siswa, $currentYear),
            'skill_matrix' => $this->getSkillMatrix($siswa, $currentYear),
            'growth_journey' => $this->getGrowthJourney($siswa, $currentYear),
            'badges_showcase' => $this->getBadgesShowcase($siswa, $currentYear),
            'certificates_display' => $this->getCertificatesDisplay($siswa, $currentYear),
            'impact_metrics' => $this->getImpactMetrics($siswa, $currentYear),
            'future_roadmap' => $this->getFutureRoadmap($siswa, $currentYear)
        ];
        
        return view('siswa.portfolio.index', compact(
            'siswa', 
            'academicYears',
            'currentYear',
            'portfolioData'
        ));
    }
    
    /**
     * Show detailed achievement timeline
     */
    public function timeline(Request $request)
    {
        $user = Auth::user();
        $siswa = $user->siswa;
        
        $selectedYear = $request->get('academic_year');
        $currentYear = $selectedYear ? TahunAjaran::find($selectedYear) : TahunAjaran::where('is_active', true)->first();
        
        $timelineData = $this->getDetailedTimeline($siswa, $currentYear);
        
        return response()->json([
            'success' => true,
            'timeline' => $timelineData
        ]);
    }
    
    /**
     * Get achievements by category for detailed view
     */
    public function categoryDetails($categoryId, Request $request)
    {
        $user = Auth::user();
        $siswa = $user->siswa;
        
        $selectedYear = $request->get('academic_year');
        $currentYear = $selectedYear ? TahunAjaran::find($selectedYear) : TahunAjaran::where('is_active', true)->first();
        
        $category = KategoriPrestasi::findOrFail($categoryId);
        $achievements = $siswa->prestasi()
            ->where('id_kategori_prestasi', $categoryId)
            ->where('status', 'diterima');
        
        if ($currentYear) {
            $achievements->where('id_tahun_ajaran', $currentYear->id);
        }
        
        $achievements = $achievements->with(['tingkat', 'ekskul'])
            ->orderByDesc('tanggal_prestasi')
            ->get();
        
        return response()->json([
            'success' => true,
            'category' => $category,
            'achievements' => $achievements,
            'statistics' => [
                'total_count' => $achievements->count(),
                'competition_levels' => $achievements->groupBy('kategori.tingkat_kompetisi')->map->count(),
                'monthly_distribution' => $achievements->groupBy(function($item) {
                    return date('Y-m', strtotime($item->tanggal_prestasi));
                })->map->count()
            ]
        ]);
    }
    
    /**
     * Share portfolio externally (read-only view)
     */
    public function share($shareToken)
    {
        // Implementation for sharing portfolio with parents/teachers
        // Would verify share token and show read-only view
        return view('siswa.portfolio.shared', compact('shareToken'));
    }
    
    /**
     * Export portfolio as PDF
     */
    public function exportPDF(Request $request)
    {
        $user = Auth::user();
        $siswa = $user->siswa;
        
        $selectedYear = $request->get('academic_year');
        $currentYear = $selectedYear ? TahunAjaran::find($selectedYear) : TahunAjaran::where('is_active', true)->first();
        
        $portfolioData = [
            'personal_info' => $this->getPersonalInfo($siswa),
            'achievements' => $this->getAchievementsForPDF($siswa, $currentYear),
            'statistics' => $this->getStatisticsForPDF($siswa, $currentYear),
            'skills' => $this->getSkillsForPDF($siswa, $currentYear),
            'certificates' => $this->getCertificatesForPDF($siswa, $currentYear)
        ];
        
        // Would generate PDF using DOMPDF
        return response()->json([
            'success' => true,
            'message' => 'PDF export will be implemented',
            'data' => $portfolioData
        ]);
    }
    
    /**
     * Private methods for portfolio data
     */
    private function getPersonalStatistics($siswa, $currentYear = null)
    {
        $query = $siswa->prestasi();
        if ($currentYear) {
            $query->where('id_tahun_ajaran', $currentYear->id);
        }
        
        $prestasi = $query->get();
        $approved = $prestasi->where('status', 'diterima');
        
        return [
            'total_achievements' => $approved->count(),
            'success_rate' => $prestasi->count() > 0 ? round(($approved->count() / $prestasi->count()) * 100, 1) : 0,
            'categories_mastered' => $approved->groupBy('id_kategori_prestasi')->count(),
            'competition_levels' => $approved->groupBy('kategori.tingkat_kompetisi')->map->count()->toArray(),
            'monthly_average' => $this->calculateMonthlyAverage($approved, $currentYear),
            'peak_performance_month' => $this->getPeakPerformanceMonth($approved),
            'diversity_score' => $this->calculateDiversityScore($approved),
            'consistency_rating' => $this->calculateConsistencyRating($approved)
        ];
    }
    
    private function getAchievementGallery($siswa, $currentYear = null)
    {
        $query = $siswa->prestasi()->where('status', 'diterima');
        if ($currentYear) {
            $query->where('id_tahun_ajaran', $currentYear->id);
        }
        
        return $query->with(['kategori', 'tingkat', 'ekskul'])
                    ->orderByDesc('tanggal_prestasi')
                    ->get()
                    ->map(function($achievement) {
                        return [
                            'id' => $achievement->id,
                            'title' => $achievement->nama_prestasi,
                            'date' => $achievement->tanggal_prestasi,
                            'category' => $achievement->kategori->nama_kategori ?? 'N/A',
                            'type' => $achievement->kategori->jenis_prestasi ?? 'N/A',
                            'level' => $achievement->kategori->tingkat_kompetisi ?? 'N/A',
                            'award_level' => $achievement->tingkat->tingkat ?? 'N/A',
                            'extracurricular' => $achievement->ekskul->nama ?? null,
                            'description' => $achievement->keterangan,
                            'document' => $achievement->dokumen_url,
                            'impact_score' => $this->calculateImpactScore($achievement)
                        ];
                    });
    }
    
    private function getSkillMatrix($siswa, $currentYear = null)
    {
        $query = $siswa->prestasi()->where('status', 'diterima');
        if ($currentYear) {
            $query->where('id_tahun_ajaran', $currentYear->id);
        }
        
        $achievements = $query->with('kategori')->get();
        
        $skills = [
            'academic_skills' => $achievements->where('kategori.jenis_prestasi', 'akademik')
                                           ->groupBy('kategori.bidang_prestasi')
                                           ->map->count()
                                           ->toArray(),
            'soft_skills' => $this->extractSoftSkills($achievements),
            'technical_skills' => $this->extractTechnicalSkills($achievements),
            'leadership_experience' => $this->extractLeadershipExperience($achievements),
            'collaboration_projects' => $this->extractCollaborationProjects($achievements)
        ];
        
        return $skills;
    }
    
    private function getGrowthJourney($siswa, $currentYear = null)
    {
        // Multi-year growth analysis
        $allYears = TahunAjaran::orderBy('nama_tahun_ajaran')->get();
        $journey = [];
        
        foreach ($allYears as $year) {
            $yearAchievements = $siswa->prestasi()
                                   ->where('id_tahun_ajaran', $year->id)
                                   ->where('status', 'diterima')
                                   ->count();
            
            if ($yearAchievements > 0) {
                $journey[] = [
                    'year' => $year->nama_tahun_ajaran,
                    'achievements' => $yearAchievements,
                    'is_current' => $currentYear && $year->id === $currentYear->id,
                    'growth_rate' => $this->calculateYearOverYearGrowth($siswa, $year)
                ];
            }
        }
        
        return [
            'yearly_progression' => $journey,
            'growth_trajectory' => $this->analyzeGrowthTrajectory($journey),
            'milestone_achievements' => $this->identifyMilestones($siswa, $currentYear),
            'breakthrough_moments' => $this->identifyBreakthroughs($siswa, $currentYear)
        ];
    }
    
    private function getBadgesShowcase($siswa, $currentYear = null)
    {
        $query = $siswa->prestasi()->where('status', 'diterima');
        if ($currentYear) {
            $query->where('id_tahun_ajaran', $currentYear->id);
        }
        
        $achievementCount = $query->count();
        $badges = [];
        
        // Performance-based badges
        $performanceBadges = $this->getPerformanceBadges($achievementCount);
        $badges = array_merge($badges, $performanceBadges);
        
        // Category-based badges
        $categoryBadges = $this->getCategoryBadges($siswa, $currentYear);
        $badges = array_merge($badges, $categoryBadges);
        
        // Special achievement badges
        $specialBadges = $this->getSpecialBadges($siswa, $currentYear);
        $badges = array_merge($badges, $specialBadges);
        
        return [
            'earned_badges' => $badges,
            'progress_to_next' => $this->getProgressToNextBadge($achievementCount),
            'badge_categories' => $this->organizeBadgesByCategory($badges),
            'rarity_showcase' => $this->getRarityShowcase($badges)
        ];
    }
    
    private function getCertificatesDisplay($siswa, $currentYear = null)
    {
        $query = $siswa->prestasi()->where('status', 'diterima')->whereNotNull('dokumen_url');
        if ($currentYear) {
            $query->where('id_tahun_ajaran', $currentYear->id);
        }
        
        return $query->with(['kategori', 'tingkat'])
                    ->orderByDesc('tanggal_prestasi')
                    ->get()
                    ->map(function($achievement) {
                        return [
                            'title' => $achievement->nama_prestasi,
                            'document_url' => $achievement->dokumen_url,
                            'date' => $achievement->tanggal_prestasi,
                            'category' => $achievement->kategori->nama_kategori ?? 'N/A',
                            'level' => $achievement->tingkat->tingkat ?? 'N/A',
                            'preview_available' => $this->hasPreview($achievement->dokumen_url),
                            'file_type' => $this->getFileType($achievement->dokumen_url)
                        ];
                    });
    }
    
    private function getImpactMetrics($siswa, $currentYear = null)
    {
        return [
            'school_contribution' => $this->calculateSchoolContribution($siswa, $currentYear),
            'peer_inspiration' => $this->calculatePeerInspiration($siswa, $currentYear),
            'skill_development' => $this->calculateSkillDevelopment($siswa, $currentYear),
            'community_impact' => $this->calculateCommunityImpact($siswa, $currentYear),
            'personal_growth' => $this->calculatePersonalGrowth($siswa, $currentYear)
        ];
    }
    
    private function getFutureRoadmap($siswa, $currentYear = null)
    {
        return [
            'recommended_goals' => $this->getRecommendedGoals($siswa, $currentYear),
            'skill_gaps' => $this->identifySkillGaps($siswa, $currentYear),
            'opportunity_areas' => $this->identifyOpportunityAreas($siswa, $currentYear),
            'next_milestones' => $this->suggestNextMilestones($siswa, $currentYear),
            'development_priorities' => $this->getDevelopmentPriorities($siswa, $currentYear)
        ];
    }
    
    // Helper calculation methods
    private function calculateMonthlyAverage($achievements, $currentYear)
    {
        if ($achievements->isEmpty()) return 0;
        
        $months = $achievements->groupBy(function($item) {
            return date('Y-m', strtotime($item->tanggal_prestasi));
        })->count();
        
        return $months > 0 ? round($achievements->count() / $months, 1) : 0;
    }
    
    private function getPeakPerformanceMonth($achievements)
    {
        if ($achievements->isEmpty()) return null;
        
        return $achievements->groupBy(function($item) {
            return date('Y-m', strtotime($item->tanggal_prestasi));
        })->sortByDesc->count()->keys()->first();
    }
    
    private function calculateDiversityScore($achievements)
    {
        if ($achievements->isEmpty()) return 0;
        
        $totalCategories = KategoriPrestasi::count();
        $usedCategories = $achievements->groupBy('id_kategori_prestasi')->count();
        
        return $totalCategories > 0 ? round(($usedCategories / $totalCategories) * 100, 1) : 0;
    }
    
    private function calculateConsistencyRating($achievements)
    {
        // Calculate based on distribution across months
        if ($achievements->count() < 2) return 0;
        
        $monthlyDistribution = $achievements->groupBy(function($item) {
            return date('Y-m', strtotime($item->tanggal_prestasi));
        })->map->count();
        
        $variance = $this->calculateVariance($monthlyDistribution->values()->toArray());
        $mean = $monthlyDistribution->avg();
        
        $consistencyScore = $mean > 0 ? max(0, 100 - (($variance / $mean) * 50)) : 0;
        
        return round($consistencyScore, 1);
    }
    
    private function calculateVariance($values)
    {
        if (count($values) < 2) return 0;
        
        $mean = array_sum($values) / count($values);
        $variance = array_sum(array_map(function($x) use ($mean) {
            return pow($x - $mean, 2);
        }, $values)) / count($values);
        
        return $variance;
    }
    
    // Placeholder implementations for comprehensive functionality
    private function calculateImpactScore($achievement) { return rand(70, 100); }
    private function extractSoftSkills($achievements) { return ['Communication', 'Leadership', 'Teamwork']; }
    private function extractTechnicalSkills($achievements) { return ['Problem Solving', 'Analysis', 'Research']; }
    private function extractLeadershipExperience($achievements) { return ['Team Leader', 'Project Manager']; }
    private function extractCollaborationProjects($achievements) { return ['Group Projects', 'Team Competitions']; }
    private function calculateYearOverYearGrowth($siswa, $year) { return rand(-10, 50); }
    private function analyzeGrowthTrajectory($journey) { return 'positive_trend'; }
    private function identifyMilestones($siswa, $currentYear) { return []; }
    private function identifyBreakthroughs($siswa, $currentYear) { return []; }
    private function getPerformanceBadges($count) { return []; }
    private function getCategoryBadges($siswa, $currentYear) { return []; }
    private function getSpecialBadges($siswa, $currentYear) { return []; }
    private function getProgressToNextBadge($count) { return ['next' => 'Rising Star', 'progress' => 60]; }
    private function organizeBadgesByCategory($badges) { return []; }
    private function getRarityShowcase($badges) { return []; }
    private function hasPreview($url) { return str_contains($url, '.pdf'); }
    private function getFileType($url) { return pathinfo($url, PATHINFO_EXTENSION); }
    private function calculateSchoolContribution($siswa, $currentYear) { return 75.5; }
    private function calculatePeerInspiration($siswa, $currentYear) { return 82.0; }
    private function calculateSkillDevelopment($siswa, $currentYear) { return 88.5; }
    private function calculateCommunityImpact($siswa, $currentYear) { return 70.0; }
    private function calculatePersonalGrowth($siswa, $currentYear) { return 85.0; }
    private function getRecommendedGoals($siswa, $currentYear) { return ['Join robotics club', 'Participate in science fair']; }
    private function identifySkillGaps($siswa, $currentYear) { return ['Public Speaking', 'Data Analysis']; }
    private function identifyOpportunityAreas($siswa, $currentYear) { return ['STEM competitions', 'Arts exhibitions']; }
    private function suggestNextMilestones($siswa, $currentYear) { return ['Reach 10 achievements', 'Win national competition']; }
    private function getDevelopmentPriorities($siswa, $currentYear) { return ['Academic excellence', 'Leadership skills']; }
    
    // Additional helper methods for PDF export
    private function getPersonalInfo($siswa) { return []; }
    private function getAchievementsForPDF($siswa, $currentYear) { return []; }
    private function getStatisticsForPDF($siswa, $currentYear) { return []; }
    private function getSkillsForPDF($siswa, $currentYear) { return []; }
    private function getCertificatesForPDF($siswa, $currentYear) { return []; }
    private function getDetailedTimeline($siswa, $currentYear) { return []; }
}