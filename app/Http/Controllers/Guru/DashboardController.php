<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\PrestasiSiswa;
use App\Models\TahunAjaran;
use App\Models\KategoriPrestasi;
use App\Models\SiswaEkskul;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedYear = $request->get('academic_year');
        
        // Get academic years untuk filter
        $academicYears = TahunAjaran::orderBy('nama_tahun_ajaran', 'desc')->get();
        $activeYear = $academicYears->where('is_active', true)->first();
        $currentYear = $selectedYear ? TahunAjaran::find($selectedYear) : $activeYear;
        
        // Kelas yang diampu (untuk tahun yang dipilih atau semua)
        $kelasQuery = Kelas::where('id_wali_kelas', $user->id);
        if ($currentYear) {
            $kelasQuery->where('tahun_ajaran', $currentYear->nama_tahun_ajaran);
        }
        $kelas = $kelasQuery->get();
        $kelasIds = $kelas->pluck('id');
        
        // Siswa di kelas yang diampu
        $siswa = Siswa::whereIn('id_kelas', $kelasIds)->get();
        $siswaCount = $siswa->count();
        
        // Enhanced Analytics
        $analytics = $this->getEnhancedAnalytics($siswa->pluck('id'), $currentYear);
        
        // Class Performance Comparison
        $classComparison = $this->getClassPerformanceComparison($kelas);
        
        // Top Performers dalam kelas
        $topPerformers = $this->getTopPerformers($siswa->pluck('id'), 5);
        
        // Academic vs Non-Academic breakdown
        $typeBreakdown = $this->getAchievementTypeBreakdown($siswa->pluck('id'), $currentYear);
        
        // Monthly trend (enhanced dengan tahun ajaran context)
        $monthlyTrend = $this->getEnhancedMonthlyTrend($siswa->pluck('id'), $currentYear);
        
        // Competition level distribution
        $competitionLevels = $this->getCompetitionLevelDistribution($siswa->pluck('id'), $currentYear);
        
        // Extracurricular participation dan achievements
        $extracurricularStats = $this->getExtracurricularStats($siswa->pluck('id'), $currentYear);
        
        // Extract variables for the view
        $totalPrestasi = $analytics['total_prestasi'] ?? 0;
        $prestasiDiterima = $analytics['diterima'] ?? 0;
        $prestasiPending = $analytics['pending'] ?? 0;
        $prestasiDitolak = $analytics['ditolak'] ?? 0;
        
        // Variables specifically needed by the view
        $prestasiPerBulan = $monthlyTrend; // Monthly trend data for area chart
        
        // Prestasi per kategori for donut chart
        $prestasiPerKategori = collect();
        if ($kelasIds->isNotEmpty()) {
            $query = PrestasiSiswa::whereIn('id_siswa', $siswa->pluck('id'))
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
        if ($siswa->isNotEmpty()) {
            $query = PrestasiSiswa::whereIn('id_siswa', $siswa->pluck('id'))
                ->with(['siswa', 'kategori', 'tingkat']);
                
            if ($currentYear) {
                $query->where('id_tahun_ajaran', $currentYear->id);
            }
            
            $prestasiTerbaru = $query->orderBy('tanggal_prestasi', 'desc')
                ->limit(10)
                ->get();
        }
        
        return view('guru.dashboard', compact(
            'kelas', 'siswa', 'siswaCount', 'academicYears', 'currentYear',
            'analytics', 'classComparison', 'topPerformers', 'typeBreakdown', 
            'monthlyTrend', 'competitionLevels', 'extracurricularStats', 
            'totalPrestasi', 'prestasiDiterima', 'prestasiPending', 'prestasiDitolak',
            'prestasiPerBulan', 'prestasiPerKategori', 'prestasiTerbaru'
        ));
    }
    
    private function getEnhancedAnalytics($siswaIds, $currentYear = null)
    {
        $query = PrestasiSiswa::whereIn('id_siswa', $siswaIds);
        
        if ($currentYear) {
            $query->where('id_tahun_ajaran', $currentYear->id);
        }
        
        $prestasi = $query->get();
        
        return [
            'total_prestasi' => $prestasi->count(),
            'diterima' => $prestasi->where('status', 'diterima')->count(),
            'pending' => $prestasi->where('status', 'menunggu_validasi')->count(),
            'ditolak' => $prestasi->where('status', 'ditolak')->count(),
            'average_per_student' => $siswaIds->count() > 0 ? round($prestasi->count() / $siswaIds->count(), 1) : 0,
            'acceptance_rate' => $prestasi->count() > 0 ? round(($prestasi->where('status', 'diterima')->count() / $prestasi->count()) * 100, 1) : 0
        ];
    }
    
    private function getClassPerformanceComparison($kelas)
    {
        $comparison = [];
        
        foreach ($kelas as $kelasItem) {
            $siswaInClass = Siswa::where('id_kelas', $kelasItem->id)->pluck('id');
            $prestasiCount = PrestasiSiswa::whereIn('id_siswa', $siswaInClass)
                ->where('status', 'diterima')
                ->count();
                
            $comparison[] = [
                'nama_kelas' => $kelasItem->nama_kelas,
                'siswa_count' => $siswaInClass->count(),
                'prestasi_count' => $prestasiCount,
                'average_per_student' => $siswaInClass->count() > 0 ? round($prestasiCount / $siswaInClass->count(), 1) : 0
            ];
        }
        
        return collect($comparison)->sortByDesc('average_per_student')->values();
    }
    
    private function getTopPerformers($siswaIds, $limit = 5)
    {
        return Siswa::whereIn('id', $siswaIds)
            ->withCount(['prestasi as total_prestasi' => function($query) {
                $query->where('status', 'diterima');
            }])
            ->with('kelas')
            ->orderByDesc('total_prestasi')
            ->limit($limit)
            ->get();
    }
    
    private function getAchievementTypeBreakdown($siswaIds, $currentYear = null)
    {
        $query = PrestasiSiswa::whereIn('id_siswa', $siswaIds)
            ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
            ->where('prestasi_siswa.status', 'diterima');
            
        if ($currentYear) {
            $query->where('prestasi_siswa.id_tahun_ajaran', $currentYear->id);
        }
        
        return $query->selectRaw('kategori_prestasi.jenis_prestasi, COUNT(*) as total')
            ->groupBy('kategori_prestasi.jenis_prestasi')
            ->get();
    }
    
    private function getEnhancedMonthlyTrend($siswaIds, $currentYear = null)
    {
        $query = PrestasiSiswa::whereIn('id_siswa', $siswaIds)
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
    
    private function getCompetitionLevelDistribution($siswaIds, $currentYear = null)
    {
        $query = PrestasiSiswa::whereIn('id_siswa', $siswaIds)
            ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
            ->where('prestasi_siswa.status', 'diterima');
            
        if ($currentYear) {
            $query->where('prestasi_siswa.id_tahun_ajaran', $currentYear->id);
        }
        
        return $query->selectRaw('kategori_prestasi.tingkat_kompetisi, COUNT(*) as total')
            ->whereNotNull('kategori_prestasi.tingkat_kompetisi')
            ->groupBy('kategori_prestasi.tingkat_kompetisi')
            ->get();
    }
    
    private function getExtracurricularStats($siswaIds, $currentYear = null)
    {
        // Get students participating in extracurriculars
        $participationQuery = SiswaEkskul::whereIn('id_siswa', $siswaIds);
        
        if ($currentYear) {
            $participationQuery->where('tahun_ajaran', $currentYear->nama_tahun_ajaran);
        }
        
        $participation = $participationQuery->with('ekskul')->get();
        
        // Get achievements dari extracurricular activities
        $achievementQuery = PrestasiSiswa::whereIn('id_siswa', $siswaIds)
            ->whereNotNull('id_ekskul')
            ->where('status', 'diterima');
            
        if ($currentYear) {
            $achievementQuery->where('id_tahun_ajaran', $currentYear->id);
        }
        
        $achievements = $achievementQuery->count();
        
        return [
            'total_participation' => $participation->count(),
            'unique_activities' => $participation->pluck('id_ekskul')->unique()->count(),
            'ekstrakurikuler_achievements' => $achievements,
            'participation_rate' => $siswaIds->count() > 0 ? round(($participation->pluck('id_siswa')->unique()->count() / $siswaIds->count()) * 100, 1) : 0
        ];
    }
}
