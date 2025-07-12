<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // Kirim data statistik dsb sesuai kebutuhan dashboard guru
        return view('guru.dashboard');
    }
}
