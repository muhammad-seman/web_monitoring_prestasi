@extends('layouts.app')
@section('title', 'Dashboard Kepala Sekolah')
@section('content')

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Dashboard Kepala Sekolah</h4>
        <p class="card-subtitle mb-0">Statistik Global Sekolah</p>
      </div>
    </div>
  </div>
</div>

<!-- Statistik Cards -->
<div class="row">
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="fw-semibold d-block mb-1">Total Siswa</span>
            <div class="d-flex align-items-end mt-2">
              <h4 class="mb-0 me-2">{{ number_format($totalSiswa) }}</h4>
            </div>
          </div>
          <span class="badge bg-label-primary rounded p-2">
            <i class="ti ti-user ti-sm"></i>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="fw-semibold d-block mb-1">Total Prestasi</span>
            <div class="d-flex align-items-end mt-2">
              <h4 class="mb-0 me-2">{{ number_format($totalPrestasi) }}</h4>
            </div>
          </div>
          <span class="badge bg-label-success rounded p-2">
            <i class="ti ti-trophy ti-sm"></i>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="fw-semibold d-block mb-1">Total Kelas</span>
            <div class="d-flex align-items-end mt-2">
              <h4 class="mb-0 me-2">{{ number_format($totalKelas) }}</h4>
            </div>
          </div>
          <span class="badge bg-label-info rounded p-2">
            <i class="ti ti-school ti-sm"></i>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="fw-semibold d-block mb-1">Ekstrakurikuler</span>
            <div class="d-flex align-items-end mt-2">
              <h4 class="mb-0 me-2">{{ number_format($totalEkstrakurikuler) }}</h4>
            </div>
          </div>
          <span class="badge bg-label-warning rounded p-2">
            <i class="ti ti-users ti-sm"></i>
          </span>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Status Prestasi -->
<div class="row">
  <div class="col-lg-4">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Status Prestasi</h5>
      </div>
      <div class="card-body">
        <div class="d-flex justify-content-between mb-3">
          <span>Pending</span>
          <span class="badge bg-warning">{{ $prestasiPending }}</span>
        </div>
        <div class="d-flex justify-content-between mb-3">
          <span>Approved</span>
          <span class="badge bg-success">{{ $prestasiApproved }}</span>
        </div>
        <div class="d-flex justify-content-between">
          <span>Rejected</span>
          <span class="badge bg-danger">{{ $prestasiRejected }}</span>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Prestasi Berdasarkan Tingkat</h5>
      </div>
      <div class="card-body">
        @if($prestasiTingkat->count() > 0)
          @foreach($prestasiTingkat as $tingkat)
          <div class="d-flex justify-content-between mb-2">
            <span>{{ $tingkat->tingkat }}</span>
            <span class="badge bg-primary">{{ $tingkat->total }}</span>
          </div>
          @endforeach
        @else
          <p class="text-muted">Belum ada data prestasi</p>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Prestasi Berdasarkan Kategori -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Prestasi Berdasarkan Kategori</h5>
      </div>
      <div class="card-body">
        @if($prestasiKategori->count() > 0)
          <div class="row">
            @foreach($prestasiKategori as $kategori)
            <div class="col-md-3 mb-3">
              <div class="border rounded p-3 text-center">
                <h6>{{ $kategori->nama_kategori }}</h6>
                <h4 class="text-primary">{{ $kategori->total }}</h4>
              </div>
            </div>
            @endforeach
          </div>
        @else
          <p class="text-muted text-center">Belum ada data prestasi</p>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Prestasi Per Bulan -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Prestasi Per Bulan (6 Bulan Terakhir)</h5>
      </div>
      <div class="card-body">
        @if($prestasiPerBulan->count() > 0)
          <div class="row">
            @foreach($prestasiPerBulan as $bulan)
            <div class="col-md-2 mb-3">
              <div class="border rounded p-3 text-center">
                @php
                  $label = '-';
                  if (!empty($bulan->bulan) && strpos($bulan->bulan, '-') !== false) {
                      [$tahun, $bln] = explode('-', $bulan->bulan);
                      $label = date('M', mktime(0, 0, 0, (int)$bln, 1, (int)$tahun));
                  }
                @endphp
                <h6>{{ $label }}</h6>
                <h4 class="text-info">{{ $bulan->total }}</h4>
              </div>
            </div>
            @endforeach
          </div>
        @else
          <p class="text-muted text-center">Belum ada data prestasi</p>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Row Grafik Prestasi -->
<div class="row">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Tren Prestasi (6 Bulan Terakhir)</h5>
      </div>
      <div class="card-body">
        <div id="prestasi-chart" style="height: 300px;"></div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Prestasi per Kategori</h5>
      </div>
      <div class="card-body">
        <div id="kategori-chart" style="height: 300px;"></div>
      </div>
    </div>
  </div>
</div>
<!-- CDN ApexCharts dan script chart -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.41.0/dist/apexcharts.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Area Chart Prestasi per Bulan
    var prestasiOptions = {
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
                    // value: 'YYYY-MM'
                    var parts = value.split('-');
                    if(parts.length === 2) {
                        return new Date(parts[0] + '-' + parts[1].padStart(2, '0') + '-01').toLocaleDateString('id-ID', { month: 'short', year: 'numeric' });
                    }
                    return value;
                }
            }
        },
        yaxis: {
            labels: { formatter: function(value) { return Math.round(value); } }
        },
        tooltip: { x: { format: 'MMM yyyy' } }
    };
    var prestasiChart = new ApexCharts(document.querySelector("#prestasi-chart"), prestasiOptions);
    prestasiChart.render();

    // Donut Chart Prestasi per Kategori
    var kategoriOptions = {
        series: @json($prestasiKategori->pluck('total')->toArray() ?: [0]),
        chart: { type: 'donut', height: 300 },
        labels: @json($prestasiKategori->pluck('nama_kategori')->toArray() ?: ['-']),
        colors: ['#7367F0', '#28C76F', '#EA5455', '#FF9F43', '#1E9FF2'],
        plotOptions: { pie: { donut: { size: '65%' } } },
        legend: { position: 'bottom' }
    };
    var kategoriChart = new ApexCharts(document.querySelector("#kategori-chart"), kategoriOptions);
    kategoriChart.render();
});
</script>

<!-- Row Tabel Top Kelas dan Ekskul -->
<div class="row">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Top 5 Kelas dengan Prestasi Terbanyak</h5>
      </div>
      <div class="card-body">
        @if($topKelasPrestasi->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Kelas</th>
                  <th>Total Prestasi</th>
                  <th>Persentase</th>
                </tr>
              </thead>
              <tbody>
                @foreach($topKelasPrestasi as $kelas)
                <tr>
                  <td>{{ $kelas->nama_kelas }}</td>
                  <td>{{ $kelas->total_prestasi }}</td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="progress flex-grow-1 me-2" style="height: 6px;">
                        <div class="progress-bar" style="width: {{ $totalPrestasi > 0 ? ($kelas->total_prestasi / $totalPrestasi) * 100 : 0 }}%"></div>
                      </div>
                      <span class="text-muted small">
                        {{ $totalPrestasi > 0 ? round(($kelas->total_prestasi / $totalPrestasi) * 100, 1) : 0 }}%
                        <br><small>(dari total prestasi)</small>
                      </span>
                    </div>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="text-center py-4">
            <i class="ti ti-school ti-2x text-muted mb-2"></i>
            <p class="text-muted">Belum ada data kelas dengan prestasi</p>
          </div>
        @endif
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Top 5 Ekstrakurikuler</h5>
      </div>
      <div class="card-body">
        @if($topEkskul->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Ekstrakurikuler</th>
                  <th>Total Anggota</th>
                  <th>Persentase</th>
                </tr>
              </thead>
              <tbody>
                @foreach($topEkskul as $ekskul)
                <tr>
                  <td>{{ $ekskul->nama }}</td>
                  <td>{{ $ekskul->total_anggota }}</td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="progress flex-grow-1 me-2" style="height: 6px;">
                        <div class="progress-bar bg-info" style="width: {{ $totalSiswa > 0 ? ($ekskul->total_anggota / $totalSiswa) * 100 : 0 }}%"></div>
                      </div>
                      <span class="text-muted small">
                        {{ $totalSiswa > 0 ? round(($ekskul->total_anggota / $totalSiswa) * 100, 1) : 0 }}%
                        <br><small>(dari total siswa)</small>
                      </span>
                    </div>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="text-center py-4">
            <i class="ti ti-activity ti-2x text-muted mb-2"></i>
            <p class="text-muted">Belum ada data ekstrakurikuler</p>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Row Aktivitas & Prestasi Terbaru -->
<div class="row">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Aktivitas Terbaru</h5>
      </div>
      <div class="card-body">
        @if($aktivitasTerbaru->count() > 0)
          <div class="timeline">
            @foreach($aktivitasTerbaru as $aktivitas)
            <div class="timeline-item">
              <div class="timeline-marker"></div>
              <div class="timeline-content">
                <h6 class="mb-1">{{ $aktivitas->action }}</h6>
                <p class="text-muted mb-0">{{ $aktivitas->description }}</p>
                <small class="text-muted">{{ $aktivitas->user->nama ?? 'System' }} • {{ $aktivitas->created_at->diffForHumans() }}</small>
              </div>
            </div>
            @endforeach
          </div>
        @else
          <div class="text-center py-4">
            <i class="ti ti-activity ti-2x text-muted mb-2"></i>
            <p class="text-muted">Belum ada aktivitas terbaru</p>
          </div>
        @endif
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Prestasi Terbaru</h5>
      </div>
      <div class="card-body">
        @if($prestasiTerbaru->count() > 0)
          @foreach($prestasiTerbaru as $prestasi)
          <div class="d-flex align-items-start mb-3">
            <div class="flex-shrink-0">
              <div class="avatar avatar-sm me-3">
                <span class="avatar-initial rounded bg-label-success">
                  <i class="ti ti-trophy"></i>
                </span>
              </div>
            </div>
            <div class="flex-grow-1">
              <h6 class="mb-1">{{ $prestasi->nama_prestasi }}</h6>
              <p class="text-muted mb-0">{{ $prestasi->siswa->nama ?? 'N/A' }} • {{ $prestasi->kategoriPrestasi->nama_kategori ?? 'N/A' }}</p>
              <small class="text-muted">{{ $prestasi->tingkatPenghargaan->tingkat ?? 'N/A' }} • {{ $prestasi->created_at->diffForHumans() }}</small>
            </div>
          </div>
          @endforeach
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

@endsection 