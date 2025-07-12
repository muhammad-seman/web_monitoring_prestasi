@extends('layouts.app')
@section('title', 'Data Anak')
@section('content')

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Data Anak</h4>
        <p class="card-subtitle mb-4">Informasi lengkap anak-anak yang Anda wali</p>

        @forelse($anak as $siswa)
        <div class="card mb-4">
          <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="mb-0">{{ $siswa->nama }}</h5>
              <span class="badge bg-primary">{{ $siswa->nisn }}</span>
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <h6 class="fw-semibold mb-3">Informasi Pribadi</h6>
                <table class="table table-borderless">
                  <tr>
                    <td width="120">NISN</td>
                    <td>: {{ $siswa->nisn }}</td>
                  </tr>
                  <tr>
                    <td>Jenis Kelamin</td>
                    <td>: {{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                  </tr>
                  <tr>
                    <td>Tanggal Lahir</td>
                    <td>: {{ $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('d F Y') : '-' }}</td>
                  </tr>
                  <tr>
                    <td>Alamat</td>
                    <td>: {{ $siswa->alamat ?? '-' }}</td>
                  </tr>
                  <tr>
                    <td>Tahun Masuk</td>
                    <td>: {{ $siswa->tahun_masuk ?? '-' }}</td>
                  </tr>
                </table>
              </div>
              <div class="col-md-6">
                <h6 class="fw-semibold mb-3">Informasi Akademik</h6>
                <table class="table table-borderless">
                  <tr>
                    <td width="120">Kelas</td>
                    <td>: {{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}</td>
                  </tr>
                  <tr>
                    <td>Wali Kelas</td>
                    <td>: {{ $siswa->kelas->wali->nama ?? '-' }}</td>
                  </tr>
                  <tr>
                    <td>Total Prestasi</td>
                    <td>: {{ $siswa->prestasi->count() }} prestasi</td>
                  </tr>
                  <tr>
                    <td>Prestasi Diterima</td>
                    <td>: {{ $siswa->prestasi->where('status', 'diterima')->count() }} prestasi</td>
                  </tr>
                  <tr>
                    <td>Ekstrakurikuler</td>
                    <td>: {{ $siswa->ekstrakurikuler->count() }} ekskul</td>
                  </tr>
                </table>
              </div>
            </div>

            <!-- Ekstrakurikuler -->
            @if($siswa->ekstrakurikuler->count() > 0)
            <div class="mt-4">
              <h6 class="fw-semibold mb-3">Ekstrakurikuler yang Diikuti</h6>
              <div class="row">
                @foreach($siswa->ekstrakurikuler as $ekskul)
                <div class="col-md-4 mb-2">
                  <div class="d-flex align-items-center p-2 border rounded">
                    <i class="ti ti-school text-primary me-2"></i>
                    <div>
                      <h6 class="mb-0">{{ $ekskul->nama }}</h6>
                      <small class="text-muted">{{ $ekskul->pembina ?? 'Pembina belum ditentukan' }}</small>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
            </div>
            @endif

            <!-- Prestasi Terbaru -->
            @if($siswa->prestasi->count() > 0)
            <div class="mt-4">
              <h6 class="fw-semibold mb-3">Prestasi Terbaru (5 Terakhir)</h6>
              <div class="table-responsive">
                <table class="table table-sm">
                  <thead>
                    <tr>
                      <th>Prestasi</th>
                      <th>Kategori</th>
                      <th>Tingkat</th>
                      <th>Status</th>
                      <th>Tanggal</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($siswa->prestasi->take(5) as $prestasi)
                    <tr>
                      <td>
                        <div>
                          <h6 class="mb-0">{{ $prestasi->nama_prestasi }}</h6>
                          <small class="text-muted">{{ $prestasi->penyelenggara }}</small>
                        </div>
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
                        <small class="text-muted">{{ \Carbon\Carbon::parse($prestasi->tanggal_prestasi)->format('d/m/Y') }}</small>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
            @endif
          </div>
        </div>
        @empty
        <div class="text-center py-5">
          <i class="ti ti-user-off fs-1 text-muted mb-3"></i>
          <h5 class="text-muted">Belum ada anak yang di-assign</h5>
          <p class="text-muted">Silakan hubungi admin untuk meng-assign anak Anda</p>
        </div>
        @endforelse
      </div>
    </div>
  </div>
</div>

@endsection 