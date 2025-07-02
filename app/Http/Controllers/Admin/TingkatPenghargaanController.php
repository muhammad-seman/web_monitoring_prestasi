<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\TingkatPenghargaan;
use Illuminate\Http\Request;

class TingkatPenghargaanController extends Controller
{
    // Tampilkan semua tingkat penghargaan
    public function index()
    {
        $tingkat = TingkatPenghargaan::paginate(10);
        return view('admin.tingkat_penghargaan.index', compact('tingkat'));
    }

    // Store
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tingkat' => 'required|string|max:100|unique:tingkat_penghargaan,tingkat',
        ]);

        $tg = TingkatPenghargaan::create($validated);
        ActivityLogger::log('create', 'tingkat_penghargaan', 'Tambah tingkat: ' . $tg->tingkat);

        return redirect()->route('admin.tingkat_penghargaan.index')->with('success', 'Tingkat berhasil ditambah.');
    }

    // Update
    public function update(Request $request, TingkatPenghargaan $tingkat_penghargaan)
    {
        $validated = $request->validate([
            'tingkat' => 'required|string|max:100|unique:tingkat_penghargaan,tingkat,' . $tingkat_penghargaan->id,
        ]);

        $tingkat_penghargaan->update($validated);
        ActivityLogger::log('update', 'tingkat_penghargaan', 'Update tingkat: ' . $tingkat_penghargaan->tingkat);

        return redirect()->route('admin.tingkat_penghargaan.index')->with('success', 'Tingkat berhasil diupdate.');
    }

    // Destroy
    public function destroy(TingkatPenghargaan $tingkat_penghargaan)
    {
        $nama = $tingkat_penghargaan->tingkat;
        $tingkat_penghargaan->delete();
        ActivityLogger::log('delete', 'tingkat_penghargaan', 'Hapus tingkat: ' . $nama);

        return redirect()->route('admin.tingkat_penghargaan.index')->with('success', 'Tingkat berhasil dihapus.');
    }
}