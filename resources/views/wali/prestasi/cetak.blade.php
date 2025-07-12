<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Prestasi Anak</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-section h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            font-weight: bold;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .status-diterima { background-color: #d4edda; color: #155724; }
        .status-menunggu { background-color: #fff3cd; color: #856404; }
        .status-draft { background-color: #e2e3e5; color: #383d41; }
        .status-ditolak { background-color: #f8d7da; color: #721c24; }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
        }
        .no-data {
            text-align: center;
            font-style: italic;
            color: #666;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REKAP PRESTASI ANAK</h1>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Oleh: {{ auth()->user()->name }} (Wali)</p>
    </div>

    <div class="info-section">
        <h3>Ringkasan Data</h3>
        <table style="width: 50%; border: none;">
            <tr>
                <td style="border: none; padding: 2px;">Total Prestasi</td>
                <td style="border: none; padding: 2px;">: {{ $prestasi->count() }} prestasi</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px;">Prestasi Diterima</td>
                <td style="border: none; padding: 2px;">: {{ $prestasi->where('status', 'diterima')->count() }} prestasi</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px;">Menunggu Validasi</td>
                <td style="border: none; padding: 2px;">: {{ $prestasi->where('status', 'menunggu_validasi')->count() }} prestasi</td>
            </tr>
        </table>
    </div>

    @if($prestasi->count() > 0)
        <div class="info-section">
            <h3>Detail Prestasi</h3>
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="20%">Nama Anak</th>
                        <th width="25%">Prestasi</th>
                        <th width="12%">Kategori</th>
                        <th width="12%">Tingkat</th>
                        <th width="10%">Status</th>
                        <th width="10%">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prestasi as $index => $p)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $p->siswa->nama }}</strong><br>
                            <small>{{ $p->siswa->nisn }} - {{ $p->siswa->kelas->nama_kelas ?? '-' }}</small>
                        </td>
                        <td>
                            <strong>{{ $p->nama_prestasi }}</strong><br>
                            <small>{{ $p->penyelenggara }}</small>
                        </td>
                        <td>{{ $p->kategori->nama_kategori }}</td>
                        <td>{{ $p->tingkat->tingkat }}</td>
                        <td>
                            @php
                                $statusClass = match($p->status) {
                                    'diterima' => 'status-diterima',
                                    'menunggu_validasi' => 'status-menunggu',
                                    'draft' => 'status-draft',
                                    'ditolak' => 'status-ditolak',
                                    default => 'status-draft'
                                };
                                $statusText = match($p->status) {
                                    'diterima' => 'Diterima',
                                    'menunggu_validasi' => 'Menunggu',
                                    'draft' => 'Draft',
                                    'ditolak' => 'Ditolak',
                                    default => ucfirst($p->status)
                                };
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($p->tanggal_prestasi)->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="info-section">
            <h3>Rekap per Anak</h3>
            @php
                $anakGroup = $prestasi->groupBy('id_siswa');
            @endphp
            @foreach($anakGroup as $siswaId => $prestasiAnak)
                @php
                    $siswa = $prestasiAnak->first()->siswa;
                @endphp
                <table style="margin-bottom: 15px;">
                    <tr>
                        <td colspan="4" style="background-color: #f8f9fa; font-weight: bold;">
                            {{ $siswa->nama }} ({{ $siswa->nisn }}) - {{ $siswa->kelas->nama_kelas ?? '-' }}
                        </td>
                    </tr>
                    <tr>
                        <th width="5%">No</th>
                        <th width="35%">Prestasi</th>
                        <th width="20%">Kategori</th>
                        <th width="15%">Status</th>
                    </tr>
                    @foreach($prestasiAnak as $index => $p)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $p->nama_prestasi }}</strong><br>
                            <small>{{ $p->penyelenggara }} - {{ \Carbon\Carbon::parse($p->tanggal_prestasi)->format('d/m/Y') }}</small>
                        </td>
                        <td>{{ $p->kategori->nama_kategori }} ({{ $p->tingkat->tingkat }})</td>
                        <td>
                            @php
                                $statusClass = match($p->status) {
                                    'diterima' => 'status-diterima',
                                    'menunggu_validasi' => 'status-menunggu',
                                    'draft' => 'status-draft',
                                    'ditolak' => 'status-ditolak',
                                    default => 'status-draft'
                                };
                                $statusText = match($p->status) {
                                    'diterima' => 'Diterima',
                                    'menunggu_validasi' => 'Menunggu',
                                    'draft' => 'Draft',
                                    'ditolak' => 'Ditolak',
                                    default => ucfirst($p->status)
                                };
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                        </td>
                    </tr>
                    @endforeach
                </table>
            @endforeach
        </div>
    @else
        <div class="no-data">
            <p>Tidak ada data prestasi untuk ditampilkan</p>
        </div>
    @endif

    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis oleh sistem</p>
        <p>Halaman 1 dari 1</p>
    </div>
</body>
</html> 