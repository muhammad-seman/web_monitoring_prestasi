<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\KategoriPrestasiController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\TingkatPenghargaanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;
// ...tambahkan Controller untuk role lain nanti

use Illuminate\Support\Facades\Route;

// Login & Logout
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin only (prefix dan middleware)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class);
    Route::get('logs', [LogController::class, 'index'])->name('logs.index');
    Route::resource('kelas', KelasController::class);
    Route::resource('siswa', SiswaController::class);
    Route::resource('kategori_prestasi', KategoriPrestasiController::class);
    Route::resource('tingkat_penghargaan', TingkatPenghargaanController::class);
});

// Jika nanti role 'kepala', 'pegawai', dll tinggal tambahkan group serupa:
# Route::middleware(['auth', 'role:kepala'])->prefix('kepala')->name('kepala.')->group(function () {
#     Route::get('/dashboard', [KepalaDashboardController::class, 'index'])->name('dashboard');
#     // dst...
# });

