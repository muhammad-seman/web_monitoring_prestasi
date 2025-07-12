<?php

namespace App\Http\Controllers\Kepala;

use App\Http\Controllers\Controller;
use App\Models\Ekstrakurikuler;
use App\Models\PrestasiSiswa;
use Illuminate\Http\Request;

class EkstrakurikulerController extends Controller
{
    public function index()
    {
        $ekstrakurikuler = Ekstrakurikuler::withCount('siswa')->with('siswa.kelas')->paginate(15);
        return view('kepala.ekstrakurikuler.index', compact('ekstrakurikuler'));
    }
    

    
    public function prestasiEkskul(Ekstrakurikuler $ekstrakurikuler)
    {
        $prestasi = PrestasiSiswa::where('id_ekskul', $ekstrakurikuler->id)
            ->with(['siswa', 'kategoriPrestasi', 'tingkatPenghargaan'])
            ->paginate(15);
        
        return view('kepala.ekstrakurikuler.prestasi', compact('ekstrakurikuler', 'prestasi'));
    }
} 