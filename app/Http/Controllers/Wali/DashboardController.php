<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PrestasiSiswa;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Ambil anak-anak dari wali ini
        $anak = $user->anak()->with(['kelas', 'prestasi'])->get();
        
        // Statistik prestasi anak-anak
        $totalPrestasi = 0;
        $prestasiDiterima = 0;
        $prestasiMenunggu = 0;
        $prestasiDitolak = 0;
        
        foreach ($anak as $siswa) {
            $totalPrestasi += $siswa->prestasi->count();
            $prestasiDiterima += $siswa->prestasi->where('status', 'diterima')->count();
            $prestasiMenunggu += $siswa->prestasi->whereIn('status', ['draft', 'menunggu_validasi'])->count();
            $prestasiDitolak += $siswa->prestasi->where('status', 'ditolak')->count();
        }
        
        // Prestasi terbaru (5 terakhir)
        $prestasiTerbaru = PrestasiSiswa::whereIn('id_siswa', $anak->pluck('id'))
            ->with(['siswa', 'kategori', 'tingkat', 'ekskul'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('wali.dashboard', compact(
            'anak', 
            'totalPrestasi', 
            'prestasiDiterima', 
            'prestasiMenunggu', 
            'prestasiDitolak',
            'prestasiTerbaru'
        ));
    }
}
