@extends('layouts.app')

@section('title', 'Dashboard Guru')

@section('content')
<div class="container mt-4">
    <h2>Dashboard Guru</h2>
    <div class="alert alert-info mt-3">
        Selamat datang di Dashboard Guru. Silakan gunakan menu di samping untuk mengelola data siswa, prestasi, ekstrakurikuler, dan lainnya sesuai kelas/seksi yang Anda ampu.
    </div>

    <!-- Statistik Cards -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="mb-1">Jumlah Kelas</h6>
                    <h3>{{ $kelas->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="mb-1">Jumlah Siswa</h6>
                    <h3>{{ $siswaCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="mb-1">Total Prestasi</h6>
                    <h3>{{ $totalPrestasi }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="mb-1">Diterima</h6>
                    <h3 class="text-success">{{ $prestasiDiterima }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="mb-1">Pending</h6>
                    <h3 class="text-warning">{{ $prestasiPending }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="mb-1">Ditolak</h6>
                    <h3 class="text-danger">{{ $prestasiDitolak }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Prestasi Siswa di Kelas yang Diampu -->
    <div class="row mt-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Tren Prestasi Siswa (6 Bulan Terakhir)</h5>
                </div>
                <div class="card-body">
                    <div id="prestasi-guru-chart" style="height: 300px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Prestasi per Kategori</h5>
                </div>
                <div class="card-body">
                    <div id="kategori-guru-chart" style="height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.41.0/dist/apexcharts.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Area Chart Prestasi Siswa per Bulan
        var prestasiGuruOptions = {
            series: [{
                name: 'Prestasi',
                data: @json($prestasiPerBulan->pluck('total')->toArray() ?: [0])
            }],
            chart: {
                type: 'area',
                height: 300,
                toolbar: { show: false }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            colors: ['#7367F0'],
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.2, stops: [0, 90, 100] }
            },
            xaxis: {
                categories: @json($prestasiPerBulan->pluck('bulan')->toArray() ?: ['-']),
                labels: {
                    formatter: function(value) {
                        if (!value || value === '-') return '-';
                        return new Date(value + '-01').toLocaleDateString('id-ID', { month: 'short', year: 'numeric' });
                    }
                }
            },
            yaxis: {
                labels: { formatter: function(value) { return Math.round(value); } }
            },
            tooltip: { x: { format: 'MMM yyyy' } }
        };
        var prestasiGuruChart = new ApexCharts(document.querySelector("#prestasi-guru-chart"), prestasiGuruOptions);
        prestasiGuruChart.render();

        // Donut Chart Prestasi per Kategori
        var kategoriGuruOptions = {
            series: @json($prestasiPerKategori->pluck('total')->toArray() ?: [0]),
            chart: { type: 'donut', height: 300 },
            labels: @json($prestasiPerKategori->pluck('kategori')->toArray() ?: ['-']),
            colors: ['#7367F0', '#28C76F', '#EA5455', '#FF9F43', '#1E9FF2'],
            plotOptions: { pie: { donut: { size: '65%' } } },
            legend: { position: 'bottom' }
        };
        var kategoriGuruChart = new ApexCharts(document.querySelector("#kategori-guru-chart"), kategoriGuruOptions);
        kategoriGuruChart.render();
    });
    </script>

    <!-- Tabel Siswa di Kelas yang Diampu -->
    <div class="row mt-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Daftar Siswa di Kelas yang Diampu</h5>
                </div>
                <div class="card-body">
                    @if($siswa->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Kelas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($siswa as $sis)
                                <tr>
                                    <td>{{ $sis->nama }}</td>
                                    <td>{{ $sis->kelas ? $sis->kelas->nama_kelas : '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="ti ti-user ti-2x text-muted mb-2"></i>
                        <p class="text-muted">Belum ada siswa di kelas yang diampu</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- Tabel Prestasi Terbaru Siswa -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Prestasi Terbaru Siswa</h5>
                </div>
                <div class="card-body">
                    @if($prestasiTerbaru->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama Siswa</th>
                                    <th>Prestasi</th>
                                    <th>Kategori</th>
                                    <th>Tingkat</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($prestasiTerbaru as $prestasi)
                                <tr>
                                    <td>{{ $prestasi->siswa->nama ?? '-' }}</td>
                                    <td>{{ $prestasi->nama_prestasi }}</td>
                                    <td>{{ $prestasi->kategori->nama_kategori ?? '-' }}</td>
                                    <td>{{ $prestasi->tingkat->tingkat ?? '-' }}</td>
                                    <td>
                                        @php
                                            $statusBadge = match($prestasi->status) {
                                                'diterima' => 'success',
                                                'menunggu_validasi' => 'warning',
                                                'draft' => 'secondary',
                                                'ditolak' => 'danger',
                                                default => 'secondary'
                                            };
                                            $statusText = match($prestasi->status) {
                                                'diterima' => 'Diterima',
                                                'menunggu_validasi' => 'Menunggu',
                                                'draft' => 'Draft',
                                                'ditolak' => 'Ditolak',
                                                default => ucfirst($prestasi->status)
                                            };
                                        @endphp
                                        <span class="badge bg-light-{{ $statusBadge }} text-{{ $statusBadge }}">{{ $statusText }}</span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($prestasi->tanggal_prestasi)->format('d/m/Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="ti ti-trophy ti-2x text-muted mb-2"></i>
                        <p class="text-muted">Belum ada prestasi terbaru</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
