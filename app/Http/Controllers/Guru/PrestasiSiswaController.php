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
}
