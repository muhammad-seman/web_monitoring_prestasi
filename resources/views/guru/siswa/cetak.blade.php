<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Data Siswa - Kelas Saya</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .kelas-info {
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>DAFTAR SISWA KELAS SAYA</h2>
        <p>Sistem Monitoring Prestasi Siswa</p>
        <p>Tanggal Cetak: {{ date('d-m-Y H:i') }}</p>
    </div>

    <div class="kelas-info">
        @if(isset($kelas) && $kelas->count() > 0)
            <strong>Kelas yang diampu:</strong>
            @foreach($kelas as $kls)
                {{ $kls->nama_kelas }}{{ !$loop->last ? ', ' : '' }}
            @endforeach
        @else
            <strong>Kelas:</strong> Tidak ada kelas yang diampu
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th>NISN</th>
                <th>Nama</th>
                <th>Gender</th>
                <th>Tempat, Tanggal Lahir</th>
                <th>Alamat</th>
                <th>Tahun Masuk</th>
            </tr>
        </thead>
        <tbody>
            @forelse($siswa as $i => $s)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $s->nisn }}</td>
                <td>{{ $s->nama }}</td>
                <td>{{ $s->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                <td>
                    {{ $s->tempat_lahir ?? '-' }},
                    {{ $s->tanggal_lahir ? \Carbon\Carbon::parse($s->tanggal_lahir)->format('d-m-Y') : '-' }}
                </td>
                <td>{{ $s->alamat ?? '-' }}</td>
                <td>{{ $s->tahun_masuk ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="no-data">Belum ada data siswa di kelas ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px;">
        <p><strong>Total Siswa:</strong> {{ $siswa->count() }} orang</p>
    </div>
</body>
</html>
