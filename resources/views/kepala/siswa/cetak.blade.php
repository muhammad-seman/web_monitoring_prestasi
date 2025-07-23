@extends('layouts.letterhead', [
    'title' => 'Daftar Siswa - Kepala Sekolah',
    'date' => 'Barabai, ' . date('d F Y'),
    'letterType' => 'Kepala Sekolah',
    'signatureName' => 'Kepala Sekolah',
    'signatureTitle' => Auth::user()->name ?? 'Kepala Sekolah'
])

@section('content')
    <div style="text-align: center; margin-bottom: 30px;">
        <h2 style="margin: 0; font-size: 16px; font-weight: bold;">DAFTAR SISWA</h2>
        <p style="margin: 5px 0; font-size: 12px;">Sistem Monitoring Prestasi Siswa</p>
        <p style="margin: 5px 0; font-size: 12px;">Tanggal Cetak: {{ date('d-m-Y H:i') }}</p>
    </div>

    <div style="margin-bottom: 15px; font-size: 11px; color: #666;">
        @if(request('kelas_id'))
            <strong>Kelas:</strong> {{ optional($kelas->where('id', request('kelas_id'))->first())->nama_kelas ?? '-' }}
        @else
            <strong>Kelas:</strong> Semua Kelas
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th>Nama</th>
                <th>NISN</th>
                <th>Kelas</th>
                <th>Jenis Kelamin</th>
                <th>Tahun Masuk</th>
            </tr>
        </thead>
        <tbody>
            @forelse($siswa as $i => $s)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $s->nama }}</td>
                <td>{{ $s->nisn }}</td>
                <td>{{ $s->kelas->nama_kelas ?? '-' }}</td>
                <td>{{ $s->jenis_kelamin }}</td>
                <td>{{ $s->tahun_masuk }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 20px; font-style: italic;">Belum ada data siswa.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    
    <div style="margin-top: 30px; font-size: 10px; color: #666; text-align: center;">
        <p><em>Dicetak oleh: {{ Auth::user()->name ?? 'Kepala Sekolah' }} pada {{ date('d-m-Y H:i:s') }}</em></p>
    </div>
@endsection 