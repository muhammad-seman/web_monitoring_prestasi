<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $siswa = $user->siswa;
        $prestasi = $siswa ? $siswa->prestasi : collect();
        $total = $prestasi->count();
        $diterima = $prestasi->where('status', 'diterima')->count();
        $pending = $prestasi->where('status', 'menunggu_validasi')->count();
        $ditolak = $prestasi->where('status', 'ditolak')->count();
        $ekskulList = $siswa ? $siswa->ekstrakurikuler : collect();
        return view('siswa.dashboard', compact('siswa', 'total', 'diterima', 'pending', 'ditolak', 'ekskulList'));
    }
} 