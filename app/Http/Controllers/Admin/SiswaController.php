<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Siswa::with(['kelas', 'wali']);

        if ($request->filled('kelas')) {
            $query->where('id_kelas', $request->kelas);
        }
        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }
        if ($request->filled('tahun_masuk')) {
            $query->where('tahun_masuk', $request->tahun_masuk);
        }

        $siswa = $query->orderBy('nama')->paginate(10);
        $kelas = Kelas::pluck('nama_kelas', 'id');
        $wali = User::where('role', 'wali')->pluck('nama', 'id');

        return view('admin.siswa.index', compact('siswa', 'kelas', 'wali'));
    }

    // Store siswa baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nisn'           => 'required|string|max:20|unique:siswa',
            'nama'           => 'required|string|max:100',
            'jenis_kelamin'  => 'required|in:L,P',
            'tanggal_lahir'  => 'nullable|date',
            'alamat'         => 'nullable|string|max:255',
            'id_kelas'       => 'required|exists:kelas,id',
            'tahun_masuk'    => 'nullable|string|max:9', // contoh: 2023/2024
            'wali_id'        => 'nullable|exists:users,id',
        ]);

        $s = Siswa::create($validated);

        // Logger: create
        ActivityLogger::log(
            'create',
            'siswa',
            'Tambah siswa: ' . $s->nama . ' (NISN: ' . $s->nisn . ')'
        );

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil ditambah.');
    }

    // Update siswa
    public function update(Request $request, Siswa $siswa)
    {
        $validated = $request->validate([
            'nisn'           => 'required|string|max:20|unique:siswa,nisn,' . $siswa->id,
            'nama'           => 'required|string|max:100',
            'jenis_kelamin'  => 'required|in:L,P',
            'tanggal_lahir'  => 'nullable|date',
            'alamat'         => 'nullable|string|max:255',
            'id_kelas'       => 'required|exists:kelas,id',
            'tahun_masuk'    => 'nullable|string|max:9',
            'wali_id'        => 'nullable|exists:users,id',
        ]);

        $siswa->update($validated);

        // Logger: update
        ActivityLogger::log(
            'update',
            'siswa',
            'Update siswa: ' . $siswa->nama . ' (NISN: ' . $siswa->nisn . ')'
        );

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil diupdate.');
    }

    // Hapus siswa
    public function destroy(Siswa $siswa)
    {
        $desc = $siswa->nama . ' (NISN: ' . $siswa->nisn . ')';
        $siswa->delete();

        // Logger: delete
        ActivityLogger::log(
            'delete',
            'siswa',
            'Hapus siswa: ' . $desc
        );

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil dihapus.');
    }

    public function cetak(Request $request)
    {
        $query = Siswa::with(['kelas', 'wali']);

        if ($request->filled('kelas')) {
            $query->where('id_kelas', $request->kelas);
        }
        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }
        if ($request->filled('tahun_masuk')) {
            $query->where('tahun_masuk', $request->tahun_masuk);
        }

        $siswa = $query->orderBy('nama')->get();

        // Buat view khusus cetak (tanpa modal dsb)
        $pdf = PDF::loadView('admin.siswa.cetak', compact('siswa'));
        return $pdf->stream('daftar-siswa.pdf');
    }
}
