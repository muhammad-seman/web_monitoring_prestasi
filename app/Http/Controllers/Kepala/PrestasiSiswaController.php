<?php

namespace App\Http\Controllers\Kepala;

use App\Http\Controllers\Controller;
use App\Models\PrestasiSiswa;
use App\Models\Kelas;
use App\Models\KategoriPrestasi;
use App\Models\TingkatPenghargaan;
use Illuminate\Http\Request;

class PrestasiSiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = PrestasiSiswa::with(['siswa.kelas', 'kategoriPrestasi', 'tingkatPenghargaan']);
        
        // Filter berdasarkan kelas
        if ($request->filled('kelas_id')) {
            $query->whereHas('siswa', function($q) use ($request) {
                $q->where('id_kelas', $request->kelas_id);
            });
        }
        
        // Filter berdasarkan kategori
        if ($request->filled('kategori_id')) {
            $query->where('id_kategori_prestasi', $request->kategori_id);
        }
        
        // Filter berdasarkan tingkat
        if ($request->filled('tingkat_id')) {
            $query->where('id_tingkat_penghargaan', $request->tingkat_id);
        }
        
        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from')) {
            $query->whereDate('tanggal_prestasi', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('tanggal_prestasi', '<=', $request->to);
        }

        $prestasi = $query->paginate(15);
        $kelas = Kelas::all();
        $kategori = KategoriPrestasi::all();
        $tingkat = TingkatPenghargaan::all();
        
        return view('kepala.prestasi_siswa.index', compact('prestasi', 'kelas', 'kategori', 'tingkat'));
    }
    
    public function cetak(Request $request)
    {
        $query = PrestasiSiswa::with(['siswa.kelas', 'kategoriPrestasi', 'tingkatPenghargaan']);

        if ($request->filled('kelas_id')) {
            $query->whereHas('siswa', function($q) use ($request) {
                $q->where('id_kelas', $request->kelas_id);
            });
        }
        if ($request->filled('kategori_id')) {
            $query->where('id_kategori_prestasi', $request->kategori_id);
        }
        if ($request->filled('tingkat_id')) {
            $query->where('id_tingkat_penghargaan', $request->tingkat_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from')) {
            $query->whereDate('tanggal_prestasi', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('tanggal_prestasi', '<=', $request->to);
        }

        $data['prestasi'] = $query->orderByDesc('tanggal_prestasi')->get();
        $data['kelas'] = \App\Models\Kelas::all();
        $data['kategori'] = \App\Models\KategoriPrestasi::all();
        $data['tingkat'] = \App\Models\TingkatPenghargaan::all();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('kepala.prestasi_siswa.cetak', $data);
        return $pdf->stream('rekap-prestasi-kepala-' . now()->format('Ymd-His') . '.pdf');
    }
    
    public function validasi(Request $request, PrestasiSiswa $prestasi_siswa)
    {
        $request->validate([
            'status' => 'required|in:diterima,ditolak',
            'catatan' => 'nullable|string|max:255'
        ]);
        
        $prestasi_siswa->update([
            'status' => $request->status,
            'alasan_tolak' => $request->catatan,
            'validated_at' => now()
        ]);
        
        return redirect()->back()->with('success', 'Prestasi berhasil divalidasi.');
    }
    
    public function cetakLaporan()
    {
        $prestasi = PrestasiSiswa::with(['siswa.kelas', 'kategoriPrestasi', 'tingkatPenghargaan'])->get();
        
        return view('kepala.laporan.cetak', compact('prestasi'));
    }
    
    public function grafik()
    {
        // Data untuk grafik prestasi
        $prestasiPerBulan = PrestasiSiswa::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();
            
        $prestasiPerKategori = PrestasiSiswa::selectRaw('kategori_prestasi.nama_kategori, COUNT(*) as total')
            ->join('kategori_prestasi', 'prestasi_siswa.id_kategori_prestasi', '=', 'kategori_prestasi.id')
            ->groupBy('kategori_prestasi.id', 'kategori_prestasi.nama_kategori')
            ->get();
            
        return view('kepala.laporan.grafik', compact('prestasiPerBulan', 'prestasiPerKategori'));
    }
    
    public function laporanBulanan()
    {
        $bulan = request('bulan', now()->format('Y-m'));
        $prestasi = PrestasiSiswa::with(['siswa.kelas', 'kategoriPrestasi', 'tingkatPenghargaan'])
            ->whereYear('created_at', substr($bulan, 0, 4))
            ->whereMonth('created_at', substr($bulan, 5, 2))
            ->get();
            
        return view('kepala.laporan.bulanan', compact('prestasi', 'bulan'));
    }
    
    public function laporanTahunan()
    {
        $tahun = request('tahun', now()->year);
        $prestasi = PrestasiSiswa::with(['siswa.kelas', 'kategoriPrestasi', 'tingkatPenghargaan'])
            ->whereYear('created_at', $tahun)
            ->get();
            
        return view('kepala.laporan.tahunan', compact('prestasi', 'tahun'));
    }
} 