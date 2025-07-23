@extends('layouts.letterhead', [
    'title' => 'Rekap Prestasi Siswa - Kepala Sekolah',
    'date' => 'Barabai, ' . date('d F Y'),
    'letterType' => 'Kepala Sekolah',
    'signatureName' => 'Kepala Sekolah',
    'signatureTitle' => Auth::user()->name ?? 'Kepala Sekolah'
])

@section('content')
    <div style="text-align: center; margin-bottom: 30px;">
        <h2 style="margin: 0; font-size: 16px; font-weight: bold;">REKAP PRESTASI SISWA</h2>
        <p style="margin: 5px 0; font-size: 12px;">Sistem Monitoring Prestasi Siswa</p>
        <p style="margin: 5px 0; font-size: 12px;">Tanggal Cetak: {{ date('d-m-Y H:i') }}</p>
    </div>

    <div style="margin-bottom: 15px; font-size: 11px; color: #666;">
        @if(request('kelas_id') || request('kategori_id') || request('tingkat_id') || request('status'))
            <strong>Filter yang diterapkan:</strong><br>
            @if(request('kelas_id'))
                • Kelas: {{ optional($kelas->where('id', request('kelas_id'))->first())->nama_kelas ?? '-' }}<br>
            @endif
            @if(request('kategori_id'))
                • Kategori: {{ optional($kategori->where('id', request('kategori_id'))->first())->nama_kategori ?? '-' }}<br>
            @endif
            @if(request('tingkat_id'))
                • Tingkat: {{ optional($tingkat->where('id', request('tingkat_id'))->first())->tingkat ?? '-' }}<br>
            @endif
            @if(request('status'))
                • Status: {{ ucwords(str_replace('_', ' ', request('status'))) }}<br>
            @endif
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Nama Prestasi</th>
                <th>Kategori</th>
                <th>Tingkat</th>
                <th>Penyelenggara</th>
                <th>Tanggal</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($prestasi as $i => $p)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $p->siswa->nama ?? '-' }}</td>
                <td>{{ $p->siswa->kelas->nama_kelas ?? '-' }}</td>
                <td>{{ $p->nama_prestasi }}</td>
                <td>{{ $p->kategoriPrestasi->nama_kategori ?? '-' }}</td>
                <td>{{ $p->tingkatPenghargaan->tingkat ?? '-' }}</td>
                <td>{{ $p->penyelenggara ?? '-' }}</td>
                <td>{{ $p->tanggal_prestasi ? \Carbon\Carbon::parse($p->tanggal_prestasi)->format('d-m-Y') : '-' }}</td>
                <td>
                    @php
                        $statusStyle = match($p->status) {
                            'draft' => 'background-color: #6c757d; color: white;',
                            'menunggu_validasi' => 'background-color: #ffc107; color: black;',
                            'diterima' => 'background-color: #28a745; color: white;',
                            'ditolak' => 'background-color: #dc3545; color: white;',
                            default => 'background-color: #6c757d; color: white;'
                        };
                    @endphp
                    <span style="padding: 2px 6px; border-radius: 3px; font-size: 10px; font-weight: bold; {{ $statusStyle }}">
                        {{ ucwords(str_replace('_', ' ', $p->status)) }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="no-data">Belum ada data prestasi siswa.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    
    <div style="margin-top: 30px; font-size: 10px; color: #666; text-align: center;">
        <p><em>Dicetak oleh: {{ Auth::user()->name ?? 'Kepala Sekolah' }} pada {{ date('d-m-Y H:i:s') }}</em></p>
    </div>
@endsection 