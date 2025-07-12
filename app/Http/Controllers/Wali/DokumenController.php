<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PrestasiSiswa;
use App\Models\DokumenPrestasi;

class DokumenController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Ambil ID anak-anak dari wali ini
        $anakIds = $user->anak()->pluck('id');
        
        $query = PrestasiSiswa::whereIn('id_siswa', $anakIds)
            ->whereNotNull('dokumen_url')
            ->with(['siswa', 'kategori', 'tingkat', 'ekskul'])
            ->orderByDesc('created_at');

        // Filter berdasarkan kategori
        if ($request->filled('kategori')) {
            $query->where('id_kategori_prestasi', $request->kategori);
        }
        
        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter berdasarkan anak
        if ($request->filled('anak')) {
            $query->where('id_siswa', $request->anak);
        }

        $prestasi = $query->paginate(10)->appends($request->except('page'));
        
        return view('wali.dokumen.index', compact('prestasi'));
    }
    
    public function download($id)
    {
        $user = Auth::user();
        $anakIds = $user->anak()->pluck('id');
        
        $prestasi = PrestasiSiswa::whereIn('id_siswa', $anakIds)
            ->where('id', $id)
            ->firstOrFail();
            
        if (!$prestasi->dokumen_url || !file_exists(public_path($prestasi->dokumen_url))) {
            return back()->with('error', 'Dokumen tidak ditemukan.');
        }
        
        return response()->download(public_path($prestasi->dokumen_url));
    }
}
