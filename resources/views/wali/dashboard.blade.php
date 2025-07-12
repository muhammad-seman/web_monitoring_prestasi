@extends('layouts.app')
@section('title', 'Dashboard Wali')
@section('content')

<!-- Row 1 -->
<div class="row">
  <div class="col-lg-12">
    <div class="row">
      <div class="col-lg-3">
        <div class="card overflow-hidden">
          <div class="card-body p-4">
            <h5 class="card-title mb-9 fw-semibold">Total Prestasi</h5>
            <div class="row align-items-center">
              <div class="col-8">
                <h4 class="fw-semibold mb-3">{{ $totalPrestasi }}</h4>
                <div class="d-flex align-items-center mb-3">
                  <span class="me-1 rounded-circle bg-light-success round-20 d-flex align-items-center justify-content-center">
                    <i class="ti ti-trophy text-success"></i>
                  </span>
                  <p class="text-dark me-1 fs-3 mb-0">Prestasi Anak</p>
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
                <h4 class="fw-semibold mb-3">{{ $prestasiDiterima }}</h4>
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
                <h4 class="fw-semibold mb-3">{{ $prestasiMenunggu }}</h4>
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
                <h4 class="fw-semibold mb-3">{{ $prestasiDitolak }}</h4>
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

<!-- Row 2 -->
<div class="row">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-semibold mb-4">Prestasi Terbaru Anak</h5>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Anak</th>
                <th>Prestasi</th>
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
                  <div class="d-flex align-items-center">
                    <div class="me-2 pe-1">
                      <span class="round-8 bg-primary rounded-circle me-2 d-inline-block"></span>
                    </div>
                    <div class="d-flex flex-column">
                      <h6 class="mb-1 fw-semibold">{{ $prestasi->siswa->nama }}</h6>
                      <span class="text-muted fs-3">{{ $prestasi->siswa->kelas->nama_kelas ?? '-' }}</span>
                    </div>
                  </div>
                </td>
                <td>
                  <h6 class="mb-1 fw-semibold">{{ $prestasi->nama_prestasi }}</h6>
                  <span class="text-muted fs-3">{{ $prestasi->penyelenggara }}</span>
                </td>
                <td>
                  <span class="badge bg-light-primary text-primary">{{ $prestasi->kategori->nama_kategori }}</span>
                </td>
                <td>
                  <span class="badge bg-light-info text-info">{{ $prestasi->tingkat->tingkat }}</span>
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
                <td colspan="6" class="text-center">Belum ada prestasi terbaru</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-semibold mb-4">Data Anak</h5>
        @forelse($anak as $siswa)
        <div class="d-flex align-items-center mb-4">
          <div class="me-3">
            <span class="round-8 bg-primary rounded-circle me-2 d-inline-block"></span>
          </div>
          <div class="flex-grow-1">
            <h6 class="mb-1 fw-semibold">{{ $siswa->nama }}</h6>
            <span class="text-muted fs-3">{{ $siswa->nisn }}</span>
            <div class="mt-1">
              <span class="badge bg-light-info text-info">{{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}</span>
            </div>
            <div class="mt-1">
              <small class="text-muted">
                {{ $siswa->prestasi->count() }} prestasi
                @if($siswa->prestasi->where('status', 'diterima')->count() > 0)
                  • {{ $siswa->prestasi->where('status', 'diterima')->count() }} diterima
                @endif
              </small>
            </div>
          </div>
        </div>
        @empty
        <div class="text-center text-muted">
          <i class="ti ti-user-off fs-1 mb-3"></i>
          <p>Belum ada anak yang di-assign</p>
        </div>
        @endforelse
      </div>
    </div>
  </div>
</div>

@endsection 