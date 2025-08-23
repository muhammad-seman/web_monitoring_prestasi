<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TahunAjaran;
use App\Models\PrestasiSiswa;
use Illuminate\Support\Facades\DB;
use App\Helpers\ActivityLogger;

class TahunAjaranController extends Controller
{
    public function index()
    {
        $tahunAjarans = TahunAjaran::withCount('prestasi')
            ->orderBy('nama_tahun_ajaran', 'desc')
            ->get()
            ->map(function($tahun) {
                return [
                    'id' => $tahun->id,
                    'nama_tahun_ajaran' => $tahun->nama_tahun_ajaran,
                    'tanggal_mulai' => $tahun->tanggal_mulai->format('d/m/Y'),
                    'tanggal_selesai' => $tahun->tanggal_selesai->format('d/m/Y'),
                    'semester_aktif' => ucfirst($tahun->semester_aktif),
                    'is_active' => $tahun->is_active,
                    'keterangan' => $tahun->keterangan,
                    'total_prestasi' => $tahun->prestasi_count,
                    'format_tahun' => $tahun->format_tahun
                ];
            });

        return view('admin.tahun_ajaran.index', compact('tahunAjarans'));
    }

    public function create()
    {
        return view('admin.tahun_ajaran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tahun_ajaran' => 'required|string|max:10|unique:tahun_ajaran,nama_tahun_ajaran',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'semester_aktif' => 'required|in:ganjil,genap',
            'keterangan' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $tahunAjaran = TahunAjaran::create([
                'nama_tahun_ajaran' => $request->nama_tahun_ajaran,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'semester_aktif' => $request->semester_aktif,
                'is_active' => false,
                'keterangan' => $request->keterangan
            ]);

            ActivityLogger::log(
                'create', 
                'TahunAjaran', 
                $tahunAjaran->id, 
                "Menambahkan tahun ajaran baru: {$tahunAjaran->nama_tahun_ajaran}"
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tahun ajaran berhasil ditambahkan',
                'data' => $tahunAjaran
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $tahunAjaran = TahunAjaran::with(['prestasi.siswa', 'prestasi.kategoriPrestasi'])
            ->findOrFail($id);

        $statistics = [
            'total_prestasi' => $tahunAjaran->prestasi->where('status', 'diterima')->count(),
            'prestasi_pending' => $tahunAjaran->prestasi->where('status', 'menunggu_validasi')->count(),
            'prestasi_ditolak' => $tahunAjaran->prestasi->where('status', 'ditolak')->count(),
            'prestasi_akademik' => $tahunAjaran->prestasi
                ->where('status', 'diterima')
                ->where('kategoriPrestasi.jenis_prestasi', 'akademik')->count(),
            'prestasi_non_akademik' => $tahunAjaran->prestasi
                ->where('status', 'diterima')
                ->where('kategoriPrestasi.jenis_prestasi', 'non_akademik')->count(),
        ];

        $monthlyDistribution = $tahunAjaran->prestasi()
            ->select(
                DB::raw('DATE_FORMAT(tanggal_prestasi, "%Y-%m") as bulan'),
                DB::raw('COUNT(*) as total')
            )
            ->where('status', 'diterima')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        return view('admin.tahun_ajaran.show', compact('tahunAjaran', 'statistics', 'monthlyDistribution'));
    }

    public function edit($id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);
        return view('admin.tahun_ajaran.edit', compact('tahunAjaran'));
    }

    public function update(Request $request, $id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);

        $request->validate([
            'nama_tahun_ajaran' => 'required|string|max:10|unique:tahun_ajaran,nama_tahun_ajaran,' . $id,
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'semester_aktif' => 'required|in:ganjil,genap',
            'keterangan' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $oldData = $tahunAjaran->toArray();

            $tahunAjaran->update([
                'nama_tahun_ajaran' => $request->nama_tahun_ajaran,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'semester_aktif' => $request->semester_aktif,
                'keterangan' => $request->keterangan
            ]);

            ActivityLogger::log(
                'update', 
                'TahunAjaran', 
                $tahunAjaran->id, 
                "Mengubah tahun ajaran: {$tahunAjaran->nama_tahun_ajaran}",
                $oldData
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tahun ajaran berhasil diupdate',
                'data' => $tahunAjaran
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);

        // Check if this academic year has any achievements
        $hasAchievements = $tahunAjaran->prestasi()->count() > 0;
        
        if ($hasAchievements) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus tahun ajaran yang sudah memiliki data prestasi'
            ], 422);
        }

        // Don't allow deleting active academic year
        if ($tahunAjaran->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus tahun ajaran yang sedang aktif'
            ], 422);
        }

        try {
            DB::beginTransaction();

            ActivityLogger::log(
                'delete', 
                'TahunAjaran', 
                $tahunAjaran->id, 
                "Menghapus tahun ajaran: {$tahunAjaran->nama_tahun_ajaran}"
            );

            $tahunAjaran->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tahun ajaran berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function setActive(Request $request, $id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);

        try {
            DB::beginTransaction();

            // Deactivate all other academic years
            TahunAjaran::where('is_active', true)->update(['is_active' => false]);

            // Activate selected academic year
            $tahunAjaran->update(['is_active' => true]);

            ActivityLogger::log(
                'activate', 
                'TahunAjaran', 
                $tahunAjaran->id, 
                "Mengaktifkan tahun ajaran: {$tahunAjaran->nama_tahun_ajaran}"
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Tahun ajaran {$tahunAjaran->nama_tahun_ajaran} berhasil diaktifkan"
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function changeSemester(Request $request, $id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);

        $request->validate([
            'semester' => 'required|in:ganjil,genap'
        ]);

        try {
            DB::beginTransaction();

            $oldSemester = $tahunAjaran->semester_aktif;
            
            $tahunAjaran->update([
                'semester_aktif' => $request->semester
            ]);

            ActivityLogger::log(
                'change_semester', 
                'TahunAjaran', 
                $tahunAjaran->id, 
                "Mengubah semester dari {$oldSemester} ke {$request->semester} pada tahun ajaran: {$tahunAjaran->nama_tahun_ajaran}"
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Semester berhasil diubah ke " . ucfirst($request->semester)
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getActive()
    {
        $activeTahunAjaran = TahunAjaran::getActiveTahunAjaran();
        
        if (!$activeTahunAjaran) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada tahun ajaran yang aktif'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $activeTahunAjaran->id,
                'nama_tahun_ajaran' => $activeTahunAjaran->nama_tahun_ajaran,
                'semester_aktif' => $activeTahunAjaran->semester_aktif,
                'format_tahun' => $activeTahunAjaran->format_tahun,
                'tanggal_mulai' => $activeTahunAjaran->tanggal_mulai->format('Y-m-d'),
                'tanggal_selesai' => $activeTahunAjaran->tanggal_selesai->format('Y-m-d')
            ]
        ]);
    }

    public function getAllForSelect()
    {
        $tahunAjarans = TahunAjaran::select('id', 'nama_tahun_ajaran', 'is_active')
            ->orderBy('nama_tahun_ajaran', 'desc')
            ->get()
            ->map(function($tahun) {
                return [
                    'value' => $tahun->id,
                    'label' => $tahun->nama_tahun_ajaran . ($tahun->is_active ? ' (Aktif)' : ''),
                    'is_active' => $tahun->is_active
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $tahunAjarans
        ]);
    }

    public function duplicateToNext(Request $request, $id)
    {
        $currentTahunAjaran = TahunAjaran::findOrFail($id);

        $request->validate([
            'nama_tahun_ajaran' => 'required|string|max:10|unique:tahun_ajaran,nama_tahun_ajaran'
        ]);

        try {
            DB::beginTransaction();

            // Calculate next academic year dates
            $nextStartDate = $currentTahunAjaran->tanggal_mulai->addYear();
            $nextEndDate = $currentTahunAjaran->tanggal_selesai->addYear();

            $nextTahunAjaran = TahunAjaran::create([
                'nama_tahun_ajaran' => $request->nama_tahun_ajaran,
                'tanggal_mulai' => $nextStartDate,
                'tanggal_selesai' => $nextEndDate,
                'semester_aktif' => 'ganjil',
                'is_active' => false,
                'keterangan' => "Duplikasi dari tahun ajaran {$currentTahunAjaran->nama_tahun_ajaran}"
            ]);

            ActivityLogger::log(
                'duplicate', 
                'TahunAjaran', 
                $nextTahunAjaran->id, 
                "Menduplikasi tahun ajaran dari {$currentTahunAjaran->nama_tahun_ajaran} ke {$nextTahunAjaran->nama_tahun_ajaran}"
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tahun ajaran berhasil diduplikasi',
                'data' => $nextTahunAjaran
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
