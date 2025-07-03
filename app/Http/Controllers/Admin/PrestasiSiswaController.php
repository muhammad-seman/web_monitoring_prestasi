<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\PrestasiSiswa;
use App\Models\Siswa;
use App\Models\KategoriPrestasi;
use App\Models\TingkatPenghargaan;
use App\Models\Ekstrakurikuler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class PrestasiSiswaController extends Controller
{
    public function index()
    {
        $prestasi = PrestasiSiswa::with(['siswa', 'kategori', 'tingkat', 'ekskul', 'creator', 'validator'])->orderByDesc('created_at')->paginate(10);
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

        $prestasi_siswa->update($validated);

        ActivityLogger::log('update', 'prestasi_siswa', 'Update prestasi: ' . $prestasi_siswa->nama_prestasi . ' oleh ' . ($prestasi_siswa->siswa->nama ?? '-'));
        return redirect()->route('admin.prestasi_siswa.index')->with('success', 'Prestasi siswa berhasil diupdate.');
    }

    public function destroy(PrestasiSiswa $prestasi_siswa)
    {
        $desc = $prestasi_siswa->nama_prestasi . ' (' . ($prestasi_siswa->siswa->nama ?? '-') . ')';
        $prestasi_siswa->delete();
        ActivityLogger::log('delete', 'prestasi_siswa', 'Hapus prestasi: ' . $desc);
        return redirect()->route('admin.prestasi_siswa.index')->with('success', 'Prestasi siswa berhasil dihapus.');
    }
}
