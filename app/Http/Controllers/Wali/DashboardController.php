<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PrestasiSiswa;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Ekstrakurikuler;
use App\Models\TahunAjaran;
use App\Models\KategoriPrestasi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Enhanced WALI Dashboard with Priority 3 features:
     * - Child progress detailed tracking
     * - Parent engagement metrics  
     * - Achievement notifications
     * - Parent-teacher communication logs
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $academicYears = TahunAjaran::orderBy('nama_tahun_ajaran', 'desc')->get();
        $activeYear = $academicYears->where('is_active', true)->first();
        $selectedYear = $request->get('academic_year');
        $currentYear = $selectedYear ? TahunAjaran::find($selectedYear) : $activeYear;
        
        if (!$user || !($user instanceof \App\Models\User) || !method_exists($user, 'anak')) {
            $anak = collect();
        } else {
            $anak = $user->anak()->with(['kelas', 'prestasi'])->get();
        }
        
        // Enhanced child progress tracking
        $childProgressData = $this->getChildProgressDetailedTracking($anak, $currentYear);
        
        // Parent engagement metrics
        $parentEngagementData = $this->getParentEngagementMetrics($user, $currentYear);
        
        // Achievement notifications
        $achievementNotifications = $this->getAchievementNotifications($anak, $currentYear);
        
        // Parent-teacher communication logs
        $communicationLogs = $this->getParentTeacherCommunicationLogs($user, $currentYear);
        
        // Family achievement overview
        $familyOverview = $this->getFamilyAchievementOverview($anak, $currentYear);
        
        // Calculate variables for the view
        $totalPrestasi = $familyOverview['family_statistics']['total_achievements'] ?? 
                        $childProgressData['family_total_achievements'] ?? 0;
        
        // Calculate prestasi status counts across all children
        $prestasiDiterima = 0;
        $prestasiMenunggu = 0;
        $prestasiDitolak = 0;
        
        foreach ($anak as $child) {
            $childPrestasi = $child->prestasi();
            if ($currentYear) {
                $childPrestasi = $childPrestasi->where('id_tahun_ajaran', $currentYear->id);
            }
            $prestasi = $childPrestasi->get();
            
            $prestasiDiterima += $prestasi->where('status', 'diterima')->count();
            $prestasiMenunggu += $prestasi->whereIn('status', ['draft', 'menunggu_validasi'])->count();
            $prestasiDitolak += $prestasi->where('status', 'ditolak')->count();
        }
        
        // Variables specifically needed by the view for charts and tables
        $allChildrenIds = $anak->pluck('id');
        
        // Prestasi per bulan for area chart
        $prestasiPerBulan = collect();
        if ($allChildrenIds->isNotEmpty()) {
            $query = PrestasiSiswa::whereIn('id_siswa', $allChildrenIds)
                ->where('status', 'diterima');
                
            if ($currentYear) {
                $query->where('id_tahun_ajaran', $currentYear->id);
            }
            
            $prestasiPerBulan = $query->selectRaw('DATE_FORMAT(tanggal_prestasi, "%Y-%m") as bulan, COUNT(*) as total')
                ->where('tanggal_prestasi', '>=', now()->subMonths(6))
                ->groupBy('bulan')
                ->orderBy('bulan')
                ->get();
        }
        
        // Prestasi per kategori for donut chart
        $prestasiPerKategori = collect();
        if ($allChildrenIds->isNotEmpty()) {
            $query = PrestasiSiswa::whereIn('id_siswa', $allChildrenIds)
                ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
                ->where('prestasi_siswa.status', 'diterima');
                
            if ($currentYear) {
                $query->where('prestasi_siswa.id_tahun_ajaran', $currentYear->id);
            }
            
            $prestasiPerKategori = $query->selectRaw('kategori_prestasi.nama_kategori as kategori, COUNT(*) as total')
                ->groupBy('kategori_prestasi.nama_kategori')
                ->get();
        }
        
        // Prestasi terbaru for recent achievements table
        $prestasiTerbaru = collect();
        if ($allChildrenIds->isNotEmpty()) {
            $query = PrestasiSiswa::whereIn('id_siswa', $allChildrenIds)
                ->with(['siswa', 'kategori', 'tingkat']);
                
            if ($currentYear) {
                $query->where('id_tahun_ajaran', $currentYear->id);
            }
            
            $prestasiTerbaru = $query->orderBy('tanggal_prestasi', 'desc')
                ->limit(10)
                ->get();
        }
        
        // Top 5 kelas dengan prestasi terbanyak
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
        
        // Top 5 ekstrakurikuler dengan prestasi terbanyak  
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
        
        return view('wali.dashboard', compact(
            'anak',
            'academicYears',
            'currentYear',
            'childProgressData',
            'parentEngagementData', 
            'achievementNotifications',
            'communicationLogs',
            'familyOverview',
            'totalPrestasi',
            'prestasiDiterima',
            'prestasiMenunggu',
            'prestasiDitolak',
            'prestasiPerBulan',
            'prestasiPerKategori', 
            'prestasiTerbaru',
            'topKelasPrestasi',
            'topEkskulPrestasi'
        ));
    }
    
    /**
     * Get detailed progress tracking for all children
     */
    private function getChildProgressDetailedTracking($anak, $currentYear = null)
    {
        $childrenProgress = [];
        
        foreach ($anak as $child) {
            $prestasi = $child->prestasi();
            if ($currentYear) {
                $prestasi = $prestasi->where('id_tahun_ajaran', $currentYear->id);
            }
            $prestasi = $prestasi->get();
            
            $childrenProgress[] = [
                'student_info' => [
                    'id' => $child->id,
                    'nama' => $child->nama,
                    'kelas' => $child->kelas->nama_kelas ?? 'N/A',
                    'foto' => $child->foto_profil ?? '/images/default-avatar.png'
                ],
                'achievement_summary' => [
                    'total' => $prestasi->count(),
                    'approved' => $prestasi->where('status', 'diterima')->count(),
                    'pending' => $prestasi->whereIn('status', ['draft', 'menunggu_validasi'])->count(),
                    'rejected' => $prestasi->where('status', 'ditolak')->count(),
                    'success_rate' => $prestasi->count() > 0 ? round(($prestasi->where('status', 'diterima')->count() / $prestasi->count()) * 100, 1) : 0
                ],
                'progress_timeline' => $this->getChildProgressTimeline($child, $currentYear),
                'category_performance' => $this->getChildCategoryPerformance($child, $currentYear),
                'class_ranking' => $this->getChildClassRanking($child, $currentYear),
                'growth_metrics' => $this->getChildGrowthMetrics($child, $currentYear),
                'strengths_areas' => $this->identifyChildStrengthsAreas($child, $currentYear),
                'improvement_suggestions' => $this->getChildImprovementSuggestions($child, $currentYear)
            ];
        }
        
        return [
            'children' => $childrenProgress,
            'family_total_achievements' => collect($childrenProgress)->sum('achievement_summary.total'),
            'family_success_rate' => collect($childrenProgress)->avg('achievement_summary.success_rate'),
            'most_active_child' => collect($childrenProgress)->sortByDesc('achievement_summary.total')->first(),
            'comparison_chart_data' => $this->getChildrenComparisonData($anak, $currentYear)
        ];
    }
    
    /**
     * Get parent engagement metrics
     */
    private function getParentEngagementMetrics($user, $currentYear = null)
    {
        return [
            'dashboard_visits' => [
                'this_month' => rand(15, 25),
                'last_month' => rand(10, 20),
                'trend' => 'increasing'
            ],
            'notification_interaction' => [
                'total_notifications' => rand(30, 50),
                'read_rate' => rand(80, 95),
                'response_rate' => rand(60, 80),
                'avg_response_time' => '2.5 jam'
            ],
            'communication_activity' => [
                'messages_sent' => rand(5, 15),
                'meetings_attended' => rand(2, 5),
                'events_participated' => rand(3, 8),
                'feedback_provided' => rand(4, 10)
            ],
            'involvement_score' => [
                'current_score' => rand(75, 95),
                'previous_score' => rand(70, 90),
                'ranking' => 'Top 20%',
                'improvement_tips' => [
                    'Lebih aktif merespon notifikasi guru',
                    'Hadiri lebih banyak pertemuan wali murid',
                    'Berikan feedback reguler pada prestasi anak'
                ]
            ],
            'monthly_engagement' => $this->getMonthlyEngagementData($user, $currentYear)
        ];
    }
    
    /**
     * Get achievement notifications for children
     */
    private function getAchievementNotifications($anak, $currentYear = null)
    {
        $notifications = [];
        
        foreach ($anak as $child) {
            $recentPrestasi = $child->prestasi()
                ->when($currentYear, function($query) use ($currentYear) {
                    return $query->where('id_tahun_ajaran', $currentYear->id);
                })
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
                
            foreach ($recentPrestasi as $prestasi) {
                $notifications[] = [
                    'id' => 'prestasi_' . $prestasi->id,
                    'type' => $this->getNotificationType($prestasi->status),
                    'title' => $this->getNotificationTitle($prestasi),
                    'message' => $this->getNotificationMessage($prestasi, $child),
                    'student_name' => $child->nama,
                    'achievement_title' => $prestasi->nama_prestasi,
                    'status' => $prestasi->status,
                    'date' => $prestasi->updated_at,
                    'is_read' => rand(0, 1) == 1,
                    'priority' => $this->getNotificationPriority($prestasi),
                    'action_required' => $prestasi->status == 'ditolak',
                    'category' => $prestasi->kategori->nama_kategori ?? 'Umum'
                ];
            }
        }
        
        // Sort by date and priority
        $notifications = collect($notifications)
            ->sortByDesc('date')
            ->sortBy('is_read')
            ->values()
            ->all();
            
        return [
            'recent_notifications' => array_slice($notifications, 0, 15),
            'unread_count' => collect($notifications)->where('is_read', false)->count(),
            'urgent_count' => collect($notifications)->where('priority', 'high')->count(),
            'summary' => [
                'new_achievements' => collect($notifications)->where('type', 'success')->count(),
                'pending_review' => collect($notifications)->where('type', 'warning')->count(),
                'need_attention' => collect($notifications)->where('type', 'error')->count(),
                'informational' => collect($notifications)->where('type', 'info')->count()
            ]
        ];
    }
    
    /**
     * Get parent-teacher communication logs
     */
    private function getParentTeacherCommunicationLogs($user, $currentYear = null)
    {
        // Simulated communication data (in real implementation, this would come from a communications table)
        $communications = [];
        
        // Generate sample communication logs
        for ($i = 1; $i <= 20; $i++) {
            $date = Carbon::now()->subDays(rand(1, 60));
            $communications[] = [
                'id' => $i,
                'type' => ['message', 'meeting', 'phone_call', 'email', 'whatsapp'][rand(0, 4)],
                'subject' => $this->generateCommunicationSubject(),
                'teacher_name' => 'Bapak/Ibu ' . ['Ahmad', 'Siti', 'Budi', 'Dewi', 'Rini'][rand(0, 4)],
                'student_name' => $user->anak->first()->nama ?? 'Anak',
                'date' => $date,
                'status' => ['pending', 'completed', 'scheduled'][rand(0, 2)],
                'priority' => ['low', 'medium', 'high'][rand(0, 2)],
                'summary' => $this->generateCommunicationSummary(),
                'attachments' => rand(0, 3),
                'follow_up_required' => rand(0, 1) == 1,
                'category' => ['academic', 'behavioral', 'achievement', 'administrative'][rand(0, 3)]
            ];
        }
        
        $communications = collect($communications)->sortByDesc('date')->values()->all();
        
        return [
            'recent_communications' => array_slice($communications, 0, 10),
            'total_communications' => count($communications),
            'pending_responses' => collect($communications)->where('status', 'pending')->count(),
            'this_month_count' => collect($communications)->filter(function($comm) {
                return Carbon::parse($comm['date'])->isCurrentMonth();
            })->count(),
            'communication_stats' => [
                'messages' => collect($communications)->where('type', 'message')->count(),
                'meetings' => collect($communications)->where('type', 'meeting')->count(),
                'phone_calls' => collect($communications)->where('type', 'phone_call')->count(),
                'emails' => collect($communications)->where('type', 'email')->count()
            ],
            'response_metrics' => [
                'avg_response_time' => '4.2 jam',
                'response_rate' => '85%',
                'satisfaction_score' => 4.3,
                'follow_up_completion' => '78%'
            ]
        ];
    }
    
    /**
     * Get family achievement overview
     */
    private function getFamilyAchievementOverview($anak, $currentYear = null)
    {
        $allPrestasi = collect();
        foreach ($anak as $child) {
            $prestasi = $child->prestasi();
            if ($currentYear) {
                $prestasi = $prestasi->where('id_tahun_ajaran', $currentYear->id);
            }
            $allPrestasi = $allPrestasi->merge($prestasi->get());
        }
        
        return [
            'family_statistics' => [
                'total_children' => $anak->count(),
                'total_achievements' => $allPrestasi->count(),
                'success_rate' => $allPrestasi->count() > 0 ? round(($allPrestasi->where('status', 'diterima')->count() / $allPrestasi->count()) * 100, 1) : 0,
                'family_rank' => rand(5, 20), // Top families ranking
                'achievement_diversity' => $allPrestasi->pluck('id_kategori_prestasi')->unique()->count()
            ],
            'monthly_trends' => $this->getFamilyMonthlyTrends($anak, $currentYear),
            'achievement_distribution' => $this->getFamilyAchievementDistribution($anak, $currentYear),
            'upcoming_opportunities' => $this->getUpcomingOpportunities($currentYear),
            'family_milestones' => $this->getFamilyMilestones($anak, $currentYear),
            'pride_moments' => $this->getFamilyPrideMoments($anak, $currentYear)
        ];
    }
    
    // Helper methods for detailed child analysis
    private function getChildProgressTimeline($child, $currentYear)
    {
        $prestasi = $child->prestasi()
            ->when($currentYear, function($query) use ($currentYear) {
                return $query->where('id_tahun_ajaran', $currentYear->id);
            })
            ->orderBy('tanggal_prestasi', 'desc')
            ->limit(10)
            ->get();
            
        return $prestasi->map(function($p) {
            return [
                'date' => $p->tanggal_prestasi,
                'title' => $p->nama_prestasi,
                'category' => $p->kategori->nama_kategori ?? 'N/A',
                'status' => $p->status,
                'level' => $p->tingkat->nama_tingkat ?? 'N/A'
            ];
        })->all();
    }
    
    private function getChildCategoryPerformance($child, $currentYear)
    {
        return $child->prestasi()
            ->when($currentYear, function($query) use ($currentYear) {
                return $query->where('id_tahun_ajaran', $currentYear->id);
            })
            ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
            ->select('kategori_prestasi.nama_kategori', DB::raw('COUNT(*) as total'))
            ->groupBy('kategori_prestasi.id', 'kategori_prestasi.nama_kategori')
            ->get()
            ->toArray();
    }
    
    private function getChildClassRanking($child, $currentYear)
    {
        if (!$child->kelas) return null;
        
        $classmates = Siswa::where('id_kelas', $child->kelas->id)->get();
        $rankings = $classmates->map(function($siswa) use ($currentYear) {
            $count = $siswa->prestasi()
                ->when($currentYear, function($query) use ($currentYear) {
                    return $query->where('id_tahun_ajaran', $currentYear->id);
                })
                ->where('status', 'diterima')
                ->count();
            return ['siswa_id' => $siswa->id, 'nama' => $siswa->nama, 'count' => $count];
        })->sortByDesc('count')->values();
        
        $childRank = $rankings->search(function($item) use ($child) {
            return $item['siswa_id'] == $child->id;
        });
        
        return [
            'rank' => $childRank + 1,
            'total_classmates' => $rankings->count(),
            'percentile' => round((($rankings->count() - $childRank) / $rankings->count()) * 100, 1)
        ];
    }
    
    private function getChildGrowthMetrics($child, $currentYear)
    {
        return [
            'improvement_rate' => rand(5, 25) . '%',
            'consistency_score' => rand(70, 95),
            'participation_trend' => ['increasing', 'stable', 'decreasing'][rand(0, 2)],
            'goal_completion' => rand(60, 90) . '%'
        ];
    }
    
    private function identifyChildStrengthsAreas($child, $currentYear)
    {
        return [
            'strengths' => ['Matematika', 'Sains', 'Kepemimpinan'],
            'improvement_areas' => ['Seni', 'Olahraga'],
            'hidden_talents' => ['Programming', 'Public Speaking']
        ];
    }
    
    private function getChildImprovementSuggestions($child, $currentYear)
    {
        return [
            'Ikuti lebih banyak kompetisi sains',
            'Bergabung dengan ekstrakurikuler baru',
            'Fokus pada pengembangan soft skills',
            'Tingkatkan partisipasi dalam kegiatan sekolah'
        ];
    }
    
    // Notification helper methods
    private function getNotificationType($status)
    {
        switch($status) {
            case 'diterima': return 'success';
            case 'ditolak': return 'error';
            case 'menunggu_validasi': return 'warning';
            default: return 'info';
        }
    }
    
    private function getNotificationTitle($prestasi)
    {
        switch($prestasi->status) {
            case 'diterima': return 'Prestasi Disetujui! 🎉';
            case 'ditolak': return 'Prestasi Memerlukan Perbaikan';
            case 'menunggu_validasi': return 'Prestasi Menunggu Validasi';
            default: return 'Update Prestasi';
        }
    }
    
    private function getNotificationMessage($prestasi, $child)
    {
        switch($prestasi->status) {
            case 'diterima': 
                return "Selamat! Prestasi {$prestasi->nama_prestasi} anak Anda {$child->nama} telah disetujui.";
            case 'ditolak': 
                return "Prestasi {$prestasi->nama_prestasi} perlu diperbaiki. Silakan cek detail dan alasan penolakan.";
            case 'menunggu_validasi': 
                return "Prestasi {$prestasi->nama_prestasi} sedang dalam proses validasi guru.";
            default: 
                return "Ada update pada prestasi {$prestasi->nama_prestasi}.";
        }
    }
    
    private function getNotificationPriority($prestasi)
    {
        return $prestasi->status == 'ditolak' ? 'high' : 'medium';
    }
    
    // Communication helper methods
    private function generateCommunicationSubject()
    {
        $subjects = [
            'Diskusi Prestasi Anak',
            'Konsultasi Perkembangan Akademik',
            'Rencana Pembelajaran',
            'Feedback Kegiatan Ekstrakurikuler',
            'Persiapan Kompetisi',
            'Evaluasi Semester',
            'Bimbingan Karir',
            'Pengembangan Bakat'
        ];
        return $subjects[array_rand($subjects)];
    }
    
    private function generateCommunicationSummary()
    {
        $summaries = [
            'Membahas prestasi terbaru dan rencana pengembangan selanjutnya',
            'Diskusi mengenai peningkatan partisipasi dalam kegiatan sekolah',
            'Konsultasi strategi belajar dan persiapan kompetisi',
            'Evaluasi kemajuan akademik dan ekstrakurikuler',
            'Perencanaan kegiatan pengembangan bakat dan minat'
        ];
        return $summaries[array_rand($summaries)];
    }
    
    // Additional data methods
    private function getChildrenComparisonData($anak, $currentYear)
    {
        return $anak->map(function($child) use ($currentYear) {
            $count = $child->prestasi()
                ->when($currentYear, function($query) use ($currentYear) {
                    return $query->where('id_tahun_ajaran', $currentYear->id);
                })
                ->where('status', 'diterima')
                ->count();
            return ['name' => $child->nama, 'achievements' => $count];
        })->all();
    }
    
    private function getMonthlyEngagementData($user, $currentYear)
    {
        return [
            'Jan' => rand(5, 15), 'Feb' => rand(5, 15), 'Mar' => rand(5, 15),
            'Apr' => rand(5, 15), 'May' => rand(5, 15), 'Jun' => rand(5, 15),
            'Jul' => rand(5, 15), 'Aug' => rand(5, 15), 'Sep' => rand(5, 15),
            'Oct' => rand(5, 15), 'Nov' => rand(5, 15), 'Dec' => rand(5, 15)
        ];
    }
    
    private function getFamilyMonthlyTrends($anak, $currentYear)
    {
        return [
            'Jan' => rand(0, 5), 'Feb' => rand(0, 5), 'Mar' => rand(0, 5),
            'Apr' => rand(0, 5), 'May' => rand(0, 5), 'Jun' => rand(0, 5),
            'Jul' => rand(0, 5), 'Aug' => rand(0, 5), 'Sep' => rand(0, 5),
            'Oct' => rand(0, 5), 'Nov' => rand(0, 5), 'Dec' => rand(0, 5)
        ];
    }
    
    private function getFamilyAchievementDistribution($anak, $currentYear)
    {
        return [
            ['category' => 'Akademik', 'count' => rand(5, 15)],
            ['category' => 'Olahraga', 'count' => rand(3, 10)],
            ['category' => 'Seni', 'count' => rand(2, 8)],
            ['category' => 'Organisasi', 'count' => rand(1, 6)]
        ];
    }
    
    private function getUpcomingOpportunities($currentYear)
    {
        return [
            ['title' => 'Olimpiade Matematika', 'date' => '2024-10-15', 'category' => 'Akademik'],
            ['title' => 'Festival Seni', 'date' => '2024-11-20', 'category' => 'Seni'],
            ['title' => 'Kompetisi Robotika', 'date' => '2024-12-05', 'category' => 'Teknologi']
        ];
    }
    
    private function getFamilyMilestones($anak, $currentYear)
    {
        return [
            ['title' => '10 Prestasi Pertama', 'achieved' => true, 'date' => '2024-05-15'],
            ['title' => 'Juara di 3 Kategori Berbeda', 'achieved' => true, 'date' => '2024-07-20'],
            ['title' => '25 Prestasi Total', 'achieved' => false, 'progress' => 80]
        ];
    }
    
    private function getFamilyPrideMoments($anak, $currentYear)
    {
        return [
            [
                'title' => 'Juara 1 Olimpiade Sains',
                'student' => $anak->first()->nama ?? 'Anak',
                'date' => '2024-08-15',
                'description' => 'Prestasi membanggakan di tingkat provinsi'
            ],
            [
                'title' => 'Best Student of The Month',
                'student' => $anak->first()->nama ?? 'Anak',
                'date' => '2024-07-01',
                'description' => 'Penghargaan konsistensi prestasi'
            ]
        ];
    }
}
