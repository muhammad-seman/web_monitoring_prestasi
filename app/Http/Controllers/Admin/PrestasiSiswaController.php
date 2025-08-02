<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\PrestasiSiswa;
use App\Models\Siswa;
use App\Models\KategoriPrestasi;
use App\Models\TingkatPenghargaan;
use App\Models\Ekstrakurikuler;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

class PrestasiSiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = PrestasiSiswa::with(['siswa', 'kategori', 'tingkat', 'ekskul', 'creator', 'validator'])
            ->orderByDesc('created_at');

        // FILTER KATEGORI
        if ($request->filled('kategori')) {
            $query->where('id_kategori_prestasi', $request->kategori);
        }
        // FILTER TINGKAT
        if ($request->filled('tingkat')) {
            $query->where('id_tingkat_penghargaan', $request->tingkat);
        }
        // FILTER RANGE TANGGAL
        if ($request->filled('from')) {
            $query->whereDate('tanggal_prestasi', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('tanggal_prestasi', '<=', $request->to);
        }

        $prestasi = $query->paginate(10)->appends($request->except('page')); // biar pagination tetap bawa filter

        $siswa    = Siswa::pluck('nama', 'id');
        $kategori = KategoriPrestasi::pluck('nama_kategori', 'id');
        $tingkat  = TingkatPenghargaan::pluck('tingkat', 'id');
        $ekskul   = Ekstrakurikuler::pluck('nama', 'id');

        return view('admin.prestasi_siswa.index', compact('prestasi', 'siswa', 'kategori', 'tingkat', 'ekskul'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_siswa'                => 'required|exists:siswa,id',
            'id_kategori_prestasi'    => 'required|exists:kategori_prestasi,id',
            'id_tingkat_penghargaan'  => 'required|exists:tingkat_penghargaan,id',
            'id_ekskul'               => 'nullable|exists:ekstrakurikuler,id',
            'nama_prestasi'           => 'required|string|max:100',
            'penyelenggara'           => 'required|string|max:100',
            'tanggal_prestasi'        => 'required|date',
            'keterangan'              => 'nullable|string|max:255',
            'dokumen_file'            => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // max 2MB
            'status'                  => 'required|in:draft,menunggu_validasi,diterima,ditolak',
            'rata_rata_nilai'         => 'nullable|numeric',
            'alasan_tolak'            => 'nullable|string|max:255',
        ]);

        // Upload file jika ada
        if ($request->hasFile('dokumen_file')) {
            $file = $request->file('dokumen_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads/sertifikat', $filename, 'public');
            $validated['dokumen_url'] = 'storage/' . $path;
        }

        $validated['created_by'] = Auth::id() ?? 1;

        // Set validasi jika status diterima/ditolak
        if (in_array($validated['status'], ['diterima', 'ditolak'])) {
            $validated['validated_by'] = Auth::id() ?? 1;
            $validated['validated_at'] = \Illuminate\Support\Carbon::now();
        } else {
            $validated['validated_by'] = null;
            $validated['validated_at'] = null;
        }

        $prestasi = PrestasiSiswa::create($validated);

        ActivityLogger::log('create', 'prestasi_siswa', 'Tambah prestasi: ' . $prestasi->nama_prestasi . ' oleh ' . ($prestasi->siswa->nama ?? '-'));
        return redirect()->route('admin.prestasi_siswa.index')->with('success', 'Prestasi siswa berhasil ditambah.');
    }

    public function update(Request $request, PrestasiSiswa $prestasi_siswa)
    {
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
            'status'                  => 'required|in:draft,menunggu_validasi,diterima,ditolak',
            'rata_rata_nilai'         => 'nullable|numeric',
            'alasan_tolak'            => 'nullable|string|max:255',
        ]);

        // Upload file baru jika ada
        if ($request->hasFile('dokumen_file')) {
            // Hapus file lama (opsional, supaya storage gak numpuk)
            if ($prestasi_siswa->dokumen_url && file_exists(public_path($prestasi_siswa->dokumen_url))) {
                @unlink(public_path($prestasi_siswa->dokumen_url));
            }

            $file = $request->file('dokumen_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads/sertifikat', $filename, 'public');
            $validated['dokumen_url'] = 'storage/' . $path;
        }

        // Update validasi hanya jika status diterima/ditolak
        if (in_array($validated['status'], ['diterima', 'ditolak'])) {
            $validated['validated_by'] = Auth::id() ?? 1;
            $validated['validated_at'] = \Illuminate\Support\Carbon::now();
        } else {
            $validated['validated_by'] = null;
            $validated['validated_at'] = null;
        }

        // Store old status for comparison
        $oldStatus = $prestasi_siswa->status;
        
        $prestasi_siswa->update($validated);

        // Send notification to parent if status changed to accepted or rejected
        if ($oldStatus !== $validated['status'] && in_array($validated['status'], ['diterima', 'ditolak']) && $prestasi_siswa->siswa->wali_id) {
            $statusText = $validated['status'] === 'diterima' ? 'diterima' : 'ditolak';
            $title = $validated['status'] === 'diterima' ? 'Prestasi Diterima' : 'Prestasi Ditolak';
            $message = "Prestasi '{$prestasi_siswa->nama_prestasi}' anak Anda {$prestasi_siswa->siswa->nama} telah {$statusText}.";
            
            if ($validated['status'] === 'ditolak' && !empty($validated['alasan_tolak'])) {
                $message .= " Alasan: {$validated['alasan_tolak']}";
            }
            
            Notification::createForParent(
                $prestasi_siswa->siswa->wali_id,
                $title,
                $message,
                [
                    'prestasi_id' => $prestasi_siswa->id,
                    'siswa_id' => $prestasi_siswa->siswa->id,
                    'siswa_nama' => $prestasi_siswa->siswa->nama,
                    'prestasi_nama' => $prestasi_siswa->nama_prestasi,
                    'action' => 'validated',
                    'status' => $validated['status']
                ]
            );
        }

        // Send notification to student if status changed to accepted or rejected
        if ($oldStatus !== $validated['status'] && in_array($validated['status'], ['diterima', 'ditolak']) && $prestasi_siswa->siswa->user_id) {
            $statusText = $validated['status'] === 'diterima' ? 'diterima' : 'ditolak';
            $title = $validated['status'] === 'diterima' ? 'Prestasi Diterima' : 'Prestasi Ditolak';
            $message = "Prestasi '{$prestasi_siswa->nama_prestasi}' yang Anda ajukan telah {$statusText}.";
            
            if ($validated['status'] === 'ditolak' && !empty($validated['alasan_tolak'])) {
                $message .= " Alasan: {$validated['alasan_tolak']}";
            }
            
            Notification::create([
                'user_id' => $prestasi_siswa->siswa->user_id,
                'title' => $title,
                'message' => $message,
                'data' => json_encode([
                    'prestasi_id' => $prestasi_siswa->id,
                    'prestasi_nama' => $prestasi_siswa->nama_prestasi,
                    'action' => 'validated',
                    'status' => $validated['status']
                ]),
                'read_at' => null
            ]);
        }

        ActivityLogger::log('update', 'prestasi_siswa', 'Update prestasi: ' . $prestasi_siswa->nama_prestasi . ' oleh ' . ($prestasi_siswa->siswa->nama ?? '-'));
        return redirect()->route('admin.prestasi_siswa.index')->with('success', 'Prestasi siswa berhasil diupdate.');
    }

    /**
     * Validasi prestasi siswa yang dibuat oleh guru (untuk akses admin).
     */
    public function validasiGuru(Request $request, PrestasiSiswa $prestasi_siswa)
    {
        // Validasi bahwa prestasi ini dibuat oleh guru (bukan admin)
        $creator = $prestasi_siswa->creator;
        if (!$creator || $creator->role !== 'guru') {
            return back()->with('error', 'Prestasi ini tidak dibuat oleh guru.');
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
            'validated_by' => Auth::id()
        ]);
        
        // Send notification to parent if status changed to accepted or rejected
        if ($oldStatus !== $request->status && $prestasi_siswa->siswa->wali_id) {
            $statusText = $request->status === 'diterima' ? 'diterima' : 'ditolak';
            $title = $request->status === 'diterima' ? 'Prestasi Diterima' : 'Prestasi Ditolak';
            $message = "Prestasi '{$prestasi_siswa->nama_prestasi}' anak Anda {$prestasi_siswa->siswa->nama} telah {$statusText} oleh admin.";
            
            if ($request->status === 'ditolak' && !empty($request->alasan_tolak)) {
                $message .= " Alasan: {$request->alasan_tolak}";
            }
            
            Notification::createForParent(
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
            $message = "Prestasi '{$prestasi_siswa->nama_prestasi}' yang diajukan telah {$statusText} oleh admin.";
            
            if ($request->status === 'ditolak' && !empty($request->alasan_tolak)) {
                $message .= " Alasan: {$request->alasan_tolak}";
            }
            
            Notification::create([
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

        // Send notification to guru (creator) about validation result
        if ($oldStatus !== $request->status && $creator) {
            $statusText = $request->status === 'diterima' ? 'diterima' : 'ditolak';
            $title = $request->status === 'diterima' ? 'Prestasi Diterima Admin' : 'Prestasi Ditolak Admin';
            $message = "Prestasi '{$prestasi_siswa->nama_prestasi}' yang Anda buat untuk siswa {$prestasi_siswa->siswa->nama} telah {$statusText} oleh admin.";
            
            if ($request->status === 'ditolak' && !empty($request->alasan_tolak)) {
                $message .= " Alasan: {$request->alasan_tolak}";
            }
            
            Notification::create([
                'user_id' => $creator->id,
                'title' => $title,
                'message' => $message,
                'data' => json_encode([
                    'prestasi_id' => $prestasi_siswa->id,
                    'prestasi_nama' => $prestasi_siswa->nama_prestasi,
                    'siswa_nama' => $prestasi_siswa->siswa->nama,
                    'action' => 'admin_validated',
                    'status' => $request->status
                ]),
                'read_at' => null
            ]);
        }
        
        ActivityLogger::log('update', 'prestasi_siswa', 'Admin validasi prestasi guru: ' . $prestasi_siswa->nama_prestasi . ' status: ' . $request->status);
        return redirect()->back()->with('success', 'Prestasi guru berhasil divalidasi.');
    }

    public function destroy(PrestasiSiswa $prestasi_siswa)
    {
        $desc = $prestasi_siswa->nama_prestasi . ' (' . ($prestasi_siswa->siswa->nama ?? '-') . ')';
        $prestasi_siswa->delete();
        ActivityLogger::log('delete', 'prestasi_siswa', 'Hapus prestasi: ' . $desc);
        return redirect()->route('admin.prestasi_siswa.index')->with('success', 'Prestasi siswa berhasil dihapus.');
    }

    public function cetak(Request $request)
    {
        $query = PrestasiSiswa::with(['siswa', 'kategori', 'tingkat']);

        if ($request->filled('kategori')) {
            $query->where('id_kategori_prestasi', $request->kategori);
        }
        if ($request->filled('tingkat')) {
            $query->where('id_tingkat_penghargaan', $request->tingkat);
        }
        if ($request->filled('from')) {
            $query->whereDate('tanggal_prestasi', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('tanggal_prestasi', '<=', $request->to);
        }

        $data['prestasi'] = $query->orderByDesc('tanggal_prestasi')->get();

        $pdf = Pdf::loadView('admin.prestasi_siswa.cetak', $data);
        return $pdf->stream('rekap-prestasi-' . now()->format('Ymd-His') . '.pdf');
    }
}
