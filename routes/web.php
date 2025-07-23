<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EkstrakurikulerController;
use App\Http\Controllers\Admin\KategoriPrestasiController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\PrestasiSiswaController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\TingkatPenghargaanController;
use App\Http\Controllers\Admin\SiswaEkskulController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\NotificationController;

// Guru Controllers
use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\Guru\SiswaController as GuruSiswaController;
use App\Http\Controllers\Guru\PrestasiSiswaController as GuruPrestasiSiswaController;
use App\Http\Controllers\Guru\EkstrakurikulerController as GuruEkstrakurikulerController;
use App\Http\Controllers\Guru\KategoriPrestasiController as GuruKategoriPrestasiController;
use App\Http\Controllers\Guru\TingkatPenghargaanController as GuruTingkatPenghargaanController;
use App\Http\Controllers\Guru\KelasController as GuruKelasController;

// Kepala Sekolah Controllers
use App\Http\Controllers\Kepala\DashboardController as KepalaDashboardController;
use App\Http\Controllers\Kepala\PrestasiSiswaController as KepalaPrestasiSiswaController;
use App\Http\Controllers\Kepala\SiswaController as KepalaSiswaController;
use App\Http\Controllers\Kepala\KelasController as KepalaKelasController;
use App\Http\Controllers\Kepala\EkstrakurikulerController as KepalaEkstrakurikulerController;
use App\Http\Controllers\Kepala\UserController as KepalaUserController;
use App\Http\Controllers\Kepala\LogController as KepalaLogController;
use App\Http\Controllers\Kepala\NotifikasiController as KepalaNotifikasiController;

// Wali Controllers
use App\Http\Controllers\Wali\DashboardController as WaliDashboardController;
use App\Http\Controllers\Wali\SiswaController as WaliSiswaController;
use App\Http\Controllers\Wali\PrestasiController as WaliPrestasiController;
use App\Http\Controllers\Wali\DokumenController as WaliDokumenController;

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

// Login & Logout
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Notification routes (for all authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::get('/notifications/count', [NotificationController::class, 'getCount'])->name('notifications.count');
});

// Admin only (prefix dan middleware)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class);
    Route::get('logs', [LogController::class, 'index'])->name('logs.index');
    Route::resource('kelas', KelasController::class);
    Route::resource('siswa', SiswaController::class)->except('show');
    Route::resource('kategori_prestasi', KategoriPrestasiController::class);
    Route::resource('tingkat_penghargaan', TingkatPenghargaanController::class);
    Route::resource('ekstrakurikuler', EkstrakurikulerController::class);
    Route::resource('siswa_ekskul', SiswaEkskulController::class)->only(['index', 'store', 'destroy']);
    Route::resource('prestasi_siswa', PrestasiSiswaController::class)->except('show');

    Route::get('prestasi_siswa/cetak', [PrestasiSiswaController::class, 'cetak'])->name('prestasi_siswa.cetak');
    Route::get('siswa/cetak', [SiswaController::class, 'cetak'])->name('siswa.cetak');
});

// Guru only (prefix dan middleware)
Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    // Dashboard: Lihat statistik kelas/seksi yang diampu
    Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');

    // Siswa: Lihat daftar siswa di kelasnya
    Route::get('siswa', [GuruSiswaController::class, 'index'])->name('siswa.index');
    Route::get('siswa/cetak', [GuruSiswaController::class, 'cetak'])->name('siswa.cetak');

    // Prestasi Siswa: Tambah, Edit, Lihat, Validasi, Upload Dokumen
    Route::get('prestasi_siswa', [GuruPrestasiSiswaController::class, 'index'])->name('prestasi_siswa.index');
    Route::get('prestasi_siswa/create', [GuruPrestasiSiswaController::class, 'create'])->name('prestasi_siswa.create');
    Route::post('prestasi_siswa', [GuruPrestasiSiswaController::class, 'store'])->name('prestasi_siswa.store');
    Route::get('prestasi_siswa/{prestasi_siswa}/edit', [GuruPrestasiSiswaController::class, 'edit'])->name('prestasi_siswa.edit');
    Route::put('prestasi_siswa/{prestasi_siswa}', [GuruPrestasiSiswaController::class, 'update'])->name('prestasi_siswa.update');
    // Route::get('prestasi_siswa/{prestasi_siswa}', [GuruPrestasiSiswaController::class, 'show'])->name('prestasi_siswa.show');
    // Upload dokumen bukti
    Route::post('prestasi_siswa/{prestasi_siswa}/upload', [GuruPrestasiSiswaController::class, 'uploadDokumen'])->name('prestasi_siswa.upload');
    // Cetak rekap prestasi siswa di kelasnya
    Route::get('prestasi_siswa/cetak', [GuruPrestasiSiswaController::class, 'cetak'])->name('prestasi_siswa.cetak');

    // Ekstrakurikuler: Lihat info ekstrakurikuler, siswa peserta di kelasnya
    Route::get('ekstrakurikuler', [GuruEkstrakurikulerController::class, 'index'])->name('ekstrakurikuler.index');
    Route::get('ekstrakurikuler/{ekstrakurikuler}', [GuruEkstrakurikulerController::class, 'show'])->name('ekstrakurikuler.show');

    // Kategori Prestasi: Lihat daftar kategori
    Route::get('kategori_prestasi', [GuruKategoriPrestasiController::class, 'index'])->name('kategori_prestasi.index');

    // Tingkat Penghargaan: Lihat level penghargaan
    Route::get('tingkat_penghargaan', [GuruTingkatPenghargaanController::class, 'index'])->name('tingkat_penghargaan.index');

    // Kelas: Lihat detail kelas yang diampu
    Route::get('kelas', [GuruKelasController::class, 'index'])->name('kelas.index');
    Route::get('kelas/{kelas}', [GuruKelasController::class, 'show'])->name('kelas.show');
});

// Kepala Sekolah only (prefix dan middleware)
Route::middleware(['auth', 'role:kepala_sekolah'])->prefix('kepala')->name('kepala_sekolah.')->group(function () {
    // Dashboard: Lihat statistik global seluruh sekolah
    Route::get('/dashboard', [KepalaDashboardController::class, 'index'])->name('dashboard');

    // Rekap Prestasi: Lihat seluruh data prestasi siswa, filter berdasarkan kelas/kategori/tingkat/periode
    Route::get('prestasi_siswa', [KepalaPrestasiSiswaController::class, 'index'])->name('prestasi_siswa.index');
    Route::get('prestasi_siswa/cetak', [KepalaPrestasiSiswaController::class, 'cetak'])->name('prestasi_siswa.cetak');
    Route::post('prestasi_siswa/{prestasi_siswa}/validasi', [KepalaPrestasiSiswaController::class, 'validasi'])->name('prestasi_siswa.validasi');

    // Siswa: Lihat daftar seluruh siswa, detail prestasi, progres
    Route::get('siswa', [KepalaSiswaController::class, 'index'])->name('siswa.index');
    Route::get('siswa/cetak', [KepalaSiswaController::class, 'cetak'])->name('siswa.cetak');

    // Kelas: Lihat daftar kelas, wali kelas, dan rekap prestasi per kelas
    Route::get('kelas', [KepalaKelasController::class, 'index'])->name('kelas.index');
    Route::get('kelas/{kelas}', [KepalaKelasController::class, 'show'])->name('kelas.show');
    Route::get('kelas/{kelas}/prestasi', [KepalaKelasController::class, 'prestasiKelas'])->name('kelas.prestasi');

    // Ekstrakurikuler: Lihat daftar ekskul dan anggota, rekap prestasi ekskul
    Route::get('ekstrakurikuler', [KepalaEkstrakurikulerController::class, 'index'])->name('ekstrakurikuler.index');
    Route::get('ekstrakurikuler/{ekstrakurikuler}/prestasi', [KepalaEkstrakurikulerController::class, 'prestasiEkskul'])->name('ekstrakurikuler.prestasi');

    // Users: Melihat daftar user (admin, guru, siswa, wali), tanpa akses edit/delete
    Route::get('users', [KepalaUserController::class, 'index'])->name('users.index');

    // Log Aktivitas: Lihat history aktivitas penting terkait validasi/prestasi
    Route::get('logs', [KepalaLogController::class, 'index'])->name('logs.index');
});

// Wali only (prefix dan middleware)
Route::middleware(['auth', 'role:wali'])->prefix('wali')->name('wali.')->group(function () {
    // Dashboard: Ringkasan prestasi anak-anak
    Route::get('/dashboard', [WaliDashboardController::class, 'index'])->name('dashboard');

    // Data Anak: Lihat biodata anak-anak
    Route::get('siswa', [WaliSiswaController::class, 'index'])->name('siswa.index');

    // Prestasi Anak: Lihat daftar prestasi anak-anak
    Route::get('prestasi', [WaliPrestasiController::class, 'index'])->name('prestasi.index');
    Route::get('prestasi/cetak', [WaliPrestasiController::class, 'cetak'])->name('prestasi.cetak');

    // Dokumen Prestasi: Lihat dan download dokumen
    Route::get('dokumen', [WaliDokumenController::class, 'index'])->name('dokumen.index');
    Route::get('dokumen/{id}/download', [WaliDokumenController::class, 'download'])->name('dokumen.download');
});

// Siswa only (prefix dan middleware)
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    // Dashboard: Ringkasan prestasi & data diri
    Route::get('/dashboard', [\App\Http\Controllers\Siswa\DashboardController::class, 'index'])->name('dashboard');

    // Data Diri: Lihat & update biodata
    Route::get('profil', [\App\Http\Controllers\Siswa\ProfilController::class, 'index'])->name('profil.index');
    Route::put('profil', [\App\Http\Controllers\Siswa\ProfilController::class, 'update'])->name('profil.update');

    // Prestasi Siswa: Lihat, tambah, edit, hapus (semua modal, tidak ada show/create/edit route)
    Route::get('prestasi', [\App\Http\Controllers\Siswa\PrestasiController::class, 'index'])->name('prestasi.index');
    Route::post('prestasi', [\App\Http\Controllers\Siswa\PrestasiController::class, 'store'])->name('prestasi.store');
    Route::put('prestasi/{prestasi}', [\App\Http\Controllers\Siswa\PrestasiController::class, 'update'])->name('prestasi.update');
    Route::delete('prestasi/{prestasi}', [\App\Http\Controllers\Siswa\PrestasiController::class, 'destroy'])->name('prestasi.destroy');
    Route::get('prestasi/{prestasi}/cetak', [\App\Http\Controllers\Siswa\PrestasiController::class, 'cetakSurat'])->name('prestasi.cetakSurat');
    Route::post('prestasi/{prestasi}/submit', [\App\Http\Controllers\Siswa\PrestasiController::class, 'submit'])->name('prestasi.submit');

    // Dokumen Prestasi: Upload, hapus
    // Route::post('prestasi/{prestasi}/dokumen', [\App\Http\Controllers\Siswa\DokumenPrestasiController::class, 'store'])->name('prestasi.dokumen.store');
    // Route::delete('prestasi/{prestasi}/dokumen/{dokumen}', [\App\Http\Controllers\Siswa\DokumenPrestasiController::class, 'destroy'])->name('prestasi.dokumen.destroy');

    // Cetak rekap prestasi (filter di index, tidak ada menu terpisah)
    Route::get('prestasi/cetak', [\App\Http\Controllers\Siswa\PrestasiController::class, 'cetak'])->name('prestasi.cetak');
});

// Jika nanti role 'wali', 'siswa', dll tinggal tambahkan group serupa:
# Route::middleware(['auth', 'role:wali'])->prefix('wali')->name('wali.')->group(function () {
#     Route::get('/dashboard', [WaliDashboardController::class, 'index'])->name('dashboard');
#     // dst...
# });
