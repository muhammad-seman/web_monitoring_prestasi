<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KenaikanKelas;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\PrestasiSiswa;
use Illuminate\Support\Facades\DB;
use App\Helpers\ActivityLogger;

class KenaikanKelasController extends Controller
{
    public function index(Request $request)
    {
        $tahunAjaranId = $request->get('tahun_ajaran_id');
        $status = $request->get('status', 'all');

        $query = KenaikanKelas::with(['siswa.kelas', 'kelasAsal', 'kelasTujuan', 'tahunAjaran']);

        if ($tahunAjaranId) {
            $query->where('tahun_ajaran_id', $tahunAjaranId);
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $kenaikanKelas = $query->orderBy('created_at', 'desc')->paginate(20);
        $tahunAjarans = TahunAjaran::orderBy('nama_tahun_ajaran', 'desc')->get();

        return view('admin.kenaikan_kelas.index', compact('kenaikanKelas', 'tahunAjarans', 'tahunAjaranId', 'status'));
    }

    public function create()
    {
        $siswa = Siswa::with('kelas')
            ->whereHas('kelas', function($q) {
                $q->where('nama_kelas', 'like', '%XI%');
            })
            ->orderBy('nama')
            ->get();
        
        $kelasXII = Kelas::where('nama_kelas', 'like', '%XII%')->orderBy('nama_kelas')->get();
        $tahunAjarans = TahunAjaran::orderBy('nama_tahun_ajaran', 'desc')->get();

        return view('admin.kenaikan_kelas.create', compact('siswa', 'kelasXII', 'tahunAjarans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'siswa_selections' => 'required|array|min:1',
            'siswa_selections.*.siswa_id' => 'required|exists:siswa,id',
            'siswa_selections.*.kelas_tujuan_id' => 'required|exists:kelas,id',
            'siswa_selections.*.status' => 'required|in:naik,tidak_naik,pending',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'keterangan' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $successCount = 0;
            $errors = [];

            foreach ($request->siswa_selections as $selection) {
                $siswa = Siswa::findOrFail($selection['siswa_id']);
                
                // Check if student already has class progression record for this academic year
                $existing = KenaikanKelas::where('id_siswa', $siswa->id)
                    ->where('tahun_ajaran_id', $request->tahun_ajaran_id)
                    ->first();

                if ($existing) {
                    $errors[] = "Siswa {$siswa->nama} sudah memiliki data kenaikan kelas untuk tahun ajaran ini";
                    continue;
                }

                // Generate criteria based on student's achievements
                $criteria = $this->generateCriteria($siswa->id, $request->tahun_ajaran_id);

                KenaikanKelas::create([
                    'id_siswa' => $siswa->id,
                    'kelas_asal' => $siswa->id_kelas,
                    'kelas_tujuan' => $selection['kelas_tujuan_id'],
                    'tahun_ajaran_id' => $request->tahun_ajaran_id,
                    'status' => $selection['status'],
                    'kriteria_kelulusan' => $criteria,
                    'tanggal_kenaikan' => $selection['status'] === 'naik' ? now() : null,
                    'keterangan' => $request->keterangan,
                    'created_by' => auth()->id()
                ]);

                // If status is 'naik', update student's class
                if ($selection['status'] === 'naik') {
                    $siswa->update(['id_kelas' => $selection['kelas_tujuan_id']]);
                }

                $successCount++;

                ActivityLogger::log(
                    'create', 
                    'KenaikanKelas', 
                    $siswa->id, 
                    "Menambahkan data kenaikan kelas untuk siswa: {$siswa->nama} status: {$selection['status']}"
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil memproses {$successCount} siswa" . (count($errors) > 0 ? '. Errors: ' . implode(', ', $errors) : ''),
                'errors' => $errors
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
        $kenaikanKelas = KenaikanKelas::with([
            'siswa.prestasi.kategoriPrestasi', 
            'kelasAsal', 
            'kelasTujuan', 
            'tahunAjaran',
            'createdBy'
        ])->findOrFail($id);

        return view('admin.kenaikan_kelas.show', compact('kenaikanKelas'));
    }

    public function edit($id)
    {
        $kenaikanKelas = KenaikanKelas::with(['siswa', 'kelasAsal', 'kelasTujuan'])->findOrFail($id);
        $kelasXII = Kelas::where('nama_kelas', 'like', '%XII%')->orderBy('nama_kelas')->get();

        return view('admin.kenaikan_kelas.edit', compact('kenaikanKelas', 'kelasXII'));
    }

    public function update(Request $request, $id)
    {
        $kenaikanKelas = KenaikanKelas::findOrFail($id);

        $request->validate([
            'kelas_tujuan' => 'required|exists:kelas,id',
            'status' => 'required|in:naik,tidak_naik,pending',
            'keterangan' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $oldStatus = $kenaikanKelas->status;
            $siswa = $kenaikanKelas->siswa;

            $kenaikanKelas->update([
                'kelas_tujuan' => $request->kelas_tujuan,
                'status' => $request->status,
                'tanggal_kenaikan' => $request->status === 'naik' ? now() : null,
                'keterangan' => $request->keterangan
            ]);

            // Handle class change based on status
            if ($request->status === 'naik' && $oldStatus !== 'naik') {
                // Student is now promoted, update their class
                $siswa->update(['id_kelas' => $request->kelas_tujuan]);
            } elseif ($request->status !== 'naik' && $oldStatus === 'naik') {
                // Student is no longer promoted, revert to original class
                $siswa->update(['id_kelas' => $kenaikanKelas->kelas_asal]);
            }

            ActivityLogger::log(
                'update', 
                'KenaikanKelas', 
                $kenaikanKelas->id, 
                "Mengubah status kenaikan kelas siswa: {$siswa->nama} dari {$oldStatus} ke {$request->status}"
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data kenaikan kelas berhasil diupdate'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkProcess(Request $request)
    {
        $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'kriteria' => 'required|array',
            'kriteria.min_prestasi' => 'nullable|integer|min:0',
            'kriteria.min_nilai_rata' => 'nullable|numeric|min:0|max:100',
            'kriteria.include_non_akademik' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            // Get all Class XI students
            $siswaXI = Siswa::whereHas('kelas', function($q) {
                $q->where('nama_kelas', 'like', '%XI%');
            })->get();

            $processedCount = 0;
            $promoted = [];
            $notPromoted = [];

            foreach ($siswaXI as $siswa) {
                // Check if already processed
                $existing = KenaikanKelas::where('id_siswa', $siswa->id)
                    ->where('tahun_ajaran_id', $request->tahun_ajaran_id)
                    ->first();

                if ($existing) continue;

                // Evaluate criteria
                $evaluation = $this->evaluatePromotionCriteria($siswa->id, $request->tahun_ajaran_id, $request->kriteria);
                
                $status = $evaluation['meets_criteria'] ? 'naik' : 'tidak_naik';
                
                // Find appropriate Class XII
                $kelasXII = $this->findTargetClass($siswa->kelas->nama_kelas);

                if (!$kelasXII) {
                    $notPromoted[] = $siswa->nama . ' (Tidak ada kelas XII yang sesuai)';
                    continue;
                }

                KenaikanKelas::create([
                    'id_siswa' => $siswa->id,
                    'kelas_asal' => $siswa->id_kelas,
                    'kelas_tujuan' => $kelasXII->id,
                    'tahun_ajaran_id' => $request->tahun_ajaran_id,
                    'status' => $status,
                    'kriteria_kelulusan' => $evaluation['criteria_details'],
                    'tanggal_kenaikan' => $status === 'naik' ? now() : null,
                    'keterangan' => 'Proses otomatis berdasarkan kriteria',
                    'created_by' => auth()->id()
                ]);

                if ($status === 'naik') {
                    $siswa->update(['id_kelas' => $kelasXII->id]);
                    $promoted[] = $siswa->nama;
                } else {
                    $notPromoted[] = $siswa->nama;
                }

                $processedCount++;
            }

            ActivityLogger::log(
                'bulk_process', 
                'KenaikanKelas', 
                null, 
                "Proses kenaikan kelas otomatis: {$processedCount} siswa diproses"
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Proses selesai. {$processedCount} siswa diproses.",
                'details' => [
                    'promoted_count' => count($promoted),
                    'not_promoted_count' => count($notPromoted),
                    'promoted' => $promoted,
                    'not_promoted' => $notPromoted
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateCriteria($siswaId, $tahunAjaranId)
    {
        $prestasi = PrestasiSiswa::where('id_siswa', $siswaId)
            ->where('id_tahun_ajaran', $tahunAjaranId)
            ->where('status', 'diterima')
            ->with('kategoriPrestasi')
            ->get();

        return [
            'total_prestasi' => $prestasi->count(),
            'prestasi_akademik' => $prestasi->where('kategoriPrestasi.jenis_prestasi', 'akademik')->count(),
            'prestasi_non_akademik' => $prestasi->where('kategoriPrestasi.jenis_prestasi', 'non_akademik')->count(),
            'rata_rata_nilai' => $prestasi->where('rata_rata_nilai', '>', 0)->avg('rata_rata_nilai'),
            'tingkat_tertinggi' => $prestasi->max('kategoriPrestasi.tingkat_kompetisi'),
            'evaluated_at' => now(),
        ];
    }

    private function evaluatePromotionCriteria($siswaId, $tahunAjaranId, $criteria)
    {
        $studentCriteria = $this->generateCriteria($siswaId, $tahunAjaranId);
        
        $meetsCriteria = true;
        $reasons = [];

        // Check minimum achievements
        if (isset($criteria['min_prestasi']) && $criteria['min_prestasi'] > 0) {
            if ($studentCriteria['total_prestasi'] < $criteria['min_prestasi']) {
                $meetsCriteria = false;
                $reasons[] = "Prestasi tidak mencukupi ({$studentCriteria['total_prestasi']} < {$criteria['min_prestasi']})";
            }
        }

        // Check minimum average grade
        if (isset($criteria['min_nilai_rata']) && $criteria['min_nilai_rata'] > 0) {
            if ($studentCriteria['rata_rata_nilai'] < $criteria['min_nilai_rata']) {
                $meetsCriteria = false;
                $reasons[] = "Rata-rata nilai tidak mencukupi ({$studentCriteria['rata_rata_nilai']} < {$criteria['min_nilai_rata']})";
            }
        }

        // Check if non-academic achievements are required
        if (isset($criteria['include_non_akademik']) && $criteria['include_non_akademik']) {
            if ($studentCriteria['prestasi_non_akademik'] === 0) {
                $meetsCriteria = false;
                $reasons[] = "Tidak memiliki prestasi non-akademik";
            }
        }

        return [
            'meets_criteria' => $meetsCriteria,
            'criteria_details' => array_merge($studentCriteria, [
                'evaluation_reasons' => $reasons,
                'applied_criteria' => $criteria
            ])
        ];
    }

    private function findTargetClass($originalClassName)
    {
        // Simple logic to map XI to XII classes
        $targetClassName = str_replace('XI', 'XII', $originalClassName);
        return Kelas::where('nama_kelas', $targetClassName)->first() 
            ?? Kelas::where('nama_kelas', 'like', '%XII%')->first();
    }

    public function destroy($id)
    {
        $kenaikanKelas = KenaikanKelas::findOrFail($id);
        
        try {
            DB::beginTransaction();

            $siswa = $kenaikanKelas->siswa;

            // If student was promoted, revert their class
            if ($kenaikanKelas->status === 'naik') {
                $siswa->update(['id_kelas' => $kenaikanKelas->kelas_asal]);
            }

            ActivityLogger::log(
                'delete', 
                'KenaikanKelas', 
                $kenaikanKelas->id, 
                "Menghapus data kenaikan kelas siswa: {$siswa->nama}"
            );

            $kenaikanKelas->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data kenaikan kelas berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getEligibleStudents(Request $request)
    {
        $tahunAjaranId = $request->get('tahun_ajaran_id');
        
        $siswa = Siswa::with(['kelas', 'prestasi' => function($q) use ($tahunAjaranId) {
                $q->where('id_tahun_ajaran', $tahunAjaranId)
                  ->where('status', 'diterima');
            }])
            ->whereHas('kelas', function($q) {
                $q->where('nama_kelas', 'like', '%XI%');
            })
            ->whereDoesntHave('kenaikanKelas', function($q) use ($tahunAjaranId) {
                $q->where('tahun_ajaran_id', $tahunAjaranId);
            })
            ->orderBy('nama')
            ->get()
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'nama' => $student->nama,
                    'nisn' => $student->nisn,
                    'kelas' => $student->kelas->nama_kelas,
                    'total_prestasi' => $student->prestasi->count(),
                    'rata_rata_nilai' => $student->prestasi->where('rata_rata_nilai', '>', 0)->avg('rata_rata_nilai')
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $siswa
        ]);
    }
}
