@extends('layouts.letterhead', [
    'title' => 'Daftar Siswa',
    'date' => '',
    'letterType' => '',
    'hideSignature' => true
])

@section('content')
<div style="text-align: center; margin-bottom: 20px;">
    <h3 style="text-decoration: underline; font-size: 16px; margin-bottom: 20px;">DAFTAR SISWA</h3>
</div>

<table>
    <thead>
        <tr>
            <th style="width: 5%; text-align: center;">No</th>
            <th style="width: 12%;">NISN</th>
            <th style="width: 18%;">Nama</th>
            <th style="width: 8%;">Kelas</th>
            <th style="width: 10%;">Jenis Kelamin</th>
            <th style="width: 12%;">Tanggal Lahir</th>
            <th style="width: 20%;">Alamat</th>
            <th style="width: 8%;">Tahun Masuk</th>
            <th style="width: 7%;">Wali</th>
        </tr>
    </thead>
    <tbody>
        @forelse($siswa as $i => $s)
        <tr>
            <td style="text-align: center;">{{ $i+1 }}</td>
            <td>{{ $s->nisn }}</td>
            <td>{{ $s->nama }}</td>
            <td>{{ $s->kelas->nama_kelas ?? '-' }}</td>
            <td>{{ $s->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
            <td>{{ $s->tanggal_lahir ? \Carbon\Carbon::parse($s->tanggal_lahir)->format('d/m/Y') : '-' }}</td>
            <td style="font-size: 10px;">{{ $s->alamat ?? '-' }}</td>
            <td style="text-align: center;">{{ $s->tahun_masuk ?? '-' }}</td>
            <td>{{ $s->wali->nama ?? '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="9" style="text-align: center; font-style: italic;">Tidak ada data siswa</td>
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