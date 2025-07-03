<!DOCTYPE html>
<html>

<head>
    <title>Daftar Siswa</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 4px;
            text-align: left;
        }
    </style>
</head>

<body>
    <h3 style="text-align:center;">Daftar Siswa</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NISN</th>
                <th>Nama</th>
                <th>Kelas</th>
                <th>Jenis Kelamin</th>
                <th>Tanggal Lahir</th>
                <th>Alamat</th>
                <th>Tahun Masuk</th>
            </tr>
        </thead>
        <tbody>
            @foreach($siswa as $i => $s)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $s->nisn }}</td>
                <td>{{ $s->nama }}</td>
                <td>{{ $s->kelas->nama_kelas ?? '-' }}</td>
                <td>{{ $s->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                <td>{{ $s->tanggal_lahir ? \Carbon\Carbon::parse($s->tanggal_lahir)->format('d-m-Y') : '-' }}</td>
                <td>{{ $s->alamat }}</td>
                <td>{{ $s->tahun_masuk }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>