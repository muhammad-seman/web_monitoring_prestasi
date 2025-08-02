@extends('layouts.letterhead', [
    'title' => 'Data Siswa Ekstrakurikuler',
    'date' => '',
    'letterType' => '',
    'hideSignature' => true
])

@section('content')
<div style="text-align: center; margin-bottom: 20px;">
    <h3 style="text-decoration: underline; font-size: 16px; margin-bottom: 20px;">DATA SISWA EKSTRAKURIKULER</h3>
</div>

@if($selectedEkskul)
    <p style="margin-bottom: 10px;">Ekstrakurikuler: <strong>{{ $selectedEkskul }}</strong></p>
@endif
@if($selectedKelas)
    <p style="margin-bottom: 10px;">Kelas: <strong>{{ $selectedKelas }}</strong></p>
@endif

<table>
    <thead>
        <tr>
            <th style="width: 5%; text-align: center;">No</th>
            <th style="width: 20%;">Nama Siswa</th>
            <th style="width: 12%;">NISN</th>
            <th style="width: 10%;">Kelas</th>
            <th style="width: 20%;">Ekstrakurikuler</th>
            <th style="width: 15%;">Pembina</th>
            <th style="width: 18%;">Tanggal Bergabung</th>
        </tr>
    </thead>
    <tbody>
        @forelse($siswaEkskul as $i => $se)
        <tr>
            <td style="text-align: center;">{{ $i + 1 }}</td>
            <td>{{ $se->siswa->nama ?? '-' }}</td>
            <td>{{ $se->siswa->nisn ?? '-' }}</td>
            <td>{{ $se->siswa->kelas->nama_kelas ?? '-' }}</td>
            <td>{{ $se->ekskul->nama ?? '-' }}</td>
            <td>{{ $se->ekskul->pembina ?? '-' }}</td>
            <td>{{ $se->created_at ? \Carbon\Carbon::parse($se->created_at)->format('d/m/Y') : '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align: center; font-style: italic;">Tidak ada data siswa ekstrakurikuler</td>
        </tr>
        @endforelse
    </tbody>
</table>

<p style="margin-top: 20px; font-size: 11px;">
    <strong>Total Siswa: {{ $siswaEkskul->count() }} siswa</strong><br>
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