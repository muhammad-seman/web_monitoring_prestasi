<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Daftar Siswa - Kepala Sekolah</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .filter-info { margin-bottom: 15px; font-size: 11px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #222; padding: 6px; text-align: left; font-size: 11px; }
        th { background: #f2f2f2; font-weight: bold; }
        .no-data { text-align: center; padding: 20px; font-style: italic; }
        @media print { body { margin: 0; } .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="header">
        <h2>DAFTAR SISWA</h2>
        <p>Sistem Monitoring Prestasi Siswa</p>
        <p>Tanggal Cetak: {{ date('d-m-Y H:i') }}</p>
    </div>

    <div class="filter-info">
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
                <td colspan="6" class="no-data">Belum ada data siswa.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; font-size: 10px; color: #666;">
        <p><em>Dicetak oleh: {{ Auth::user()->name ?? 'Kepala Sekolah' }} pada {{ date('d-m-Y H:i:s') }}</em></p>
    </div>
</body>
</html> 