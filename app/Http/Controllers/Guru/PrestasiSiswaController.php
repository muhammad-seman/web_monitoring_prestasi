<?php

namespace App\Http\Controllers\Guru;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\PrestasiSiswa;
use App\Models\Siswa;
use App\Models\KategoriPrestasi;
use App\Models\TingkatPenghargaan;
use App\Models\Ekstrakurikuler;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

class PrestasiSiswaController extends Controller
{
    /**
     * Display a listing of prestasi siswa di kelas yang diampu guru.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Ambil kelas yang diampu guru
        $kelasGuru = Kelas::where('id_wali_kelas', $user->id)->pluck('id');
        
        // Query prestasi siswa hanya di kelas yang diampu
        $query = PrestasiSiswa::with(['siswa', 'kategori', 'tingkat', 'ekskul', 'creator', 'validator'])
            ->whereHas('siswa', function($q) use ($kelasGuru) {
                $q->whereIn('id_kelas', $kelasGuru);
            })
            ->orderByDesc('created_at');

        // FILTER KATEGORI
        if ($request->filled('kategori')) {
            $query->where('id_kategori_prestasi', $request->kategori);
        }
        // FILTER RANGE TANGGAL
        if ($request->filled('from')) {
            $query->whereDate('tanggal_prestasi', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('tanggal_prestasi', '<=', $request->to);
        }
        // FILTER TINGKAT
        if ($request->filled('tingkat')) {
            $query->where('id_tingkat_penghargaan', $request->tingkat);
        }
        // FILTER STATUS
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $prestasi = $query->paginate(10)->appends($request->except('page'));

        // Data untuk dropdown (hanya siswa di kelas yang diampu)
        $siswa = Siswa::whereIn('id_kelas', $kelasGuru)->pluck('nama', 'id');
        $kategori = KategoriPrestasi::pluck('nama_kategori', 'id');
        $tingkat = TingkatPenghargaan::pluck('tingkat', 'id');
        $ekskul = Ekstrakurikuler::pluck('nama', 'id');

        return view('guru.prestasi_siswa.index', compact('prestasi', 'siswa', 'kategori', 'tingkat', 'ekskul'));
    }

   

    /**
     * Store a newly created prestasi in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $kelasGuru = Kelas::where('id_wali_kelas', $user->id)->pluck('id');
        
        $validated = $request->validate([
            'id_siswa'                => 'required|exists:siswa,id',
            'id_kategori_prestasi'    => 'required|exists:kategori_prestasi,id',
            'id_tingkat_penghargaan'  => 'required|exists:tingkat_penghargaan,id',
            'id_ekskul'               => 'nullable|exists:ekstrakurikuler,id',
            'nama_prestasi'           => 'required|string|max:100',
            'penyelenggara'           => 'required|string|max:100',
            'tanggal_prestasi'        => 'required|date',
            'keterangan'              => 'nullable|string|max:255',
            'dokumen_file'            => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'rata_rata_nilai'         => 'nullable|numeric',
            'alasan_tolak'            => 'nullable|string|max:255',
        ]);

        // Validasi bahwa siswa berada di kelas yang diampu guru
        $siswa = Siswa::find($validated['id_siswa']);
        if (!$siswa || !$kelasGuru->contains($siswa->id_kelas)) {
            return back()->withErrors(['id_siswa' => 'Siswa tidak ditemukan di kelas yang Anda ampu.'])->withInput();
        }

        // Upload file jika ada
        if ($request->hasFile('dokumen_file')) {
            $file = $request->file('dokumen_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads/sertifikat', $filename, 'public');
            $validated['dokumen_url'] = 'storage/' . $path;
        }

        $validated['created_by'] = $user->id;

        $prestasi = PrestasiSiswa::create($validated);

        ActivityLogger::log('create', 'prestasi_siswa', 'Guru tambah prestasi: ' . $prestasi->nama_prestasi . ' oleh ' . ($prestasi->siswa->nama ?? '-'));
        return redirect()->route('guru.prestasi_siswa.index')->with('success', 'Prestasi siswa berhasil ditambah.');
    }

    /**
     * Display the specified prestasi.
     */
    public function show(PrestasiSiswa $prestasi_siswa)
    {
        $user = Auth::user();
        $kelasGuru = Kelas::where('id_wali_kelas', $user->id)->pluck('id');
        
        // Validasi akses
        if (!$kelasGuru->contains($prestasi_siswa->siswa->id_kelas)) {
            abort(403, 'Anda tidak memiliki akses ke prestasi siswa ini.');
        }

        return view('guru.prestasi_siswa.show', compact('prestasi_siswa'));
    }

   

    /**
     * Update the specified prestasi in storage.
     */
    public function update(Request $request, PrestasiSiswa $prestasi_siswa)
    {
        $user = Auth::user();
        $kelasGuru = Kelas::where('id_wali_kelas', $user->id)->pluck('id');
        
        // Validasi akses
        if (!$kelasGuru->contains($prestasi_siswa->siswa->id_kelas)) {
            abort(403, 'Anda tidak memiliki akses ke prestasi siswa ini.');
        }

        $validated = $request->validate([
            'id_siswa'                => 'required|exists:siswa,id',
            'id_kategori_prestasi'    => 'required|exists:kategori_prestasi,id',
            'id_tingkat_penghargaan'  => 'required|exists:tingkat_penghargaan,id',
            'id_ekskul'               => 'nullable|exists:ekstrakurikuler,id',
            'nama_prestasi'           => 'required|string|max:100',
            'penyelenggara'           => 'required|string|max:100',
            'tanggal_prestasi'        => 'required|date',
            'keterangan'              => 'nullable|string|max:255',
            'dokumen_file'            => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'rata_rata_nilai'         => 'nullable|numeric',
            'alasan_tolak'            => 'nullable|string|max:255',
        ]);

        // Validasi bahwa siswa berada di kelas yang diampu guru
        $siswa = Siswa::find($validated['id_siswa']);
        if (!$siswa || !$kelasGuru->contains($siswa->id_kelas)) {
            return back()->withErrors(['id_siswa' => 'Siswa tidak ditemukan di kelas yang Anda ampu.'])->withInput();
        }

        // Upload file baru jika ada
        if ($request->hasFile('dokumen_file')) {
            // Hapus file lama
            if ($prestasi_siswa->dokumen_url && file_exists(public_path($prestasi_siswa->dokumen_url))) {
                @unlink(public_path($prestasi_siswa->dokumen_url));
            }

            $file = $request->file('dokumen_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads/sertifikat', $filename, 'public');
            $validated['dokumen_url'] = 'storage/' . $path;
        }

        $prestasi_siswa->update($validated);

        ActivityLogger::log('update', 'prestasi_siswa', 'Guru update prestasi: ' . $prestasi_siswa->nama_prestasi . ' oleh ' . ($prestasi_siswa->siswa->nama ?? '-'));
        return redirect()->route('guru.prestasi_siswa.index')->with('success', 'Prestasi siswa berhasil diupdate.');
    }


    /**
     * Upload dokumen bukti prestasi.
     */
    public function uploadDokumen(Request $request, PrestasiSiswa $prestasi_siswa)
    {
        $user = Auth::user();
        $kelasGuru = Kelas::where('id_wali_kelas', $user->id)->pluck('id');
        
        // Validasi akses
        if (!$kelasGuru->contains($prestasi_siswa->siswa->id_kelas)) {
            abort(403, 'Anda tidak memiliki akses ke prestasi siswa ini.');
        }

        $validated = $request->validate([
            'dokumen_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Hapus file lama jika ada
        if ($prestasi_siswa->dokumen_url && file_exists(public_path($prestasi_siswa->dokumen_url))) {
            @unlink(public_path($prestasi_siswa->dokumen_url));
        }

        // Upload file baru
        $file = $request->file('dokumen_file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('uploads/sertifikat', $filename, 'public');
        
        $prestasi_siswa->update([
            'dokumen_url' => 'storage/' . $path,
        ]);

        ActivityLogger::log('update', 'prestasi_siswa', 'Guru upload dokumen prestasi: ' . $prestasi_siswa->nama_prestasi);
        return redirect()->route('guru.prestasi_siswa.show', $prestasi_siswa)->with('success', 'Dokumen berhasil diupload.');
    }

    /**
     * Validasi prestasi siswa yang dibuat oleh guru.
     */
    public function validasi(Request $request, PrestasiSiswa $prestasi_siswa)
    {
        $user = Auth::user();
        $kelasGuru = Kelas::where('id_wali_kelas', $user->id)->pluck('id');
        
        // Validasi akses - guru hanya bisa memvalidasi prestasi siswa di kelasnya
        if (!$kelasGuru->contains($prestasi_siswa->siswa->id_kelas)) {
            abort(403, 'Anda tidak memiliki akses ke prestasi siswa ini.');
        }

        // Validasi bahwa prestasi ini dibuat oleh guru yang sama
        if ($prestasi_siswa->created_by !== $user->id) {
            return back()->with('error', 'Anda hanya dapat memvalidasi prestasi yang Anda buat sendiri.');
        }

        $request->validate([
            'status' => 'required|in:diterima,ditolak',
            'alasan_tolak' => 'nullable|string|max:255'
        ]);
        
        // Store old status for comparison
        $oldStatus = $prestasi_siswa->status;
        
        $prestasi_siswa->update([
            'status' => $request->status,
            'alasan_tolak' => $request->alasan_tolak,
            'validated_at' => now(),
            'validated_by' => $user->id
        ]);
        
        // Send notification to parent if status changed to accepted or rejected
        if ($oldStatus !== $request->status && $prestasi_siswa->siswa->wali_id) {
            $statusText = $request->status === 'diterima' ? 'diterima' : 'ditolak';
            $title = $request->status === 'diterima' ? 'Prestasi Diterima' : 'Prestasi Ditolak';
            $message = "Prestasi '{$prestasi_siswa->nama_prestasi}' anak Anda {$prestasi_siswa->siswa->nama} telah {$statusText} oleh guru kelas.";
            
            if ($request->status === 'ditolak' && !empty($request->alasan_tolak)) {
                $message .= " Alasan: {$request->alasan_tolak}";
            }
            
            \App\Models\Notification::createForParent(
                $prestasi_siswa->siswa->wali_id,
                $title,
                $message,
                [
                    'prestasi_id' => $prestasi_siswa->id,
                    'siswa_id' => $prestasi_siswa->siswa->id,
                    'siswa_nama' => $prestasi_siswa->siswa->nama,
                    'prestasi_nama' => $prestasi_siswa->nama_prestasi,
                    'action' => 'validated',
                    'status' => $request->status
                ]
            );
        }

        // Send notification to student if status changed to accepted or rejected
        if ($oldStatus !== $request->status && $prestasi_siswa->siswa->user_id) {
            $statusText = $request->status === 'diterima' ? 'diterima' : 'ditolak';
            $title = $request->status === 'diterima' ? 'Prestasi Diterima' : 'Prestasi Ditolak';
            $message = "Prestasi '{$prestasi_siswa->nama_prestasi}' yang diajukan telah {$statusText} oleh guru kelas.";
            
            if ($request->status === 'ditolak' && !empty($request->alasan_tolak)) {
                $message .= " Alasan: {$request->alasan_tolak}";
            }
            
            \App\Models\Notification::create([
                'user_id' => $prestasi_siswa->siswa->user_id,
                'title' => $title,
                'message' => $message,
                'data' => json_encode([
                    'prestasi_id' => $prestasi_siswa->id,
                    'prestasi_nama' => $prestasi_siswa->nama_prestasi,
                    'action' => 'validated',
                    'status' => $request->status
                ]),
                'read_at' => null
            ]);
        }
        
        ActivityLogger::log('update', 'prestasi_siswa', 'Guru validasi prestasi: ' . $prestasi_siswa->nama_prestasi . ' status: ' . $request->status);
        return redirect()->back()->with('success', 'Prestasi berhasil divalidasi.');
    }

    /**
     * Cetak rekap prestasi siswa di kelas yang diampu.
     */
    public function cetak(Request $request)
    {
        $user = Auth::user();
        $kelasGuru = Kelas::where('id_wali_kelas', $user->id)->pluck('id');
        
        $query = PrestasiSiswa::with(['siswa', 'kategori', 'tingkat'])
            ->whereHas('siswa', function($q) use ($kelasGuru) {
                $q->whereIn('id_kelas', $kelasGuru);
            });

        if ($request->filled('kategori')) {
            $query->where('id_kategori_prestasi', $request->kategori);
        }
        if ($request->filled('from')) {
            $query->whereDate('tanggal_prestasi', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('tanggal_prestasi', '<=', $request->to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data['prestasi'] = $query->orderByDesc('tanggal_prestasi')->get();
        $data['kelas'] = Kelas::whereIn('id', $kelasGuru)->get();

        $pdf = Pdf::loadView('guru.prestasi_siswa.cetak', $data);
        return $pdf->stream('rekap-prestasi-guru-' . now()->format('Ymd-His') . '.pdf');
    }

    /**
     * Enhanced Validation Dashboard - dedicated interface for validation workflow
     */
    public function validationDashboard(Request $request)
    {
        $user = Auth::user();
        $kelasGuru = Kelas::where('id_wali_kelas', $user->id)->pluck('id');
        
        // Get prestasi that need validation (pending status)
        $query = PrestasiSiswa::with(['siswa.kelas', 'kategori', 'tingkat', 'ekskul'])
            ->whereHas('siswa', function($q) use ($kelasGuru) {
                $q->whereIn('id_kelas', $kelasGuru);
            })
            ->where('created_by', $user->id) // Only prestasi created by this teacher
            ->where('status', 'menunggu_validasi');

        // Priority-based sorting
        $priority = $request->get('priority', 'date_desc');
        switch ($priority) {
            case 'date_asc':
                $query->orderBy('created_at');
                break;
            case 'date_desc':
                $query->orderByDesc('created_at');
                break;
            case 'name_asc':
                $query->join('siswa', 'prestasi_siswa.id_siswa', '=', 'siswa.id')
                      ->orderBy('siswa.nama');
                break;
            case 'category':
                $query->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
                      ->orderBy('kategori_prestasi.nama_kategori');
                break;
            case 'urgent':
                // Urgent: older than 7 days without validation
                $query->where('created_at', '<', now()->subDays(7))->orderBy('created_at');
                break;
        }

        $pendingValidation = $query->paginate(20);
        
        // Get validation statistics
        $stats = $this->getValidationStats($user, $kelasGuru);
        
        // Get quick action templates
        $quickActions = $this->getQuickActionTemplates();

        return view('guru.prestasi_siswa.validation_dashboard', compact(
            'pendingValidation', 'stats', 'quickActions', 'priority'
        ));
    }

    /**
     * Batch validation - validate multiple prestasi at once
     */
    public function batchValidation(Request $request)
    {
        $request->validate([
            'prestasi_ids' => 'required|array|min:1',
            'prestasi_ids.*' => 'required|exists:prestasi_siswa,id',
            'action' => 'required|in:approve,reject',
            'alasan_tolak' => 'nullable|string|max:500',
            'feedback' => 'nullable|string|max:1000'
        ]);

        $user = Auth::user();
        $kelasGuru = Kelas::where('id_wali_kelas', $user->id)->pluck('id');
        
        $processed = 0;
        $errors = [];
        
        foreach ($request->prestasi_ids as $prestasiId) {
            try {
                $prestasi = PrestasiSiswa::with('siswa')->find($prestasiId);
                
                // Validate access
                if (!$prestasi || !$kelasGuru->contains($prestasi->siswa->id_kelas) || $prestasi->created_by !== $user->id) {
                    $errors[] = "Prestasi ID {$prestasiId}: Akses tidak diizinkan";
                    continue;
                }
                
                $status = $request->action === 'approve' ? 'diterima' : 'ditolak';
                $oldStatus = $prestasi->status;
                
                $prestasi->update([
                    'status' => $status,
                    'alasan_tolak' => $request->action === 'reject' ? $request->alasan_tolak : null,
                    'feedback' => $request->feedback,
                    'validated_at' => now(),
                    'validated_by' => $user->id
                ]);
                
                // Send notification to parent if status changed
                $this->sendValidationNotification($prestasi, $oldStatus, $status, $request->alasan_tolak);
                
                $processed++;
                
            } catch (\Exception $e) {
                $errors[] = "Prestasi ID {$prestasiId}: " . $e->getMessage();
            }
        }
        
        ActivityLogger::log('batch_validation', 'prestasi_siswa', "Guru batch validasi {$processed} prestasi, action: {$request->action}");
        
        $message = "{$processed} prestasi berhasil divalidasi.";
        if (!empty($errors)) {
            $message .= " Errors: " . implode(', ', $errors);
        }
        
        return redirect()->back()->with('success', $message);
    }

    /**
     * Quick validation with predefined actions
     */
    public function quickValidation(Request $request, PrestasiSiswa $prestasi_siswa)
    {
        $request->validate([
            'quick_action' => 'required|string|in:approve_excellent,approve_good,reject_incomplete,reject_invalid,needs_revision'
        ]);

        $user = Auth::user();
        $kelasGuru = Kelas::where('id_wali_kelas', $user->id)->pluck('id');
        
        // Validate access
        if (!$kelasGuru->contains($prestasi_siswa->siswa->id_kelas) || $prestasi_siswa->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke prestasi siswa ini.');
        }

        $quickActions = $this->getQuickActionTemplates();
        $action = $quickActions[$request->quick_action];
        
        $oldStatus = $prestasi_siswa->status;
        
        $prestasi_siswa->update([
            'status' => $action['status'],
            'alasan_tolak' => $action['alasan_tolak'] ?? null,
            'feedback' => $action['feedback'],
            'validated_at' => now(),
            'validated_by' => $user->id
        ]);
        
        // Send notification
        $this->sendValidationNotification($prestasi_siswa, $oldStatus, $action['status'], $action['alasan_tolak'] ?? null);
        
        ActivityLogger::log('quick_validation', 'prestasi_siswa', "Guru quick validasi: {$prestasi_siswa->nama_prestasi}, action: {$request->quick_action}");
        
        return response()->json([
            'success' => true,
            'message' => 'Prestasi berhasil divalidasi dengan quick action.',
            'new_status' => $action['status']
        ]);
    }

    /**
     * Enhanced validation with detailed criteria
     */
    public function enhancedValidation(Request $request, PrestasiSiswa $prestasi_siswa)
    {
        $request->validate([
            'status' => 'required|in:diterima,ditolak',
            'criteria' => 'required|array',
            'criteria.completeness' => 'required|integer|min:1|max:5',
            'criteria.authenticity' => 'required|integer|min:1|max:5',
            'criteria.relevance' => 'required|integer|min:1|max:5',
            'criteria.documentation' => 'required|integer|min:1|max:5',
            'detailed_feedback' => 'nullable|string|max:1000',
            'improvement_suggestions' => 'nullable|string|max:500',
            'alasan_tolak' => 'nullable|string|max:255'
        ]);

        $user = Auth::user();
        $kelasGuru = Kelas::where('id_wali_kelas', $user->id)->pluck('id');
        
        // Validate access
        if (!$kelasGuru->contains($prestasi_siswa->siswa->id_kelas) || $prestasi_siswa->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke prestasi siswa ini.');
        }

        $oldStatus = $prestasi_siswa->status;
        $criteria = $request->criteria;
        $averageScore = collect($criteria)->avg();
        
        // Auto-determine status based on criteria scores if not manually set
        if ($averageScore >= 4.0 && $request->status === 'diterima') {
            $status = 'diterima';
        } elseif ($averageScore < 3.0) {
            $status = 'ditolak';
        } else {
            $status = $request->status;
        }
        
        $prestasi_siswa->update([
            'status' => $status,
            'alasan_tolak' => $request->alasan_tolak,
            'feedback' => $request->detailed_feedback,
            'validation_criteria' => json_encode([
                'completeness' => $criteria['completeness'],
                'authenticity' => $criteria['authenticity'], 
                'relevance' => $criteria['relevance'],
                'documentation' => $criteria['documentation'],
                'average_score' => round($averageScore, 2),
                'improvement_suggestions' => $request->improvement_suggestions
            ]),
            'validated_at' => now(),
            'validated_by' => $user->id
        ]);
        
        // Send detailed notification
        $this->sendEnhancedValidationNotification($prestasi_siswa, $oldStatus, $status, $criteria, $averageScore);
        
        ActivityLogger::log('enhanced_validation', 'prestasi_siswa', "Guru enhanced validasi: {$prestasi_siswa->nama_prestasi}, score: {$averageScore}");
        
        return redirect()->back()->with('success', 'Prestasi berhasil divalidasi dengan kriteria detail.');
    }

    /**
     * Get validation statistics for dashboard
     */
    private function getValidationStats($user, $kelasGuru)
    {
        $totalPrestasi = PrestasiSiswa::whereHas('siswa', function($q) use ($kelasGuru) {
            $q->whereIn('id_kelas', $kelasGuru);
        })->where('created_by', $user->id)->count();

        $validated = PrestasiSiswa::whereHas('siswa', function($q) use ($kelasGuru) {
            $q->whereIn('id_kelas', $kelasGuru);
        })->where('created_by', $user->id)->whereIn('status', ['diterima', 'ditolak'])->count();

        $pending = PrestasiSiswa::whereHas('siswa', function($q) use ($kelasGuru) {
            $q->whereIn('id_kelas', $kelasGuru);
        })->where('created_by', $user->id)->where('status', 'menunggu_validasi')->count();

        $approved = PrestasiSiswa::whereHas('siswa', function($q) use ($kelasGuru) {
            $q->whereIn('id_kelas', $kelasGuru);
        })->where('created_by', $user->id)->where('status', 'diterima')->count();

        $rejected = PrestasiSiswa::whereHas('siswa', function($q) use ($kelasGuru) {
            $q->whereIn('id_kelas', $kelasGuru);
        })->where('created_by', $user->id)->where('status', 'ditolak')->count();

        $urgent = PrestasiSiswa::whereHas('siswa', function($q) use ($kelasGuru) {
            $q->whereIn('id_kelas', $kelasGuru);
        })->where('created_by', $user->id)
        ->where('status', 'menunggu_validasi')
        ->where('created_at', '<', now()->subDays(7))->count();

        return [
            'total' => $totalPrestasi,
            'validated' => $validated,
            'pending' => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
            'urgent' => $urgent,
            'validation_rate' => $totalPrestasi > 0 ? round(($validated / $totalPrestasi) * 100, 1) : 0,
            'approval_rate' => $validated > 0 ? round(($approved / $validated) * 100, 1) : 0
        ];
    }

    /**
     * Get quick action templates
     */
    private function getQuickActionTemplates()
    {
        return [
            'approve_excellent' => [
                'status' => 'diterima',
                'feedback' => 'Prestasi sangat baik! Dokumentasi lengkap dan sesuai kriteria. Pertahankan kualitas ini.',
                'alasan_tolak' => null
            ],
            'approve_good' => [
                'status' => 'diterima',
                'feedback' => 'Prestasi memenuhi kriteria standar. Dokumentasi cukup baik.',
                'alasan_tolak' => null
            ],
            'reject_incomplete' => [
                'status' => 'ditolak',
                'feedback' => 'Dokumentasi tidak lengkap. Mohon lengkapi dokumen pendukung yang diperlukan.',
                'alasan_tolak' => 'Dokumentasi tidak lengkap'
            ],
            'reject_invalid' => [
                'status' => 'ditolak', 
                'feedback' => 'Prestasi tidak sesuai dengan kategori atau kriteria yang ditetapkan.',
                'alasan_tolak' => 'Tidak sesuai kriteria'
            ],
            'needs_revision' => [
                'status' => 'ditolak',
                'feedback' => 'Prestasi berpotensi diterima namun perlu revisi. Silakan perbaiki sesuai saran dan submit ulang.',
                'alasan_tolak' => 'Perlu revisi'
            ]
        ];
    }

    /**
     * Send validation notification to parent
     */
    private function sendValidationNotification($prestasi, $oldStatus, $newStatus, $alasanTolak = null)
    {
        if ($oldStatus !== $newStatus && $prestasi->siswa->wali_id) {
            $statusText = $newStatus === 'diterima' ? 'diterima' : 'ditolak';
            $title = $newStatus === 'diterima' ? 'Prestasi Diterima' : 'Prestasi Ditolak';
            $message = "Prestasi '{$prestasi->nama_prestasi}' anak Anda {$prestasi->siswa->nama} telah {$statusText} oleh guru kelas.";
            
            if ($newStatus === 'ditolak' && !empty($alasanTolak)) {
                $message .= " Alasan: {$alasanTolak}";
            }
            
            \App\Models\Notification::createForParent(
                $prestasi->siswa->wali_id,
                $title,
                $message,
                [
                    'prestasi_id' => $prestasi->id,
                    'siswa_id' => $prestasi->siswa->id,
                    'siswa_nama' => $prestasi->siswa->nama,
                    'prestasi_nama' => $prestasi->nama_prestasi,
                    'action' => 'validated',
                    'status' => $newStatus
                ]
            );
        }
    }

    /**
     * Send enhanced validation notification with detailed criteria
     */
    private function sendEnhancedValidationNotification($prestasi, $oldStatus, $newStatus, $criteria, $averageScore)
    {
        if ($oldStatus !== $newStatus && $prestasi->siswa->wali_id) {
            $statusText = $newStatus === 'diterima' ? 'diterima' : 'ditolak';
            $title = $newStatus === 'diterima' ? 'Prestasi Diterima (Detail)' : 'Prestasi Ditolak (Detail)';
            
            $message = "Prestasi '{$prestasi->nama_prestasi}' anak Anda {$prestasi->siswa->nama} telah {$statusText} dengan skor rata-rata {$averageScore}/5.0. ";
            $message .= "Kriteria: Kelengkapan ({$criteria['completeness']}/5), Autentisitas ({$criteria['authenticity']}/5), Relevansi ({$criteria['relevance']}/5), Dokumentasi ({$criteria['documentation']}/5).";
            
            \App\Models\Notification::createForParent(
                $prestasi->siswa->wali_id,
                $title,
                $message,
                [
                    'prestasi_id' => $prestasi->id,
                    'siswa_id' => $prestasi->siswa->id,
                    'siswa_nama' => $prestasi->siswa->nama,
                    'prestasi_nama' => $prestasi->nama_prestasi,
                    'action' => 'enhanced_validated',
                    'status' => $newStatus,
                    'criteria_scores' => $criteria,
                    'average_score' => $averageScore
                ]
            );
        }
    }

    /**
     * Get validation history for a specific prestasi
     */
    public function validationHistory(PrestasiSiswa $prestasi_siswa)
    {
        $user = Auth::user();
        $kelasGuru = Kelas::where('id_wali_kelas', $user->id)->pluck('id');
        
        // Validate access
        if (!$kelasGuru->contains($prestasi_siswa->siswa->id_kelas)) {
            abort(403, 'Anda tidak memiliki akses ke prestasi siswa ini.');
        }

        // Get validation history from activity logs
        $history = \App\Models\ActivityLog::where('model_type', 'prestasi_siswa')
            ->where('model_id', $prestasi_siswa->id)
            ->where('action', 'like', '%validasi%')
            ->with('user')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'history' => $history,
            'prestasi' => $prestasi_siswa->load(['siswa', 'kategori'])
        ]);
    }
}
