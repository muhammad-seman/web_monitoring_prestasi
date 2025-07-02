<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\KategoriPrestasi;
use Illuminate\Http\Request;

class KategoriPrestasiController extends Controller
{
    // Tampilkan semua kategori
    public function index()
    {
        $kategori = KategoriPrestasi::paginate(10);
        return view('admin.kategori_prestasi.index', compact('kategori'));
    }

    // Store kategori baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori_prestasi,nama_kategori',
            'deskripsi'     => 'nullable|string|max:255',
        ]);

        $kat = KategoriPrestasi::create($validated);

        ActivityLogger::log('create', 'kategori_prestasi', 'Tambah kategori: ' . $kat->nama_kategori);
        return redirect()->route('admin.kategori_prestasi.index')->with('success', 'Kategori berhasil ditambah.');
    }

    // Update kategori
    public function update(Request $request, KategoriPrestasi $kategori_prestasi)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori_prestasi,nama_kategori,' . $kategori_prestasi->id,
            'deskripsi'     => 'nullable|string|max:255',
        ]);

        $kategori_prestasi->update($validated);

        ActivityLogger::log('update', 'kategori_prestasi', 'Update kategori: ' . $kategori_prestasi->nama_kategori);
        return redirect()->route('admin.kategori_prestasi.index')->with('success', 'Kategori berhasil diupdate.');
    }

    // Hapus kategori
    public function destroy(KategoriPrestasi $kategori_prestasi)
    {
        $nama = $kategori_prestasi->nama_kategori;
        $kategori_prestasi->delete();

        ActivityLogger::log('delete', 'kategori_prestasi', 'Hapus kategori: ' . $nama);
        return redirect()->route('admin.kategori_prestasi.index')->with('success', 'Kategori berhasil dihapus.');
    }
}