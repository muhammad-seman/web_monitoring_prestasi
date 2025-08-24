<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    // Tampilkan semua kelas
    public function index()
    {
        $kelas = Kelas::with('wali')->paginate(10);
        $wali = User::whereIn('role', ['guru'])->pluck('nama', 'id');
        return view('admin.kelas.index', compact('kelas', 'wali'));
    }

    // Store kelas baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas'    => 'required|string|max:50',
            'id_wali_kelas' => 'nullable|exists:users,id',
            'tahun_ajaran'  => 'required|string|max:20',
        ]);

        // Check for unique combination of nama_kelas and tahun_ajaran
        $exists = Kelas::where('nama_kelas', $request->nama_kelas)
                       ->where('tahun_ajaran', $request->tahun_ajaran)
                       ->exists();

        if ($exists) {
            return back()->withErrors([
                'nama_kelas' => 'Kombinasi nama kelas dan tahun ajaran sudah ada.'
            ])->withInput();
        }

        $validated = $request->only(['nama_kelas', 'id_wali_kelas', 'tahun_ajaran']);

        $kls = Kelas::create($validated);

        // Logger: create
        ActivityLogger::log(
            'create',
            'kelas',
            'Tambah kelas: ' . $kls->nama_kelas
        );

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil ditambah.');
    }

    // Update kelas
    public function update(Request $request, Kelas $kela)
    {
        $request->validate([
            'nama_kelas'    => 'required|string|max:50',
            'id_wali_kelas' => 'nullable|exists:users,id',
            'tahun_ajaran'  => 'required|string|max:20',
        ]);

        // Check for unique combination of nama_kelas and tahun_ajaran, excluding current record
        $exists = Kelas::where('nama_kelas', $request->nama_kelas)
                       ->where('tahun_ajaran', $request->tahun_ajaran)
                       ->where('id', '!=', $kela->id)
                       ->exists();

        if ($exists) {
            return back()->withErrors([
                'nama_kelas' => 'Kombinasi nama kelas dan tahun ajaran sudah ada.'
            ])->withInput();
        }

        $validated = $request->only(['nama_kelas', 'id_wali_kelas', 'tahun_ajaran']);

        $kela->update($validated);

        // Logger: update
        ActivityLogger::log(
            'update',
            'kelas',
            'Update kelas: ' . $kela->nama_kelas
        );

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil diupdate.');
    }

    // Hapus kelas
    public function destroy(Kelas $kela)
    {
        $nama = $kela->nama_kelas;
        $kela->delete();

        // Logger: delete
        ActivityLogger::log(
            'delete',
            'kelas',
            'Hapus kelas: ' . $nama
        );

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }
}