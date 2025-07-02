<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // Kirim data statistik dsb sesuai kebutuhan dashboard admin
        return view('admin.dashboard');
    }
}
