@extends('layouts.letterhead', [
    'title' => 'Laporan Prestasi Siswa',
    'date' => '',
    'letterType' => '',
    'hideSignature' => false
])

@section('content')
<style>
    .signature-box {
        text-align: left !important;
    }
</style>
<div style="text-align: center; margin-bottom: 20px;">
    <h3 style="text-decoration: underline; font-size: 16px; margin-bottom: 20px;">LAPORAN PRESTASI SISWA</h3>
</div>

<!-- Student Information -->
<div style="margin-bottom: 20px; background-color: #f5f5f5; padding: 15px; border: 1px solid #ddd;">
    <table style="border: none; margin: 0;">
        <tr>
            <td style="border: none; padding: 2px 0; width: 120px;"><strong>Nama Siswa</strong></td>
            <td style="border: none; padding: 2px 0; width: 10px;">:</td>
            <td style="border: none; padding: 2px 0;">{{ $siswa->nama ?? 'N/A' }}</td>
            <td style="border: none; padding: 2px 0; width: 120px;"><strong>Kelas</strong></td>
            <td style="border: none; padding: 2px 0; width: 10px;">:</td>
            <td style="border: none; padding: 2px 0;">{{ $siswa->kelas->nama_kelas ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="border: none; padding: 2px 0;"><strong>NIS</strong></td>
            <td style="border: none; padding: 2px 0;">:</td>
            <td style="border: none; padding: 2px 0;">{{ $siswa->nis ?? 'N/A' }}</td>
            <td style="border: none; padding: 2px 0;"><strong>Jurusan</strong></td>
            <td style="border: none; padding: 2px 0;">:</td>
            <td style="border: none; padding: 2px 0;">{{ $siswa->kelas->jurusan ?? 'N/A' }}</td>
        </tr>
    </table>
</div>

<!-- Filter Information -->
@if(request('kategori'))
    <p style="margin-bottom: 10px;">Kategori: <strong>{{ $prestasi->first()->kategoriPrestasi->nama_kategori ?? '-' }}</strong></p>
@endif
@if(request('tingkat'))
    <p style="margin-bottom: 10px;">Tingkat: <strong>{{ $prestasi->first()->tingkatPenghargaan->tingkat ?? '-' }}</strong></p>
@endif
@if(request('from') && request('to'))
    <p style="margin-bottom: 10px;">Periode: <strong>{{ \Carbon\Carbon::parse(request('from'))->format('d F Y') }} s/d {{ \Carbon\Carbon::parse(request('to'))->format('d F Y') }}</strong></p>
@endif

@if($prestasiByTingkat->count() > 0)
    @foreach($prestasiByTingkat as $tingkat => $prestasiList)
    <div style="margin-bottom: 25px;">
        <h4 style="background-color: #e3f2fd; padding: 8px; margin-bottom: 10px; font-weight: bold; border-left: 4px solid #2196f3; font-size: 12px;">
            {{ $tingkat }} ({{ $prestasiList->count() }} prestasi)
        </h4>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">No</th>
                    <th style="width: 25%;">Nama Prestasi</th>
                    <th style="width: 15%;">Kategori</th>
                    <th style="width: 15%;">Ekstrakurikuler</th>
                    <th style="width: 15%;">Penyelenggara</th>
                    <th style="width: 12%;">Tanggal</th>
                    <th style="width: 13%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prestasiList as $p)
                <tr>
                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                    <td>{{ $p->nama_prestasi }}</td>
                    <td>{{ $p->kategoriPrestasi->nama_kategori ?? '-' }}</td>
                    <td>{{ $p->ekskul->nama ?? '-' }}</td>
                    <td>{{ $p->penyelenggara ?? '-' }}</td>
                    <td>{{ $p->tanggal_prestasi ? \Carbon\Carbon::parse($p->tanggal_prestasi)->format('d/m/Y') : '-' }}</td>
                    <td>{{ ucwords(str_replace('_', ' ', $p->status)) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach
@else
    <table>
        <tr>
            <td style="text-align: center; font-style: italic;">Tidak ada data prestasi yang ditemukan.</td>
        </tr>
    </table>
@endif

<p style="margin-top: 20px; font-size: 11px;">
    <strong>Total Prestasi: {{ $prestasi->count() }} prestasi</strong><br>
    Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y H:i:s') }}
</p>

@endsection