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
use PDF;

class ReportController extends Controller
{
    public function index()
    {
        $tahunAjarans = TahunAjaran::orderBy('nama_tahun_ajaran', 'desc')->get();
        $categories = KategoriPrestasi::active()->get();
        $classes = Kelas::orderBy('nama_kelas')->get();
        
        return view('admin.reports.index', compact('tahunAjarans', 'categories', 'classes'));
    }

    public function generateStudentReport(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'tahun_ajaran_id' => 'nullable|exists:tahun_ajaran,id',
            'format' => 'required|in:pdf,excel,json'
        ]);

        try {
            $siswa = Siswa::with(['kelas', 'prestasi.kategoriPrestasi', 'prestasi.tingkatPenghargaan', 'prestasi.tahunAjaran'])
                ->findOrFail($request->siswa_id);

            $query = $siswa->prestasi()->with(['kategoriPrestasi', 'tingkatPenghargaan', 'tahunAjaran']);
            
            if ($request->tahun_ajaran_id) {
                $query->where('id_tahun_ajaran', $request->tahun_ajaran_id);
            }
            
            $prestasi = $query->where('status', 'diterima')
                ->orderBy('tanggal_prestasi', 'desc')
                ->get();

            // Calculate statistics
            $stats = [
                'total_prestasi' => $prestasi->count(),
                'prestasi_akademik' => $prestasi->where('kategoriPrestasi.jenis_prestasi', 'akademik')->count(),
                'prestasi_non_akademik' => $prestasi->where('kategoriPrestasi.jenis_prestasi', 'non_akademik')->count(),
                'tingkat_sekolah' => $prestasi->where('kategoriPrestasi.tingkat_kompetisi', 'sekolah')->count(),
                'tingkat_kabupaten' => $prestasi->where('kategoriPrestasi.tingkat_kompetisi', 'kabupaten')->count(),
                'tingkat_provinsi' => $prestasi->where('kategoriPrestasi.tingkat_kompetisi', 'provinsi')->count(),
                'tingkat_nasional' => $prestasi->where('kategoriPrestasi.tingkat_kompetisi', 'nasional')->count(),
                'tingkat_internasional' => $prestasi->where('kategoriPrestasi.tingkat_kompetisi', 'internasional')->count(),
            ];

            $data = [
                'siswa' => $siswa,
                'prestasi' => $prestasi,
                'stats' => $stats,
                'periode' => $request->tahun_ajaran_id ? TahunAjaran::find($request->tahun_ajaran_id) : null,
                'generated_at' => now()
            ];

            switch ($request->format) {
                case 'pdf':
                    return $this->generateStudentPDF($data);
                case 'excel':
                    return $this->generateStudentExcel($data);
                case 'json':
                    return response()->json($data);
                default:
                    return response()->json($data);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function generateClassReport(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'tahun_ajaran_id' => 'nullable|exists:tahun_ajaran,id',
            'format' => 'required|in:pdf,excel,json'
        ]);

        try {
            $kelas = Kelas::with(['siswa.prestasi.kategoriPrestasi', 'siswa.prestasi.tingkatPenghargaan'])
                ->findOrFail($request->kelas_id);

            $tahunAjaran = $request->tahun_ajaran_id ? TahunAjaran::find($request->tahun_ajaran_id) : null;

            $siswaData = $kelas->siswa->map(function($siswa) use ($request) {
                $query = $siswa->prestasi()->with(['kategoriPrestasi', 'tingkatPenghargaan']);
                
                if ($request->tahun_ajaran_id) {
                    $query->where('id_tahun_ajaran', $request->tahun_ajaran_id);
                }
                
                $prestasi = $query->where('status', 'diterima')->get();
                
                return [
                    'siswa' => $siswa,
                    'total_prestasi' => $prestasi->count(),
                    'prestasi_akademik' => $prestasi->where('kategoriPrestasi.jenis_prestasi', 'akademik')->count(),
                    'prestasi_non_akademik' => $prestasi->where('kategoriPrestasi.jenis_prestasi', 'non_akademik')->count(),
                    'prestasi' => $prestasi
                ];
            });

            $classStats = [
                'total_siswa' => $kelas->siswa->count(),
                'total_prestasi' => $siswaData->sum('total_prestasi'),
                'avg_prestasi_per_siswa' => $kelas->siswa->count() > 0 ? 
                    round($siswaData->sum('total_prestasi') / $kelas->siswa->count(), 2) : 0,
                'siswa_berprestasi' => $siswaData->where('total_prestasi', '>', 0)->count()
            ];

            $data = [
                'kelas' => $kelas,
                'siswa_data' => $siswaData,
                'class_stats' => $classStats,
                'periode' => $tahunAjaran,
                'generated_at' => now()
            ];

            switch ($request->format) {
                case 'pdf':
                    return $this->generateClassPDF($data);
                case 'excel':
                    return $this->generateClassExcel($data);
                case 'json':
                    return response()->json($data);
                default:
                    return response()->json($data);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function generateSchoolReport(Request $request)
    {
        $request->validate([
            'tahun_ajaran_id' => 'nullable|exists:tahun_ajaran,id',
            'format' => 'required|in:pdf,excel,json'
        ]);

        try {
            $tahunAjaran = $request->tahun_ajaran_id ? TahunAjaran::find($request->tahun_ajaran_id) : null;
            
            // Overall statistics
            $query = PrestasiSiswa::where('status', 'diterima');
            if ($request->tahun_ajaran_id) {
                $query->where('id_tahun_ajaran', $request->tahun_ajaran_id);
            }

            $totalPrestasi = $query->count();
            
            // Achievement by category
            $prestasiByCategory = PrestasiSiswa::select(
                    'kategori_prestasi.nama_kategori',
                    'kategori_prestasi.jenis_prestasi',
                    'kategori_prestasi.bidang_prestasi',
                    'kategori_prestasi.tingkat_kompetisi',
                    DB::raw('COUNT(*) as total')
                )
                ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
                ->where('prestasi_siswa.status', 'diterima')
                ->when($request->tahun_ajaran_id, function($q) use ($request) {
                    return $q->where('prestasi_siswa.id_tahun_ajaran', $request->tahun_ajaran_id);
                })
                ->groupBy('kategori_prestasi.id', 'kategori_prestasi.nama_kategori', 'kategori_prestasi.jenis_prestasi', 'kategori_prestasi.bidang_prestasi', 'kategori_prestasi.tingkat_kompetisi')
                ->orderBy('total', 'desc')
                ->get();

            // Achievement by class
            $prestasiByClass = Kelas::select(
                    'kelas.nama_kelas',
                    DB::raw('COUNT(prestasi_siswa.id) as total_prestasi'),
                    DB::raw('COUNT(DISTINCT siswa.id) as total_siswa')
                )
                ->leftJoin('siswa', 'kelas.id', '=', 'siswa.id_kelas')
                ->leftJoin('prestasi_siswa', function($join) use ($request) {
                    $join->on('siswa.id', '=', 'prestasi_siswa.id_siswa')
                         ->where('prestasi_siswa.status', '=', 'diterima');
                    if ($request->tahun_ajaran_id) {
                        $join->where('prestasi_siswa.id_tahun_ajaran', '=', $request->tahun_ajaran_id);
                    }
                })
                ->groupBy('kelas.id', 'kelas.nama_kelas')
                ->orderBy('total_prestasi', 'desc')
                ->get();

            // Monthly trends
            $monthlyTrends = PrestasiSiswa::select(
                    DB::raw('DATE_FORMAT(tanggal_prestasi, "%Y-%m") as bulan'),
                    DB::raw('COUNT(*) as total')
                )
                ->where('status', 'diterima')
                ->when($request->tahun_ajaran_id, function($q) use ($request) {
                    return $q->where('id_tahun_ajaran', $request->tahun_ajaran_id);
                })
                ->groupBy('bulan')
                ->orderBy('bulan')
                ->get();

            $data = [
                'periode' => $tahunAjaran,
                'total_prestasi' => $totalPrestasi,
                'prestasi_by_category' => $prestasiByCategory,
                'prestasi_by_class' => $prestasiByClass,
                'monthly_trends' => $monthlyTrends,
                'generated_at' => now()
            ];

            switch ($request->format) {
                case 'pdf':
                    return $this->generateSchoolPDF($data);
                case 'excel':
                    return $this->generateSchoolExcel($data);
                case 'json':
                    return response()->json($data);
                default:
                    return response()->json($data);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function generateMultiYearComparison(Request $request)
    {
        $request->validate([
            'years' => 'required|array|min:2',
            'years.*' => 'exists:tahun_ajaran,id',
            'format' => 'required|in:pdf,excel,json'
        ]);

        try {
            $tahunAjarans = TahunAjaran::whereIn('id', $request->years)
                ->orderBy('nama_tahun_ajaran')
                ->get();

            $comparisonData = [];
            
            foreach ($tahunAjarans as $tahun) {
                $totalPrestasi = PrestasiSiswa::where('id_tahun_ajaran', $tahun->id)
                    ->where('status', 'diterima')
                    ->count();
                
                $prestasiAkademik = PrestasiSiswa::whereHas('kategoriPrestasi', function($q) {
                    $q->where('jenis_prestasi', 'akademik');
                })->where('id_tahun_ajaran', $tahun->id)
                  ->where('status', 'diterima')
                  ->count();
                
                $prestasiNonAkademik = PrestasiSiswa::whereHas('kategoriPrestasi', function($q) {
                    $q->where('jenis_prestasi', 'non_akademik');
                })->where('id_tahun_ajaran', $tahun->id)
                  ->where('status', 'diterima')
                  ->count();

                // Competition level breakdown
                $competitionLevels = PrestasiSiswa::select('kategori_prestasi.tingkat_kompetisi', DB::raw('count(*) as total'))
                    ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
                    ->where('prestasi_siswa.id_tahun_ajaran', $tahun->id)
                    ->where('prestasi_siswa.status', 'diterima')
                    ->groupBy('kategori_prestasi.tingkat_kompetisi')
                    ->pluck('total', 'tingkat_kompetisi')
                    ->toArray();

                $comparisonData[] = [
                    'tahun_ajaran' => $tahun->nama_tahun_ajaran,
                    'total_prestasi' => $totalPrestasi,
                    'prestasi_akademik' => $prestasiAkademik,
                    'prestasi_non_akademik' => $prestasiNonAkademik,
                    'competition_levels' => $competitionLevels
                ];
            }

            $data = [
                'comparison_data' => $comparisonData,
                'selected_years' => $tahunAjarans,
                'generated_at' => now()
            ];

            switch ($request->format) {
                case 'pdf':
                    return $this->generateComparisonPDF($data);
                case 'excel':
                    return $this->generateComparisonExcel($data);
                case 'json':
                    return response()->json($data);
                default:
                    return response()->json($data);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function generateStudentPDF($data)
    {
        $pdf = PDF::loadView('admin.reports.pdf.student', $data);
        return $pdf->download('laporan-prestasi-siswa-' . $data['siswa']->nama . '.pdf');
    }

    private function generateClassPDF($data)
    {
        $pdf = PDF::loadView('admin.reports.pdf.class', $data);
        return $pdf->download('laporan-prestasi-kelas-' . $data['kelas']->nama_kelas . '.pdf');
    }

    private function generateSchoolPDF($data)
    {
        $pdf = PDF::loadView('admin.reports.pdf.school', $data);
        $filename = 'laporan-prestasi-sekolah-' . ($data['periode'] ? $data['periode']->nama_tahun_ajaran : 'semua') . '.pdf';
        return $pdf->download($filename);
    }

    private function generateComparisonPDF($data)
    {
        $pdf = PDF::loadView('admin.reports.pdf.comparison', $data);
        return $pdf->download('laporan-perbandingan-tahunan.pdf');
    }

    private function generateStudentExcel($data)
    {
        // Excel generation would go here - using a package like maatwebsite/excel
        return response()->json(['message' => 'Excel export not implemented yet']);
    }

    private function generateClassExcel($data)
    {
        return response()->json(['message' => 'Excel export not implemented yet']);
    }

    private function generateSchoolExcel($data)
    {
        return response()->json(['message' => 'Excel export not implemented yet']);
    }

    private function generateComparisonExcel($data)
    {
        return response()->json(['message' => 'Excel export not implemented yet']);
    }
}
