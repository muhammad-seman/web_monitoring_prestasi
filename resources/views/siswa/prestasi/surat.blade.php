<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Surat Pernyataan Prestasi</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        .header { text-align: center; margin-bottom: 20px; }
        .content { margin: 0 30px; }
        .ttd { margin-top: 40px; text-align: right; }
        table { width: 100%; margin-bottom: 10px; }
        th, td { text-align: left; padding: 2px 4px; }
    </style>
</head>
<body>
    <div class="header">
        <h3>SURAT PERNYATAAN PRESTASI SISWA</h3>
    </div>
    <div class="content">
        <p>Yang bertanda tangan di bawah ini:</p>
        <table>
            <tr><th>Nama</th><td>: {{ $siswa->nama }}</td></tr>
            <tr><th>NISN</th><td>: {{ $siswa->nisn }}</td></tr>
            <tr><th>Kelas</th><td>: {{ $siswa->kelas->nama_kelas ?? '-' }}</td></tr>
        </table>
        <p>Dengan ini menyatakan bahwa saya benar-benar telah mengikuti dan meraih prestasi sebagai berikut:</p>
        <table>
            <tr><th>Nama Prestasi</th><td>: {{ $prestasi->nama_prestasi }}</td></tr>
            <tr><th>Kategori</th><td>: {{ $prestasi->kategoriPrestasi->nama_kategori ?? '-' }}</td></tr>
            <tr><th>Tingkat</th><td>: {{ $prestasi->tingkatPenghargaan->tingkat ?? '-' }}</td></tr>
            <tr><th>Ekskul</th><td>: {{ $prestasi->ekskul->nama ?? '-' }}</td></tr>
            <tr><th>Penyelenggara</th><td>: {{ $prestasi->penyelenggara }}</td></tr>
            <tr><th>Tanggal</th><td>: {{ $prestasi->tanggal_prestasi }}</td></tr>
            <tr><th>Keterangan</th><td>: {{ $prestasi->keterangan ?? '-' }}</td></tr>
        </table>
        <p>Demikian surat pernyataan ini saya buat dengan sebenar-benarnya untuk digunakan sebagaimana mestinya.</p>
        <div class="ttd">
            <p>{{ date('d-m-Y') }}</p>
            <p>Hormat saya,</p>
            <br><br><br>
            <p><strong>{{ $siswa->nama }}</strong></p>
        </div>
    </div>
</body>
</html> 