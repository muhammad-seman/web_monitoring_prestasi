<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Ekstrakurikuler;
use Illuminate\Http\Request;

class EkstrakurikulerController extends Controller
{
    // List semua ekskul
    public function index()
    {
        $ekskul = Ekstrakurikuler::paginate(10);
        return view('admin.ekstrakurikuler.index', compact('ekskul'));
    }

    // Store ekskul baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:100|unique:ekstrakurikuler,nama',
            'pembina'   => 'nullable|string|max:100',
            'keterangan'=> 'nullable|string|max:255',
        ]);

        $ek = Ekstrakurikuler::create($validated);
        ActivityLogger::log('create', 'ekstrakurikuler', 'Tambah ekskul: ' . $ek->nama);

        return redirect()->route('admin.ekstrakurikuler.index')->with('success', 'Ekstrakurikuler berhasil ditambah.');
    }

    // Update ekskul
    public function update(Request $request, Ekstrakurikuler $ekstrakurikuler)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:100|unique:ekstrakurikuler,nama,' . $ekstrakurikuler->id,
            'pembina'   => 'nullable|string|max:100',
            'keterangan'=> 'nullable|string|max:255',
        ]);

        $ekstrakurikuler->update($validated);
        ActivityLogger::log('update', 'ekstrakurikuler', 'Update ekskul: ' . $ekstrakurikuler->nama);

        return redirect()->route('admin.ekstrakurikuler.index')->with('success', 'Ekstrakurikuler berhasil diupdate.');
    }

    // Destroy ekskul
    public function destroy(Ekstrakurikuler $ekstrakurikuler)
    {
        $nama = $ekstrakurikuler->nama;
        $ekstrakurikuler->delete();
        ActivityLogger::log('delete', 'ekstrakurikuler', 'Hapus ekskul: ' . $nama);

        return redirect()->route('admin.ekstrakurikuler.index')->with('success', 'Ekstrakurikuler berhasil dihapus.');
    }
}