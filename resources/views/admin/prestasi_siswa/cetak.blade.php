<!DOCTYPE html>
<html>
<head>
    <title>Rekap Prestasi Siswa</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px;}
        table { width: 100%; border-collapse: collapse; margin-top: 20px;}
        th, td { border: 1px solid #222; padding: 4px; text-align: left; }
        th { background: #eee; }
        h3 { margin-bottom: 0; }
    </style>
</head>
<body>
    <h3>Rekap Prestasi Siswa</h3>
    @if(request('kategori'))
        <p>Kategori: <b>{{ $prestasi->first()->kategori->nama_kategori ?? '-' }}</b></p>
    @endif
    @if(request('from') && request('to'))
        <p>Periode: <b>{{ request('from') }} s/d {{ request('to') }}</b></p>
    @endif

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>Nama Prestasi</th>
                <th>Kategori</th>
                <th>Tingkat</th>
                <th>Status</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($prestasi as $i => $p)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $p->siswa->nama ?? '-' }}</td>
                <td>{{ $p->nama_prestasi }}</td>
                <td>{{ $p->kategori->nama_kategori ?? '-' }}</td>
                <td>{{ $p->tingkat->tingkat ?? '-' }}</td>
                <td>{{ ucwords(str_replace('_', ' ', $p->status)) }}</td>
                <td>{{ $p->tanggal_prestasi ? \Carbon\Carbon::parse($p->tanggal_prestasi)->format('d-m-Y') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>