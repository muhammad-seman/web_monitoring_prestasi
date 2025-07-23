@extends('layouts.letterhead', [
    'title' => 'Rekap Prestasi Anak - Wali',
    'date' => 'Barabai, ' . date('d F Y'),
    'letterType' => 'Wali Siswa',
    'signatureName' => Auth::user()->name ?? 'Wali Siswa',
    'signatureTitle' => 'Wali Siswa'
])

@section('content')
    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="margin: 0; font-size: 16px; font-weight: bold;">REKAP PRESTASI ANAK</h1>
        <p style="margin: 5px 0; font-size: 12px;">Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
        <p style="margin: 5px 0; font-size: 12px;">Oleh: {{ auth()->user()->name }} (Wali)</p>
    </div>

    <div style="margin-bottom: 20px;">
        <h3 style="margin: 0 0 10px 0; font-size: 14px; font-weight: bold; border-bottom: 1px solid #ccc; padding-bottom: 5px;">Ringkasan Data</h3>
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
        <div style="margin-bottom: 20px;">
            <h3 style="margin: 0 0 10px 0; font-size: 14px; font-weight: bold; border-bottom: 1px solid #ccc; padding-bottom: 5px;">Detail Prestasi</h3>
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
                                $statusStyle = match($statusClass) {
                                    'status-diterima' => 'background-color: #d4edda; color: #155724;',
                                    'status-menunggu' => 'background-color: #fff3cd; color: #856404;',
                                    'status-draft' => 'background-color: #e2e3e5; color: #383d41;',
                                    'status-ditolak' => 'background-color: #f8d7da; color: #721c24;',
                                    default => 'background-color: #e2e3e5; color: #383d41;'
                                };
                            @endphp
                            <span style="padding: 2px 6px; border-radius: 3px; font-size: 10px; font-weight: bold; {{ $statusStyle }}">{{ $statusText }}</span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($p->tanggal_prestasi)->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-bottom: 20px;">
            <h3 style="margin: 0 0 10px 0; font-size: 14px; font-weight: bold; border-bottom: 1px solid #ccc; padding-bottom: 5px;">Rekap per Anak</h3>
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
                                $statusStyle = match($statusClass) {
                                    'status-diterima' => 'background-color: #d4edda; color: #155724;',
                                    'status-menunggu' => 'background-color: #fff3cd; color: #856404;',
                                    'status-draft' => 'background-color: #e2e3e5; color: #383d41;',
                                    'status-ditolak' => 'background-color: #f8d7da; color: #721c24;',
                                    default => 'background-color: #e2e3e5; color: #383d41;'
                                };
                            @endphp
                            <span style="padding: 2px 6px; border-radius: 3px; font-size: 10px; font-weight: bold; {{ $statusStyle }}">{{ $statusText }}</span>
                        </td>
                    </tr>
                    @endforeach
                </table>
            @endforeach
        </div>
    @else
        <div style="text-align: center; font-style: italic; color: #666; padding: 20px;">
            <p>Tidak ada data prestasi untuk ditampilkan</p>
        </div>
    @endif

    <div style="margin-top: 30px; text-align: center; font-size: 11px;">
        <p>Dokumen ini dicetak secara otomatis oleh sistem</p>
        <p>Halaman 1 dari 1</p>
    </div>
@endsection