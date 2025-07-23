@extends('layouts.letterhead', [
    'title' => 'Surat Pernyataan Prestasi Siswa',
    'date' => 'Barabai, ' . \Carbon\Carbon::now()->format('d F Y'),
    'letterType' => 'Kepala Madrasah',
    'signatureName' => $siswa->nama,
    'signatureTitle' => 'Siswa'
])

@section('content')
<div style="text-align: center; margin-bottom: 20px;">
    <h3 style="text-decoration: underline; font-size: 16px; margin-bottom: 20px;">SURAT PERNYATAAN PRESTASI SISWA</h3>
</div>

<p style="text-align: justify; margin-bottom: 15px;">Yang bertanda tangan di bawah ini:</p>

<div class="letter-header-info">
    <table style="border: none; margin-bottom: 20px;">
        <tr>
            <td class="label">Nama</td>
            <td class="separator">:</td>
            <td>{{ $siswa->nama }}</td>
        </tr>
        <tr>
            <td class="label">NISN</td>
            <td class="separator">:</td>
            <td>{{ $siswa->nisn }}</td>
        </tr>
        <tr>
            <td class="label">Kelas</td>
            <td class="separator">:</td>
            <td>{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
        </tr>
    </table>
</div>

<p style="text-align: justify; margin-bottom: 15px;">Dengan ini menyatakan bahwa saya benar-benar telah mengikuti dan meraih prestasi sebagai berikut:</p>

<div class="letter-header-info">
    <table style="border: none; margin-bottom: 20px;">
        <tr>
            <td class="label">Nama Prestasi</td>
            <td class="separator">:</td>
            <td>{{ $prestasi->nama_prestasi }}</td>
        </tr>
        <tr>
            <td class="label">Kategori</td>
            <td class="separator">:</td>
            <td>{{ $prestasi->kategoriPrestasi->nama_kategori ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tingkat</td>
            <td class="separator">:</td>
            <td>{{ $prestasi->tingkatPenghargaan->tingkat ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Ekstrakurikuler</td>
            <td class="separator">:</td>
            <td>{{ $prestasi->ekskul->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Penyelenggara</td>
            <td class="separator">:</td>
            <td>{{ $prestasi->penyelenggara ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal</td>
            <td class="separator">:</td>
            <td>{{ \Carbon\Carbon::parse($prestasi->tanggal_prestasi)->format('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Keterangan</td>
            <td class="separator">:</td>
            <td>{{ $prestasi->keterangan ?? '-' }}</td>
        </tr>
    </table>
</div>

<p style="text-align: justify; margin-bottom: 30px;">Demikian surat pernyataan ini saya buat dengan sebenar-benarnya untuk digunakan sebagaimana mestinya.</p>
@endsection 