@extends('layouts.letterhead', [
    'title' => 'Rekap Prestasi Siswa',
    'date' => '',
    'letterType' => '',
    'hideSignature' => true
])

@section('content')
<div style="text-align: center; margin-bottom: 20px;">
    <h3 style="text-decoration: underline; font-size: 16px; margin-bottom: 20px;">REKAP PRESTASI SISWA</h3>
</div>

@if(request('kelas_id') || request('kategori_id') || request('tingkat_id') || request('status'))
    @if(request('kelas_id'))
        <p style="margin-bottom: 10px;">Kelas: <strong>{{ optional($kelas->where('id', request('kelas_id'))->first())->nama_kelas ?? '-' }}</strong></p>
    @endif
    @if(request('kategori_id'))
        <p style="margin-bottom: 10px;">Kategori: <strong>{{ optional($kategori->where('id', request('kategori_id'))->first())->nama_kategori ?? '-' }}</strong></p>
    @endif
    @if(request('tingkat_id'))
        <p style="margin-bottom: 10px;">Tingkat: <strong>{{ optional($tingkat->where('id', request('tingkat_id'))->first())->tingkat ?? '-' }}</strong></p>
    @endif
    @if(request('status'))
        <p style="margin-bottom: 10px;">Status: <strong>{{ ucwords(str_replace('_', ' ', request('status'))) }}</strong></p>
    @endif
@endif

<table>
    <thead>
        <tr>
            <th style="width: 5%; text-align: center;">No</th>
            <th style="width: 15%;">Nama Siswa</th>
            <th style="width: 8%;">Kelas</th>
            <th style="width: 20%;">Nama Prestasi</th>
            <th style="width: 12%;">Kategori</th>
            <th style="width: 12%;">Tingkat</th>
            <th style="width: 15%;">Penyelenggara</th>
            <th style="width: 8%;">Tanggal</th>
            <th style="width: 5%;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($prestasi as $i => $p)
        <tr>
            <td style="text-align: center;">{{ $i + 1 }}</td>
            <td>{{ $p->siswa->nama ?? '-' }}</td>
            <td>{{ $p->siswa->kelas->nama_kelas ?? '-' }}</td>
            <td>{{ $p->nama_prestasi }}</td>
            <td>{{ $p->kategoriPrestasi->nama_kategori ?? '-' }}</td>
            <td>{{ $p->tingkatPenghargaan->tingkat ?? '-' }}</td>
            <td>{{ $p->penyelenggara ?? '-' }}</td>
            <td>{{ $p->tanggal_prestasi ? \Carbon\Carbon::parse($p->tanggal_prestasi)->format('d/m/Y') : '-' }}</td>
            <td>{{ ucwords(str_replace('_', ' ', $p->status)) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="9" style="text-align: center; font-style: italic;">Tidak ada data prestasi</td>
        </tr>
        @endforelse
    </tbody>
</table>

<!-- SIGNATURE SECTION -->
<div style="margin-top: 50px; text-align: right; font-size: 12px;">
    <div style="display: inline-block; text-align: left;">
        <br><br>
        <strong>Kepala Madrasah</strong><br>
        <div style="border-bottom: 1px solid #000; width: 150px; margin: 60px 0 0;"></div>
        <div style="margin-top: 5px;">Soneran</div>
    </div>
</div>
@endsection 