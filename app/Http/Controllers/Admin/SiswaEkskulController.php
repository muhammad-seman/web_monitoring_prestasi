<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\SiswaEkskul;
use App\Models\Siswa;
use App\Models\Ekstrakurikuler;
use Illuminate\Http\Request;

class SiswaEkskulController extends Controller
{
    /**
     * Display a listing of siswa ekstrakurikuler.
     */
    public function index(Request $request)
    {
        $query = SiswaEkskul::with(['siswa.kelas', 'ekskul'])
            ->orderByDesc('created_at');

        // Filter ekstrakurikuler
        if ($request->filled('ekskul')) {
            $query->where('id_ekskul', $request->ekskul);
        }

        // Filter kelas
        if ($request->filled('kelas')) {
            $query->whereHas('siswa', function($q) use ($request) {
                $q->where('id_kelas', $request->kelas);
            });
        }

        $siswaEkskul = $query->paginate(10)->appends($request->except('page'));

        // Data untuk dropdown filter
        $ekskul = Ekstrakurikuler::pluck('nama', 'id');
        $siswa = Siswa::with('kelas')->orderBy('nama')->get();
        $kelas = \App\Models\Kelas::pluck('nama_kelas', 'id');

        return view('admin.siswa_ekskul.index', compact('siswaEkskul', 'ekskul', 'siswa', 'kelas'));
    }

    /**
     * Store a newly created siswa ekstrakurikuler.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_siswa' => 'required|exists:siswa,id',
            'id_ekskul' => 'required|exists:ekstrakurikuler,id',
        ]);

        // Cek apakah sudah ada data yang sama
        $existing = SiswaEkskul::where('id_siswa', $validated['id_siswa'])
            ->where('id_ekskul', $validated['id_ekskul'])
            ->first();

        if ($existing) {
            return back()->withErrors(['id_siswa' => 'Siswa ini sudah terdaftar di ekstrakurikuler tersebut.'])->withInput();
        }

        $siswaEkskul = SiswaEkskul::create($validated);

        ActivityLogger::log('create', 'siswa_ekskul', 'Tambah siswa ke ekskul: ' . 
            ($siswaEkskul->siswa->nama ?? '-') . ' ke ' . ($siswaEkskul->ekskul->nama ?? '-'));

        return redirect()->route('admin.siswa_ekskul.index')->with('success', 'Siswa berhasil ditambahkan ke ekstrakurikuler.');
    }

    /**
     * Remove the specified siswa ekstrakurikuler.
     */
    public function destroy(SiswaEkskul $siswaEkskul)
    {
        $siswaNama = $siswaEkskul->siswa->nama ?? '-';
        $ekskulNama = $siswaEkskul->ekskul->nama ?? '-';
        
        $siswaEkskul->delete();
        
        ActivityLogger::log('delete', 'siswa_ekskul', 'Hapus siswa dari ekskul: ' . $siswaNama . ' dari ' . $ekskulNama);
        
        return redirect()->route('admin.siswa_ekskul.index')->with('success', 'Siswa berhasil dihapus dari ekstrakurikuler.');
    }
}