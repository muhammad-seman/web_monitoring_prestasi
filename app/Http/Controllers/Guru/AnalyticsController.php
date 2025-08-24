<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\PrestasiSiswa;
use App\Models\TahunAjaran;
use App\Models\KategoriPrestasi;
use App\Models\SiswaEkskul;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $academicYears = TahunAjaran::orderBy('nama_tahun_ajaran', 'desc')->get();
        $activeYear = $academicYears->where('is_active', true)->first();
        
        $kelas = Kelas::where('id_wali_kelas', $user->id)->get();
        $siswa = Siswa::whereIn('id_kelas', $kelas->pluck('id'))->get();
        
        return view('guru.analytics.index', compact('academicYears', 'activeYear', 'kelas', 'siswa'));
    }
    
    public function individualStudentAnalysis($siswaId, Request $request)
    {
        $user = Auth::user();
        $selectedYear = $request->get('academic_year');
        
        $siswa = Siswa::with(['kelas', 'prestasi', 'ekstrakurikuler'])->findOrFail($siswaId);
        
        if (!$this->canAccessStudent($siswa, $user)) {
            abort(403, 'Anda tidak memiliki akses ke data siswa ini.');
        }
        
        $academicYears = TahunAjaran::orderBy('nama_tahun_ajaran', 'desc')->get();
        $activeYear = $academicYears->where('is_active', true)->first();
        $currentYear = $selectedYear ? TahunAjaran::find($selectedYear) : $activeYear;
        
        $analysis = [
            'basic_info' => $this->getStudentBasicInfo($siswa),
            'achievement_summary' => $this->getStudentAchievementSummary($siswa->id, $currentYear),
            'timeline_data' => $this->getStudentAchievementTimeline($siswa->id, $currentYear),
            'category_breakdown' => $this->getStudentCategoryBreakdown($siswa->id, $currentYear),
            'competition_levels' => $this->getStudentCompetitionLevels($siswa->id, $currentYear),
            'extracurricular_correlation' => $this->getExtracurricularAchievements($siswa->id, $currentYear),
            'class_ranking' => $this->getStudentClassRanking($siswa, $currentYear),
            'yearly_progression' => $this->getStudentYearlyProgression($siswa->id),
            'achievement_gaps' => $this->getStudentAchievementGaps($siswa->id, $currentYear),
            'recommendations' => $this->getStudentRecommendations($siswa->id, $currentYear)
        ];
        
        return response()->json([
            'success' => true,
            'student' => $siswa,
            'analysis' => $analysis,
            'academic_years' => $academicYears,
            'current_year' => $currentYear
        ]);
    }
    
    public function classPerformanceAnalysis(Request $request)
    {
        $user = Auth::user();
        $selectedYear = $request->get('academic_year');
        $kelasId = $request->get('kelas_id');
        
        $academicYears = TahunAjaran::orderBy('nama_tahun_ajaran', 'desc')->get();
        $activeYear = $academicYears->where('is_active', true)->first();
        $currentYear = $selectedYear ? TahunAjaran::find($selectedYear) : $activeYear;
        
        $kelasQuery = Kelas::where('id_wali_kelas', $user->id);
        if ($kelasId) {
            $kelasQuery->where('id', $kelasId);
        }
        $kelas = $kelasQuery->get();
        
        $analysis = [
            'class_overview' => $this->getClassOverview($kelas, $currentYear),
            'performance_metrics' => $this->getClassPerformanceMetrics($kelas, $currentYear),
            'achievement_distribution' => $this->getClassAchievementDistribution($kelas, $currentYear),
            'top_performers' => $this->getClassTopPerformers($kelas, $currentYear),
            'underperformers' => $this->getClassUnderperformers($kelas, $currentYear),
            'monthly_trends' => $this->getClassMonthlyTrends($kelas, $currentYear),
            'comparison_data' => $this->getClassComparison($kelas, $currentYear),
            'improvement_areas' => $this->getClassImprovementAreas($kelas, $currentYear)
        ];
        
        return response()->json([
            'success' => true,
            'analysis' => $analysis,
            'classes' => $kelas,
            'academic_years' => $academicYears,
            'current_year' => $currentYear
        ]);
    }
    
    public function studentProgressionTracking($siswaId)
    {
        $user = Auth::user();
        $siswa = Siswa::with('kelas')->findOrFail($siswaId);
        
        if (!$this->canAccessStudent($siswa, $user)) {
            abort(403, 'Anda tidak memiliki akses ke data siswa ini.');
        }
        
        $progression = [
            'academic_progression' => $this->getAcademicProgression($siswa->id),
            'achievement_growth' => $this->getAchievementGrowth($siswa->id),
            'skill_development' => $this->getSkillDevelopment($siswa->id),
            'extracurricular_progression' => $this->getExtracurricularProgression($siswa->id),
            'behavioral_trends' => $this->getBehavioralTrends($siswa->id),
            'goal_tracking' => $this->getGoalTracking($siswa->id),
            'milestone_achievements' => $this->getMilestoneAchievements($siswa->id),
            'future_predictions' => $this->getFuturePredictions($siswa->id)
        ];
        
        return response()->json([
            'success' => true,
            'student' => $siswa,
            'progression' => $progression
        ]);
    }
    
    private function canAccessStudent($siswa, $user)
    {
        return Kelas::where('id', $siswa->id_kelas)
                   ->where('id_wali_kelas', $user->id)
                   ->exists();
    }
    
    private function getStudentBasicInfo($siswa)
    {
        return [
            'nama' => $siswa->nama,
            'nisn' => $siswa->nisn,
            'kelas' => $siswa->kelas->nama_kelas ?? 'N/A',
            'jenis_kelamin' => $siswa->jenis_kelamin,
            'tanggal_lahir' => $siswa->tanggal_lahir,
            'alamat' => $siswa->alamat
        ];
    }
    
    private function getStudentAchievementSummary($siswaId, $currentYear = null)
    {
        $query = PrestasiSiswa::where('id_siswa', $siswaId);
        
        if ($currentYear) {
            $query->where('id_tahun_ajaran', $currentYear->id);
        }
        
        $prestasi = $query->get();
        
        return [
            'total_prestasi' => $prestasi->count(),
            'diterima' => $prestasi->where('status', 'diterima')->count(),
            'pending' => $prestasi->where('status', 'menunggu_validasi')->count(),
            'ditolak' => $prestasi->where('status', 'ditolak')->count(),
            'acceptance_rate' => $prestasi->count() > 0 ? round(($prestasi->where('status', 'diterima')->count() / $prestasi->count()) * 100, 1) : 0,
            'latest_achievement' => $prestasi->where('status', 'diterima')->sortByDesc('tanggal_prestasi')->first()
        ];
    }
    
    private function getStudentAchievementTimeline($siswaId, $currentYear = null)
    {
        $query = PrestasiSiswa::where('id_siswa', $siswaId)
                             ->where('status', 'diterima')
                             ->with('kategoriPrestasi');
                             
        if ($currentYear) {
            $query->where('id_tahun_ajaran', $currentYear->id);
        }
        
        return $query->orderBy('tanggal_prestasi')
                    ->get()
                    ->map(function($prestasi) {
                        return [
                            'tanggal' => $prestasi->tanggal_prestasi,
                            'nama_prestasi' => $prestasi->nama_prestasi,
                            'kategori' => $prestasi->kategoriPrestasi->nama_kategori ?? 'N/A',
                            'tingkat' => $prestasi->kategoriPrestasi->tingkat_kompetisi ?? 'N/A',
                            'jenis' => $prestasi->kategoriPrestasi->jenis_prestasi ?? 'N/A'
                        ];
                    });
    }
    
    private function getStudentCategoryBreakdown($siswaId, $currentYear = null)
    {
        $query = PrestasiSiswa::where('id_siswa', $siswaId)
                             ->where('status', 'diterima')
                             ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id');
                             
        if ($currentYear) {
            $query->where('prestasi_siswa.id_tahun_ajaran', $currentYear->id);
        }
        
        return $query->selectRaw('kategori_prestasi.jenis_prestasi, COUNT(*) as total')
                    ->groupBy('kategori_prestasi.jenis_prestasi')
                    ->get()
                    ->mapWithKeys(function($item) {
                        return [$item->jenis_prestasi => $item->total];
                    });
    }
    
    private function getStudentCompetitionLevels($siswaId, $currentYear = null)
    {
        $query = PrestasiSiswa::where('id_siswa', $siswaId)
                             ->where('status', 'diterima')
                             ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id');
                             
        if ($currentYear) {
            $query->where('prestasi_siswa.id_tahun_ajaran', $currentYear->id);
        }
        
        return $query->selectRaw('kategori_prestasi.tingkat_kompetisi, COUNT(*) as total')
                    ->whereNotNull('kategori_prestasi.tingkat_kompetisi')
                    ->groupBy('kategori_prestasi.tingkat_kompetisi')
                    ->get()
                    ->mapWithKeys(function($item) {
                        return [$item->tingkat_kompetisi => $item->total];
                    });
    }
    
    private function getExtracurricularAchievements($siswaId, $currentYear = null)
    {
        $query = PrestasiSiswa::where('id_siswa', $siswaId)
                             ->where('status', 'diterima')
                             ->whereNotNull('id_ekskul')
                             ->with('ekstrakurikuler');
                             
        if ($currentYear) {
            $query->where('id_tahun_ajaran', $currentYear->id);
        }
        
        $achievements = $query->get();
        
        $ekstrakuriculars = SiswaEkskul::where('id_siswa', $siswaId)->with('ekstrakurikuler')->get();
        
        return [
            'total_extracurricular' => $ekstrakuriculars->count(),
            'achievements_from_extracurricular' => $achievements->count(),
            'correlation_rate' => $ekstrakuriculars->count() > 0 ? round(($achievements->count() / $ekstrakuriculars->count()) * 100, 1) : 0,
            'breakdown' => $achievements->groupBy('ekstrakurikuler.nama')->map->count()
        ];
    }
    
    private function getStudentClassRanking($siswa, $currentYear = null)
    {
        $classmates = Siswa::where('id_kelas', $siswa->id_kelas)
                          ->withCount(['prestasi as total_prestasi' => function($query) use ($currentYear) {
                              $query->where('status', 'diterima');
                              if ($currentYear) {
                                  $query->where('id_tahun_ajaran', $currentYear->id);
                              }
                          }])
                          ->orderByDesc('total_prestasi')
                          ->get();
        
        $studentRank = $classmates->search(function($classmate) use ($siswa) {
            return $classmate->id === $siswa->id;
        }) + 1;
        
        return [
            'rank' => $studentRank,
            'total_students' => $classmates->count(),
            'student_achievements' => $classmates->where('id', $siswa->id)->first()->total_prestasi ?? 0,
            'class_average' => round($classmates->avg('total_prestasi'), 1),
            'top_performer' => $classmates->first()
        ];
    }
    
    private function getStudentYearlyProgression($siswaId)
    {
        return PrestasiSiswa::where('id_siswa', $siswaId)
                          ->where('status', 'diterima')
                          ->join('tahun_ajaran', 'prestasi_siswa.id_tahun_ajaran', '=', 'tahun_ajaran.id')
                          ->selectRaw('tahun_ajaran.nama_tahun_ajaran, COUNT(*) as total')
                          ->groupBy('tahun_ajaran.nama_tahun_ajaran')
                          ->orderBy('tahun_ajaran.nama_tahun_ajaran')
                          ->get();
    }
    
    private function getStudentAchievementGaps($siswaId, $currentYear = null)
    {
        $allCategories = KategoriPrestasi::where('is_active', true)->get();
        
        $query = PrestasiSiswa::where('id_siswa', $siswaId)
                             ->where('status', 'diterima');
                             
        if ($currentYear) {
            $query->where('id_tahun_ajaran', $currentYear->id);
        }
        
        $achievedCategories = $query->pluck('id_kategori_prestasi')->unique();
        
        $gaps = $allCategories->whereNotIn('id', $achievedCategories);
        
        return [
            'total_categories' => $allCategories->count(),
            'achieved_categories' => $achievedCategories->count(),
            'gap_categories' => $gaps->pluck('nama_kategori'),
            'completion_rate' => $allCategories->count() > 0 ? round(($achievedCategories->count() / $allCategories->count()) * 100, 1) : 0
        ];
    }
    
    private function getStudentRecommendations($siswaId, $currentYear = null)
    {
        $analysis = $this->getStudentAchievementSummary($siswaId, $currentYear);
        $categories = $this->getStudentCategoryBreakdown($siswaId, $currentYear);
        $gaps = $this->getStudentAchievementGaps($siswaId, $currentYear);
        
        $recommendations = [];
        
        if ($analysis['acceptance_rate'] < 70) {
            $recommendations[] = 'Perlu bimbingan dalam dokumentasi prestasi untuk meningkatkan tingkat penerimaan';
        }
        
        if ($categories['akademik'] ?? 0 < $categories['non_akademik'] ?? 0) {
            $recommendations[] = 'Dorong partisipasi lebih aktif dalam kompetisi akademik';
        } elseif (($categories['non_akademik'] ?? 0) < ($categories['akademik'] ?? 0)) {
            $recommendations[] = 'Seimbangkan dengan prestasi non-akademik melalui ekstrakurikuler';
        }
        
        if ($gaps['completion_rate'] < 50) {
            $recommendations[] = 'Eksplorasi kategori prestasi baru: ' . implode(', ', $gaps['gap_categories']->take(3)->toArray());
        }
        
        return $recommendations;
    }
    
    private function getClassOverview($kelas, $currentYear = null)
    {
        $overview = [];
        
        foreach ($kelas as $kelasItem) {
            $siswaIds = Siswa::where('id_kelas', $kelasItem->id)->pluck('id');
            
            $query = PrestasiSiswa::whereIn('id_siswa', $siswaIds);
            if ($currentYear) {
                $query->where('id_tahun_ajaran', $currentYear->id);
            }
            
            $prestasi = $query->get();
            
            $overview[] = [
                'kelas' => $kelasItem->nama_kelas,
                'total_siswa' => $siswaIds->count(),
                'total_prestasi' => $prestasi->where('status', 'diterima')->count(),
                'pending_validasi' => $prestasi->where('status', 'menunggu_validasi')->count(),
                'average_per_student' => $siswaIds->count() > 0 ? round($prestasi->where('status', 'diterima')->count() / $siswaIds->count(), 2) : 0
            ];
        }
        
        return $overview;
    }
    
    private function getClassPerformanceMetrics($kelas, $currentYear = null)
    {
        $metrics = [
            'total_students' => 0,
            'total_achievements' => 0,
            'average_per_student' => 0,
            'top_performing_class' => null,
            'improvement_needed' => []
        ];
        
        $classPerformance = [];
        
        foreach ($kelas as $kelasItem) {
            $siswaIds = Siswa::where('id_kelas', $kelasItem->id)->pluck('id');
            
            $query = PrestasiSiswa::whereIn('id_siswa', $siswaIds)->where('status', 'diterima');
            if ($currentYear) {
                $query->where('id_tahun_ajaran', $currentYear->id);
            }
            
            $achievements = $query->count();
            $avgPerStudent = $siswaIds->count() > 0 ? round($achievements / $siswaIds->count(), 2) : 0;
            
            $classPerformance[] = [
                'nama_kelas' => $kelasItem->nama_kelas,
                'total_siswa' => $siswaIds->count(),
                'total_prestasi' => $achievements,
                'average_per_student' => $avgPerStudent
            ];
            
            $metrics['total_students'] += $siswaIds->count();
            $metrics['total_achievements'] += $achievements;
        }
        
        $metrics['average_per_student'] = $metrics['total_students'] > 0 ? 
            round($metrics['total_achievements'] / $metrics['total_students'], 2) : 0;
        
        $topClass = collect($classPerformance)->sortByDesc('average_per_student')->first();
        $metrics['top_performing_class'] = $topClass;
        
        $lowPerformers = collect($classPerformance)->where('average_per_student', '<', $metrics['average_per_student']);
        $metrics['improvement_needed'] = $lowPerformers->pluck('nama_kelas')->toArray();
        
        return $metrics;
    }
    
    private function getClassAchievementDistribution($kelas, $currentYear = null)
    {
        $allSiswaIds = [];
        foreach ($kelas as $kelasItem) {
            $siswaIds = Siswa::where('id_kelas', $kelasItem->id)->pluck('id');
            $allSiswaIds = array_merge($allSiswaIds, $siswaIds->toArray());
        }
        
        $query = PrestasiSiswa::whereIn('id_siswa', $allSiswaIds)
                             ->where('status', 'diterima')
                             ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id');
                             
        if ($currentYear) {
            $query->where('prestasi_siswa.id_tahun_ajaran', $currentYear->id);
        }
        
        return [
            'by_type' => $query->selectRaw('kategori_prestasi.jenis_prestasi, COUNT(*) as total')
                              ->groupBy('kategori_prestasi.jenis_prestasi')
                              ->get(),
            'by_level' => $query->selectRaw('kategori_prestasi.tingkat_kompetisi, COUNT(*) as total')
                               ->whereNotNull('kategori_prestasi.tingkat_kompetisi')
                               ->groupBy('kategori_prestasi.tingkat_kompetisi')
                               ->get()
        ];
    }
    
    private function getClassTopPerformers($kelas, $currentYear = null, $limit = 10)
    {
        $allSiswaIds = [];
        foreach ($kelas as $kelasItem) {
            $siswaIds = Siswa::where('id_kelas', $kelasItem->id)->pluck('id');
            $allSiswaIds = array_merge($allSiswaIds, $siswaIds->toArray());
        }
        
        $query = function($q) use ($currentYear) {
            $q->where('status', 'diterima');
            if ($currentYear) {
                $q->where('id_tahun_ajaran', $currentYear->id);
            }
        };
        
        return Siswa::whereIn('id', $allSiswaIds)
                   ->withCount(['prestasi as total_prestasi' => $query])
                   ->with('kelas')
                   ->orderByDesc('total_prestasi')
                   ->limit($limit)
                   ->get();
    }
    
    private function getClassUnderperformers($kelas, $currentYear = null)
    {
        $allSiswaIds = [];
        foreach ($kelas as $kelasItem) {
            $siswaIds = Siswa::where('id_kelas', $kelasItem->id)->pluck('id');
            $allSiswaIds = array_merge($allSiswaIds, $siswaIds->toArray());
        }
        
        $query = function($q) use ($currentYear) {
            $q->where('status', 'diterima');
            if ($currentYear) {
                $q->where('id_tahun_ajaran', $currentYear->id);
            }
        };
        
        return Siswa::whereIn('id', $allSiswaIds)
                   ->withCount(['prestasi as total_prestasi' => $query])
                   ->with('kelas')
                   ->having('total_prestasi', '=', 0)
                   ->orderBy('nama')
                   ->get();
    }
    
    private function getClassMonthlyTrends($kelas, $currentYear = null)
    {
        $allSiswaIds = [];
        foreach ($kelas as $kelasItem) {
            $siswaIds = Siswa::where('id_kelas', $kelasItem->id)->pluck('id');
            $allSiswaIds = array_merge($allSiswaIds, $siswaIds->toArray());
        }
        
        $query = PrestasiSiswa::whereIn('id_siswa', $allSiswaIds)
                             ->where('status', 'diterima');
                             
        if ($currentYear) {
            $query->where('id_tahun_ajaran', $currentYear->id);
        }
        
        return $query->selectRaw('DATE_FORMAT(tanggal_prestasi, "%Y-%m") as bulan, COUNT(*) as total')
                    ->where('tanggal_prestasi', '>=', now()->subMonths(12))
                    ->groupBy('bulan')
                    ->orderBy('bulan')
                    ->get();
    }
    
    private function getClassComparison($kelas, $currentYear = null)
    {
        $comparison = [];
        
        foreach ($kelas as $kelasItem) {
            $siswaIds = Siswa::where('id_kelas', $kelasItem->id)->pluck('id');
            
            $query = PrestasiSiswa::whereIn('id_siswa', $siswaIds)->where('status', 'diterima');
            if ($currentYear) {
                $query->where('id_tahun_ajaran', $currentYear->id);
            }
            
            $achievements = $query->count();
            $avgPerStudent = $siswaIds->count() > 0 ? round($achievements / $siswaIds->count(), 2) : 0;
            
            $comparison[] = [
                'nama_kelas' => $kelasItem->nama_kelas,
                'total_siswa' => $siswaIds->count(),
                'total_prestasi' => $achievements,
                'average_per_student' => $avgPerStudent,
                'rank' => 0 // Will be calculated after sorting
            ];
        }
        
        $comparison = collect($comparison)->sortByDesc('average_per_student')->values();
        
        return $comparison->map(function($item, $index) {
            $item['rank'] = $index + 1;
            return $item;
        });
    }
    
    private function getClassImprovementAreas($kelas, $currentYear = null)
    {
        $areas = [];
        
        $allSiswaIds = [];
        foreach ($kelas as $kelasItem) {
            $siswaIds = Siswa::where('id_kelas', $kelasItem->id)->pluck('id');
            $allSiswaIds = array_merge($allSiswaIds, $siswaIds->toArray());
        }
        
        $noAchievements = Siswa::whereIn('id', $allSiswaIds)
                             ->whereDoesntHave('prestasi', function($query) use ($currentYear) {
                                 $query->where('status', 'diterima');
                                 if ($currentYear) {
                                     $query->where('id_tahun_ajaran', $currentYear->id);
                                 }
                             })
                             ->count();
        
        if ($noAchievements > 0) {
            $areas[] = "Terdapat {$noAchievements} siswa yang belum memiliki prestasi";
        }
        
        $pendingValidation = PrestasiSiswa::whereIn('id_siswa', $allSiswaIds)
                                        ->where('status', 'menunggu_validasi')
                                        ->count();
        
        if ($pendingValidation > 5) {
            $areas[] = "Terdapat {$pendingValidation} prestasi menunggu validasi";
        }
        
        return $areas;
    }
    
    private function getAcademicProgression($siswaId)
    {
        return PrestasiSiswa::where('id_siswa', $siswaId)
                          ->where('status', 'diterima')
                          ->join('tahun_ajaran', 'prestasi_siswa.id_tahun_ajaran', '=', 'tahun_ajaran.id')
                          ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
                          ->where('kategori_prestasi.jenis_prestasi', 'akademik')
                          ->selectRaw('tahun_ajaran.nama_tahun_ajaran, COUNT(*) as total')
                          ->groupBy('tahun_ajaran.nama_tahun_ajaran')
                          ->orderBy('tahun_ajaran.nama_tahun_ajaran')
                          ->get();
    }
    
    private function getAchievementGrowth($siswaId)
    {
        return PrestasiSiswa::where('id_siswa', $siswaId)
                          ->where('status', 'diterima')
                          ->selectRaw('YEAR(tanggal_prestasi) as tahun, COUNT(*) as total')
                          ->groupBy('tahun')
                          ->orderBy('tahun')
                          ->get();
    }
    
    private function getSkillDevelopment($siswaId)
    {
        return PrestasiSiswa::where('id_siswa', $siswaId)
                          ->where('status', 'diterima')
                          ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
                          ->selectRaw('kategori_prestasi.bidang_prestasi, COUNT(*) as total')
                          ->whereNotNull('kategori_prestasi.bidang_prestasi')
                          ->groupBy('kategori_prestasi.bidang_prestasi')
                          ->get();
    }
    
    private function getExtracurricularProgression($siswaId)
    {
        return SiswaEkskul::where('id_siswa', $siswaId)
                         ->with('ekstrakurikuler')
                         ->orderBy('tahun_ajaran')
                         ->get()
                         ->groupBy('tahun_ajaran');
    }
    
    private function getBehavioralTrends($siswaId)
    {
        return PrestasiSiswa::where('id_siswa', $siswaId)
                          ->selectRaw('status, COUNT(*) as total')
                          ->groupBy('status')
                          ->get();
    }
    
    private function getGoalTracking($siswaId)
    {
        return [];
    }
    
    private function getMilestoneAchievements($siswaId)
    {
        $milestones = [];
        
        $firstAchievement = PrestasiSiswa::where('id_siswa', $siswaId)
                                       ->where('status', 'diterima')
                                       ->orderBy('tanggal_prestasi')
                                       ->first();
        
        if ($firstAchievement) {
            $milestones[] = [
                'type' => 'first_achievement',
                'date' => $firstAchievement->tanggal_prestasi,
                'description' => 'Prestasi pertama: ' . $firstAchievement->nama_prestasi
            ];
        }
        
        $totalAchievements = PrestasiSiswa::where('id_siswa', $siswaId)
                                        ->where('status', 'diterima')
                                        ->count();
        
        $milestoneNumbers = [5, 10, 15, 20, 25];
        foreach ($milestoneNumbers as $number) {
            if ($totalAchievements >= $number) {
                $milestones[] = [
                    'type' => 'achievement_count',
                    'number' => $number,
                    'description' => "Mencapai {$number} prestasi"
                ];
            }
        }
        
        return $milestones;
    }
    
    private function getFuturePredictions($siswaId)
    {
        $recentTrend = PrestasiSiswa::where('id_siswa', $siswaId)
                                  ->where('status', 'diterima')
                                  ->where('tanggal_prestasi', '>=', now()->subMonths(6))
                                  ->count();
        
        $predictions = [];
        
        if ($recentTrend >= 3) {
            $predictions[] = 'Berpotensi mencapai 10+ prestasi dalam tahun ajaran ini';
        } elseif ($recentTrend >= 1) {
            $predictions[] = 'Konsistensi baik, diperkirakan 5-8 prestasi per tahun';
        } else {
            $predictions[] = 'Perlu motivasi tambahan untuk meningkatkan prestasi';
        }
        
        return $predictions;
    }
}
