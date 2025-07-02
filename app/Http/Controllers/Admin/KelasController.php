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
        $validated = $request->validate([
            'nama_kelas'    => 'required|string|max:50',
            'id_wali_kelas' => 'nullable|exists:users,id',
            'tahun_ajaran'  => 'required|string|max:20',
        ]);

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
        $validated = $request->validate([
            'nama_kelas'    => 'required|string|max:50',
            'id_wali_kelas' => 'nullable|exists:users,id',
            'tahun_ajaran'  => 'required|string|max:20',
        ]);

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