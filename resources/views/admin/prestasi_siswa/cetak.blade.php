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

@if(request('kategori'))
    <p style="margin-bottom: 10px;">Kategori: <strong>{{ $prestasi->first()->kategori->nama_kategori ?? '-' }}</strong></p>
@endif
@if(request('from') && request('to'))
    <p style="margin-bottom: 10px;">Periode: <strong>{{ \Carbon\Carbon::parse(request('from'))->format('d F Y') }} s/d {{ \Carbon\Carbon::parse(request('to'))->format('d F Y') }}</strong></p>
@endif

<table>
    <thead>
        <tr>
            <th style="width: 5%; text-align: center;">No</th>
            <th style="width: 20%;">Nama Siswa</th>
            <th style="width: 25%;">Nama Prestasi</th>
            <th style="width: 15%;">Kategori</th>
            <th style="width: 15%;">Tingkat</th>
            <th style="width: 10%;">Status</th>
            <th style="width: 10%;">Tanggal</th>
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
            <td>{{ ucwords(str_replace('_', ' ', $p->status)) }}</td>
            <td>{{ $p->tanggal_prestasi ? \Carbon\Carbon::parse($p->tanggal_prestasi)->format('d/m/Y') : '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align: center; font-style: italic;">Tidak ada data prestasi</td>
        </tr>
        @endforelse
    </tbody>
</table>

<p style="margin-top: 20px; font-size: 11px;">
    <strong>Total Prestasi: {{ $prestasi->count() }} prestasi</strong><br>
    Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y H:i:s') }}
</p>

<!-- SIGNATURE SECTION -->
<div style="margin-top: 50px; text-align: right; font-size: 12px;">
    <div style="display: inline-block; text-align: left;">
        <br><br>
        <strong>Kepala Sekolah</strong><br>
        <div style="border-bottom: 1px solid #000; width: 150px; margin: 60px 0 0;"></div>
        <div style="margin-top: 5px;">Soneran</div>
    </div>
</div>
@endsection