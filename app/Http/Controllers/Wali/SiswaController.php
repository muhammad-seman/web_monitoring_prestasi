<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Ambil anak-anak dari wali ini dengan relasi lengkap
        $anak = $user->anak()->with([
            'kelas', 
            'ekstrakurikuler', 
            'prestasi' => function($query) {
                $query->with(['kategori', 'tingkat', 'ekskul']);
            }
        ])->get();
        
        return view('wali.siswa.index', compact('anak'));
    }
}
