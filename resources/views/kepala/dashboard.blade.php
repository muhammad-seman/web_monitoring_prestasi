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
                <h6>{{ date('M', mktime(0, 0, 0, $bulan->bulan, 1)) }}</h6>
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

@endsection 