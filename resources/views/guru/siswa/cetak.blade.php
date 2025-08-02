@extends('layouts.letterhead', [
    'title' => 'Daftar Siswa Kelas',
    'date' => '',
    'letterType' => '',
    'hideSignature' => true
])

@section('content')
<div style="text-align: center; margin-bottom: 20px;">
    <h3 style="text-decoration: underline; font-size: 16px; margin-bottom: 20px;">DAFTAR SISWA KELAS</h3>
</div>

@if(isset($kelas) && $kelas->count() > 0)
    <p style="margin-bottom: 10px;">Kelas yang diampu: <strong>
        @foreach($kelas as $kls)
            {{ $kls->nama_kelas }}{{ !$loop->last ? ', ' : '' }}
        @endforeach
    </strong></p>
@else
    <p style="margin-bottom: 10px;">Kelas: <strong>Tidak ada kelas yang diampu</strong></p>
@endif

<table>
    <thead>
        <tr>
            <th style="width: 5%; text-align: center;">No</th>
            <th style="width: 12%;">NISN</th>
            <th style="width: 18%;">Nama</th>
            <th style="width: 10%;">Jenis Kelamin</th>
            <th style="width: 15%;">Tempat, Tanggal Lahir</th>
            <th style="width: 25%;">Alamat</th>
            <th style="width: 10%;">Tahun Masuk</th>
        </tr>
    </thead>
    <tbody>
        @forelse($siswa as $i => $s)
        <tr>
            <td style="text-align: center;">{{ $i + 1 }}</td>
            <td>{{ $s->nisn }}</td>
            <td>{{ $s->nama }}</td>
            <td>{{ $s->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
            <td>
                {{ $s->tempat_lahir ?? '-' }}{{ $s->tempat_lahir && $s->tanggal_lahir ? ', ' : '' }}{{ $s->tanggal_lahir ? \Carbon\Carbon::parse($s->tanggal_lahir)->format('d/m/Y') : ($s->tempat_lahir ? '' : '-') }}
            </td>
            <td style="font-size: 10px;">{{ $s->alamat ?? '-' }}</td>
            <td style="text-align: center;">{{ $s->tahun_masuk ?? '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align: center; font-style: italic;">Tidak ada data siswa</td>
        </tr>
        @endforelse
    </tbody>
</table>

<!-- SIGNATURE SECTION -->
<div style="margin-top: 50px; text-align: right; font-size: 12px;">
    <div style="display: inline-block; text-align: left;">
        <br><br>
        <strong>Wali Kelas</strong><br>
        <div style="border-bottom: 1px solid #000; width: 150px; margin: 60px 0 0;"></div>
        <div style="margin-top: 5px;">{{ Auth::user()->nama ?? 'Nama Guru' }}</div>
    </div>
</div>
@endsection
