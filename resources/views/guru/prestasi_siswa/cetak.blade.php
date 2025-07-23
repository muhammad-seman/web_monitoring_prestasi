@extends('layouts.letterhead', [
    'title' => 'Rekap Prestasi Siswa - Guru',
    'date' => 'Barabai, ' . \Carbon\Carbon::now()->format('d F Y'),
    'letterType' => 'Wali Kelas',
    'signatureName' => 'Wali Kelas',
    'signatureTitle' => auth()->user()->nama ?? 'Guru'
])

@section('content')
<div style="text-align: center; margin-bottom: 20px;">
    <h3 style="text-decoration: underline; font-size: 16px; margin-bottom: 15px;">REKAP PRESTASI SISWA</h3>
    <p style="font-size: 12px; margin-bottom: 10px;">Kelas yang Diampu</p>
</div>

<div style="margin-bottom: 15px; font-size: 12px;">
    @if(isset($kelas) && $kelas->count() > 0)
        <strong>Kelas yang diampu:</strong>
        @foreach($kelas as $kls)
            {{ $kls->nama_kelas }}{{ !$loop->last ? ', ' : '' }}
        @endforeach
    @else
        <strong>Kelas:</strong> Tidak ada kelas yang diampu
    @endif
</div>

@if(request('kategori') || request('from') || request('to') || request('status'))
<div style="margin-bottom: 15px; font-size: 11px;">
    <strong>Filter yang diterapkan:</strong><br>
    @if(request('kategori'))
        • Kategori: {{ \App\Models\KategoriPrestasi::find(request('kategori'))->nama_kategori ?? '-' }}<br>
    @endif
    @if(request('from') || request('to'))
        • Periode: {{ request('from') ? \Carbon\Carbon::parse(request('from'))->format('d F Y') : 'Awal' }} 
        s/d {{ request('to') ? \Carbon\Carbon::parse(request('to'))->format('d F Y') : 'Akhir' }}<br>
    @endif
    @if(request('status'))
        • Status: {{ ucwords(str_replace('_', ' ', request('status'))) }}<br>
    @endif
</div>
@endif

<table>
    <thead>
        <tr>
            <th style="width: 4%; text-align: center;">No</th>
            <th style="width: 15%;">Nama Siswa</th>
            <th style="width: 20%;">Nama Prestasi</th>
            <th style="width: 12%;">Kategori</th>
            <th style="width: 10%;">Tingkat</th>
            <th style="width: 13%;">Penyelenggara</th>
            <th style="width: 10%;">Tanggal</th>
            <th style="width: 8%;">Status</th>
            <th style="width: 8%;">Nilai</th>
        </tr>
    </thead>
    <tbody>
        @forelse($prestasi as $i => $p)
        <tr>
            <td style="text-align: center;">{{ $i + 1 }}</td>
            <td>{{ $p->siswa->nama ?? '-' }}</td>
            <td>{{ $p->nama_prestasi }}</td>
            <td>{{ $p->kategoriPrestasi->nama_kategori ?? '-' }}</td>
            <td>{{ $p->tingkatPenghargaan->tingkat ?? '-' }}</td>
            <td style="font-size: 10px;">{{ $p->penyelenggara ?? '-' }}</td>
            <td>{{ $p->tanggal_prestasi ? \Carbon\Carbon::parse($p->tanggal_prestasi)->format('d/m/Y') : '-' }}</td>
            <td style="font-size: 10px;">{{ ucwords(str_replace('_', ' ', $p->status)) }}</td>
            <td style="text-align: center;">{{ $p->rata_rata_nilai ?? '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="9" style="text-align: center; font-style: italic;">Belum ada data prestasi siswa di kelas Anda</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div style="margin-top: 20px; font-size: 11px;">
    <table style="width: 50%; border: none;">
        <tr>
            <td style="border: none; padding: 2px;"><strong>Total Prestasi:</strong></td>
            <td style="border: none; padding: 2px;">{{ $prestasi->count() }} prestasi</td>
        </tr>
        <tr>
            <td style="border: none; padding: 2px;"><strong>Status Draft:</strong></td>
            <td style="border: none; padding: 2px;">{{ $prestasi->where('status', 'draft')->count() }} prestasi</td>
        </tr>
        <tr>
            <td style="border: none; padding: 2px;"><strong>Menunggu Validasi:</strong></td>
            <td style="border: none; padding: 2px;">{{ $prestasi->where('status', 'menunggu_validasi')->count() }} prestasi</td>
        </tr>
        <tr>
            <td style="border: none; padding: 2px;"><strong>Diterima:</strong></td>
            <td style="border: none; padding: 2px;">{{ $prestasi->where('status', 'diterima')->count() }} prestasi</td>
        </tr>
        <tr>
            <td style="border: none; padding: 2px;"><strong>Ditolak:</strong></td>
            <td style="border: none; padding: 2px;">{{ $prestasi->where('status', 'ditolak')->count() }} prestasi</td>
        </tr>
    </table>
</div>

<p style="margin-top: 15px; font-size: 11px;">
    Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y H:i:s') }}
</p>
@endsection