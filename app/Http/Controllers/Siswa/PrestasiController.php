<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PrestasiSiswa;
use App\Models\KategoriPrestasi;
use App\Models\TingkatPenghargaan;
use App\Models\Ekstrakurikuler;
use App\Models\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

class PrestasiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $siswa = $user->siswa;
        $query = $siswa ? $siswa->prestasi()->with(['kategoriPrestasi', 'tingkatPenghargaan', 'ekskul']) : PrestasiSiswa::query();
        if ($request->filled('kategori')) {
            $query->where('id_kategori_prestasi', $request->kategori);
        }
        if ($request->filled('tingkat')) {
            $query->where('id_tingkat_penghargaan', $request->tingkat);
        }
        if ($request->filled('ekskul')) {
            $query->where('id_ekskul', $request->ekskul);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from')) {
            $query->whereDate('tanggal_prestasi', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('tanggal_prestasi', '<=', $request->to);
        }
        $prestasi = $query->orderByDesc('tanggal_prestasi')->get();
        $kategori = KategoriPrestasi::pluck('nama_kategori', 'id');
        $tingkat = TingkatPenghargaan::pluck('tingkat', 'id');
        $ekskul = Ekstrakurikuler::pluck('nama', 'id');
        return view('siswa.prestasi.index', compact('prestasi', 'kategori', 'tingkat', 'ekskul'));
    }

    public function cetakSurat($id)
    {
        $user = Auth::user();
        $siswa = $user->siswa;
        $prestasi = PrestasiSiswa::where('id', $id)->where('id_siswa', $siswa->id)->with(['kategoriPrestasi', 'tingkatPenghargaan', 'ekskul'])->firstOrFail();
        $pdf = Pdf::loadView('siswa.prestasi.surat', compact('prestasi', 'siswa'));
        return $pdf->stream('surat-pernyataan-prestasi-'.$prestasi->id.'.pdf');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_prestasi' => 'required|string|max:100',
            'id_kategori_prestasi' => 'required|exists:kategori_prestasi,id',
            'id_tingkat_penghargaan' => 'required|exists:tingkat_penghargaan,id',
            'id_ekskul' => 'nullable|exists:ekstrakurikuler,id',
            'penyelenggara' => 'nullable|string|max:100',
            'tanggal_prestasi' => 'nullable|date',
            'keterangan' => 'nullable|string',
            'rata_rata_nilai' => 'nullable|numeric|min:0|max:100',
            'dokumen_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'surat_tugas_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $user = Auth::user();
        $siswa = $user->siswa;

        $data = $request->all();
        $data['id_siswa'] = $siswa->id;
        $data['created_by'] = $user->id;
        $data['status'] = 'draft';

        // Handle dokumen file upload
        if ($request->hasFile('dokumen_file')) {
            $file = $request->file('dokumen_file');
            $filename = time() . '_sertifikat_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads/sertifikat', $filename, 'public');
            $data['dokumen_url'] = 'storage/' . $path;
        }

        // Handle surat tugas file upload
        if ($request->hasFile('surat_tugas_file')) {
            $file = $request->file('surat_tugas_file');
            $filename = time() . '_surat_tugas_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads/surat_tugas', $filename, 'public');
            $data['surat_tugas_url'] = 'storage/' . $path;
        }

        PrestasiSiswa::create($data);

        return redirect()->route('siswa.prestasi.index')->with('success', 'Prestasi berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $siswa = $user->siswa;
        $prestasi = PrestasiSiswa::where('id', $id)->where('id_siswa', $siswa->id)->firstOrFail();

        $request->validate([
            'nama_prestasi' => 'required|string|max:100',
            'id_kategori_prestasi' => 'required|exists:kategori_prestasi,id',
            'id_tingkat_penghargaan' => 'required|exists:tingkat_penghargaan,id',
            'id_ekskul' => 'nullable|exists:ekstrakurikuler,id',
            'penyelenggara' => 'nullable|string|max:100',
            'tanggal_prestasi' => 'nullable|date',
            'keterangan' => 'nullable|string',
            'rata_rata_nilai' => 'nullable|numeric|min:0|max:100',
            'dokumen_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'surat_tugas_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = $request->all();

        // Handle dokumen file upload
        if ($request->hasFile('dokumen_file')) {
            // Delete old file if exists
            if ($prestasi->dokumen_url && file_exists(public_path($prestasi->dokumen_url))) {
                unlink(public_path($prestasi->dokumen_url));
            }
            
            $file = $request->file('dokumen_file');
            $filename = time() . '_sertifikat_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads/sertifikat', $filename, 'public');
            $data['dokumen_url'] = 'storage/' . $path;
        }

        // Handle surat tugas file upload
        if ($request->hasFile('surat_tugas_file')) {
            // Delete old file if exists
            if ($prestasi->surat_tugas_url && file_exists(public_path($prestasi->surat_tugas_url))) {
                unlink(public_path($prestasi->surat_tugas_url));
            }
            
            $file = $request->file('surat_tugas_file');
            $filename = time() . '_surat_tugas_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads/surat_tugas', $filename, 'public');
            $data['surat_tugas_url'] = 'storage/' . $path;
        }

        $prestasi->update($data);

        return redirect()->route('siswa.prestasi.index')->with('success', 'Prestasi berhasil diperbarui');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $siswa = $user->siswa;
        $prestasi = PrestasiSiswa::where('id', $id)->where('id_siswa', $siswa->id)->firstOrFail();

        // Delete associated files
        if ($prestasi->dokumen_url && file_exists(public_path($prestasi->dokumen_url))) {
            unlink(public_path($prestasi->dokumen_url));
        }
        if ($prestasi->surat_tugas_url && file_exists(public_path($prestasi->surat_tugas_url))) {
            unlink(public_path($prestasi->surat_tugas_url));
        }

        $prestasi->delete();

        return redirect()->route('siswa.prestasi.index')->with('success', 'Prestasi berhasil dihapus');
    }

    public function submit($id)
    {
        $user = Auth::user();
        $siswa = $user->siswa;
        $prestasi = PrestasiSiswa::where('id', $id)
            ->where('id_siswa', $siswa->id)
            ->where('status', 'draft')
            ->firstOrFail();

        // Update status to menunggu_validasi
        $prestasi->update(['status' => 'menunggu_validasi']);

        // Send notification to parent if exists
        if ($siswa->wali_id) {
            Notification::createForParent(
                $siswa->wali_id,
                'Prestasi Baru Diajukan',
                "Anak Anda {$siswa->nama} telah mengajukan prestasi '{$prestasi->nama_prestasi}' untuk validasi.",
                [
                    'prestasi_id' => $prestasi->id,
                    'siswa_id' => $siswa->id,
                    'siswa_nama' => $siswa->nama,
                    'prestasi_nama' => $prestasi->nama_prestasi,
                    'action' => 'submitted'
                ]
            );
        }

        return redirect()->route('siswa.prestasi.index')->with('success', 'Prestasi berhasil diajukan untuk validasi');
    }
} 