<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekap Prestasi Siswa - Guru</title>
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
        .filter-info {
            margin-bottom: 15px;
            font-size: 11px;
            color: #666;
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
            font-size: 11px;
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
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .status-draft { background-color: #6c757d; color: white; }
        .status-menunggu { background-color: #ffc107; color: black; }
        .status-diterima { background-color: #28a745; color: white; }
        .status-ditolak { background-color: #dc3545; color: white; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>REKAP PRESTASI SISWA KELAS SAYA</h2>
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

    <div class="filter-info">
        @if(request('kategori') || request('from') || request('to') || request('status'))
            <strong>Filter yang diterapkan:</strong><br>
            @if(request('kategori'))
                • Kategori: {{ \App\Models\KategoriPrestasi::find(request('kategori'))->nama_kategori ?? '-' }}<br>
            @endif
            @if(request('from') || request('to'))
                • Periode: {{ request('from') ? \Carbon\Carbon::parse(request('from'))->format('d-m-Y') : 'Awal' }} 
                s/d {{ request('to') ? \Carbon\Carbon::parse(request('to'))->format('d-m-Y') : 'Akhir' }}<br>
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
                <th>Nama Prestasi</th>
                <th>Kategori</th>
                <th>Tingkat</th>
                <th>Penyelenggara</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($prestasi as $i => $p)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $p->siswa->nama ?? '-' }}</td>
                <td>{{ $p->nama_prestasi }}</td>
                <td>{{ $p->kategori->nama_kategori ?? '-' }}</td>
                <td>{{ $p->tingkat->tingkat ?? '-' }}</td>
                <td>{{ $p->penyelenggara }}</td>
                <td>{{ $p->tanggal_prestasi ? \Carbon\Carbon::parse($p->tanggal_prestasi)->format('d-m-Y') : '-' }}</td>
                <td>
                    <span class="status-badge status-{{ str_replace('_', '', $p->status) }}">
                        {{ ucwords(str_replace('_', ' ', $p->status)) }}
                    </span>
                </td>
                <td>{{ $p->rata_rata_nilai ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="no-data">Belum ada data prestasi siswa di kelas Anda.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px;">
        <table style="width: auto; border: none;">
            <tr>
                <td style="border: none;"><strong>Total Prestasi:</strong></td>
                <td style="border: none;">{{ $prestasi->count() }} prestasi</td>
            </tr>
            <tr>
                <td style="border: none;"><strong>Status Draft:</strong></td>
                <td style="border: none;">{{ $prestasi->where('status', 'draft')->count() }} prestasi</td>
            </tr>
            <tr>
                <td style="border: none;"><strong>Menunggu Validasi:</strong></td>
                <td style="border: none;">{{ $prestasi->where('status', 'menunggu_validasi')->count() }} prestasi</td>
            </tr>
            <tr>
                <td style="border: none;"><strong>Diterima:</strong></td>
                <td style="border: none;">{{ $prestasi->where('status', 'diterima')->count() }} prestasi</td>
            </tr>
            <tr>
                <td style="border: none;"><strong>Ditolak:</strong></td>
                <td style="border: none;">{{ $prestasi->where('status', 'ditolak')->count() }} prestasi</td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 20px; font-size: 10px; color: #666;">
        <p><em>Dicetak oleh: {{ Auth::user()->name ?? 'Guru' }} pada {{ date('d-m-Y H:i:s') }}</em></p>
    </div>
</body>
</html> 