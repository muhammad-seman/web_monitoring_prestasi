@extends('layouts.app')
@section('title', 'Dashboard Siswa')
@section('content')

<!-- Row 1 - Statistics Cards -->
<div class="row">
  <div class="col-lg-12">
    <div class="row">
      <div class="col-lg-3">
        <div class="card overflow-hidden">
          <div class="card-body p-4">
            <h5 class="card-title mb-9 fw-semibold">Total Prestasi</h5>
            <div class="row align-items-center">
              <div class="col-8">
                <h4 class="fw-semibold mb-3">{{ $total }}</h4>
                <div class="d-flex align-items-center mb-3">
                  <span class="me-1 rounded-circle bg-light-success round-20 d-flex align-items-center justify-content-center">
                    <i class="ti ti-trophy text-success"></i>
                  </span>
                  <p class="text-dark me-1 fs-3 mb-0">Prestasi Sekolah</p>
                </div>
              </div>
              <div class="col-4">
                <div class="d-flex justify-content-center">
                  <div id="breakup"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3">
        <div class="card overflow-hidden">
          <div class="card-body p-4">
            <h5 class="card-title mb-9 fw-semibold">Prestasi Diterima</h5>
            <div class="row align-items-center">
              <div class="col-8">
                <h4 class="fw-semibold mb-3">{{ $diterima }}</h4>
                <div class="d-flex align-items-center mb-3">
                  <span class="me-1 rounded-circle bg-light-success round-20 d-flex align-items-center justify-content-center">
                    <i class="ti ti-check text-success"></i>
                  </span>
                  <p class="text-dark me-1 fs-3 mb-0">Diterima</p>
                </div>
              </div>
              <div class="col-4">
                <div class="d-flex justify-content-center">
                  <div id="breakup"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3">
        <div class="card overflow-hidden">
          <div class="card-body p-4">
            <h5 class="card-title mb-9 fw-semibold">Menunggu Validasi</h5>
            <div class="row align-items-center">
              <div class="col-8">
                <h4 class="fw-semibold mb-3">{{ $pending }}</h4>
                <div class="d-flex align-items-center mb-3">
                  <span class="me-1 rounded-circle bg-light-warning round-20 d-flex align-items-center justify-content-center">
                    <i class="ti ti-clock text-warning"></i>
                  </span>
                  <p class="text-dark me-1 fs-3 mb-0">Menunggu</p>
                </div>
              </div>
              <div class="col-4">
                <div class="d-flex justify-content-center">
                  <div id="breakup"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3">
        <div class="card overflow-hidden">
          <div class="card-body p-4">
            <h5 class="card-title mb-9 fw-semibold">Prestasi Ditolak</h5>
            <div class="row align-items-center">
              <div class="col-8">
                <h4 class="fw-semibold mb-3">{{ $ditolak }}</h4>
                <div class="d-flex align-items-center mb-3">
                  <span class="me-1 rounded-circle bg-light-danger round-20 d-flex align-items-center justify-content-center">
                    <i class="ti ti-x text-danger"></i>
                  </span>
                  <p class="text-dark me-1 fs-3 mb-0">Ditolak</p>
                </div>
              </div>
              <div class="col-4">
                <div class="d-flex justify-content-center">
                  <div id="breakup"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Row Grafik Prestasi Pribadi -->
<div class="row">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Tren Prestasi Sekolah (6 Bulan Terakhir)</h5>
      </div>
      <div class="card-body">
        <div id="prestasi-siswa-chart" style="height: 300px;"></div>
        @if($prestasiPerBulan->count() == 0)
          <div class="text-center text-muted mt-2">Belum ada data tren prestasi</div>
        @endif
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Prestasi Sekolah per Kategori</h5>
      </div>
      <div class="card-body">
        <div id="kategori-siswa-chart" style="height: 300px;"></div>
        @if($prestasiPerKategori->count() == 0)
          <div class="text-center text-muted mt-2">Belum ada data kategori prestasi</div>
        @endif
      </div>
    </div>
  </div>
</div>
                <!-- CDN ApexCharts dan script chart -->
                <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.41.0/dist/apexcharts.min.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Area Chart Prestasi Pribadi per Bulan
                        var prestasiSiswaOptions = {
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
                        var prestasiSiswaChart = new ApexCharts(document.querySelector("#prestasi-siswa-chart"), prestasiSiswaOptions);
                        prestasiSiswaChart.render();

                        // Donut Chart Prestasi per Kategori
                        var kategoriSiswaOptions = {
                            series: @json($prestasiPerKategori->pluck('total')->toArray() ?: [0]),
                            chart: { type: 'donut', height: 300 },
                            labels: @json($prestasiPerKategori->pluck('kategori')->toArray() ?: ['-']),
                            colors: ['#7367F0', '#28C76F', '#EA5455', '#FF9F43', '#1E9FF2'],
                            plotOptions: { pie: { donut: { size: '65%' } } },
                            legend: { position: 'bottom' }
                        };
                        var kategoriSiswaChart = new ApexCharts(document.querySelector("#kategori-siswa-chart"), kategoriSiswaOptions);
                        kategoriSiswaChart.render();
                    });
                </script>

<!-- Row 2 - Prestasi Terbaru -->
<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-semibold mb-4">Prestasi Terbaru Sekolah</h5>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Prestasi Siswa</th>
                                                <th>Kategori</th>
                                                <th>Tingkat</th>
                                                <th>Status</th>
                                                <th>Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($prestasiTerbaru as $prestasi)
                                            <tr>
                                                <td>
                                                    <strong>{{ $prestasi->nama_prestasi }}</strong><br>
                                                    <small class="text-muted">{{ $prestasi->penyelenggara }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-primary text-primary">{{ $prestasi->kategori->nama_kategori ?? '-' }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-info text-info">{{ $prestasi->tingkat->tingkat ?? '-' }}</span>
                                                </td>
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
                                                <td>
                                                    <span class="text-muted fs-3">{{ \Carbon\Carbon::parse($prestasi->tanggal_prestasi)->format('d/m/Y') }}</span>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Belum ada prestasi terbaru</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
      </div>
    </div>
  </div>
</div>

@endsection 