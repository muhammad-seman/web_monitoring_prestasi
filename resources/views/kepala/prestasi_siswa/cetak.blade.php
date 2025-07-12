<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekap Prestasi Siswa - Kepala Sekolah</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .filter-info { margin-bottom: 15px; font-size: 11px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #222; padding: 6px; text-align: left; font-size: 11px; }
        th { background: #f2f2f2; font-weight: bold; }
        .no-data { text-align: center; padding: 20px; font-style: italic; }
        .status-badge { padding: 2px 6px; border-radius: 3px; font-size: 10px; font-weight: bold; }
        .status-draft { background-color: #6c757d; color: white; }
        .status-menunggu_validasi { background-color: #ffc107; color: black; }
        .status-diterima { background-color: #28a745; color: white; }
        .status-ditolak { background-color: #dc3545; color: white; }
        @media print { body { margin: 0; } .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="header">
        <h2>REKAP PRESTASI SISWA</h2>
        <p>Sistem Monitoring Prestasi Siswa</p>
        <p>Tanggal Cetak: {{ date('d-m-Y H:i') }}</p>
    </div>

    <div class="filter-info">
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
                    <span class="status-badge status-{{ $p->status }}">
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

    <div style="margin-top: 30px; font-size: 10px; color: #666;">
        <p><em>Dicetak oleh: {{ Auth::user()->name ?? 'Kepala Sekolah' }} pada {{ date('d-m-Y H:i:s') }}</em></p>
    </div>
</body>
</html> 