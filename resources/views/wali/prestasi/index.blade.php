@extends('layouts.app')
@section('title', 'Prestasi Anak')
@section('content')

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body">
        <div class="d-md-flex align-items-center justify-content-between">
          <h4 class="card-title">Prestasi Anak</h4>
          <a href="{{ route('wali.prestasi.cetak', request()->all()) }}" class="btn btn-success" target="_blank">
            <i class="ti ti-printer"></i> Cetak Rekap
          </a>
        </div>

        <!-- Filter -->
        <div class="row mt-4">
          <div class="col-md-12">
            <form method="GET" class="row g-3">
              <div class="col-md-2">
                <label class="form-label">Kategori</label>
                <select name="kategori" class="form-select">
                  <option value="">Semua Kategori</option>
                  @foreach($kategori as $id => $nama)
                    <option value="{{ $id }}" {{ request('kategori') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label">Tingkat</label>
                <select name="tingkat" class="form-select">
                  <option value="">Semua Tingkat</option>
                  @foreach($tingkat as $id => $nama)
                    <option value="{{ $id }}" {{ request('tingkat') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label">Ekstrakurikuler</label>
                <select name="ekskul" class="form-select">
                  <option value="">Semua Ekskul</option>
                  @foreach($ekskul as $id => $nama)
                    <option value="{{ $id }}" {{ request('ekskul') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                  <option value="">Semua Status</option>
                  <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                  <option value="menunggu_validasi" {{ request('status') == 'menunggu_validasi' ? 'selected' : '' }}>Menunggu Validasi</option>
                  <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                  <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="from" class="form-control" value="{{ request('from') }}">
              </div>
              <div class="col-md-2">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="to" class="form-control" value="{{ request('to') }}">
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-primary">
                  <i class="ti ti-filter"></i> Filter
                </button>
                <a href="{{ route('wali.prestasi.index') }}" class="btn btn-secondary">
                  <i class="ti ti-refresh"></i> Reset
                </a>
              </div>
            </form>
          </div>
        </div>

        <!-- Tabel Prestasi -->
        <div class="table-responsive mt-4">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>No</th>
                <th>Anak</th>
                <th>Prestasi</th>
                <th>Kategori</th>
                <th>Tingkat</th>
                <th>Ekstrakurikuler</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Detail</th>
              </tr>
            </thead>
            <tbody>
              @forelse($prestasi as $p)
              <tr>
                <td>{{ $loop->iteration + ($prestasi->perPage() * ($prestasi->currentPage()-1)) }}</td>
                <td>
                  <div>
                    <h6 class="mb-0">{{ $p->siswa->nama }}</h6>
                    <small class="text-muted">{{ $p->siswa->nisn }} - {{ $p->siswa->kelas->nama_kelas ?? '-' }}</small>
                  </div>
                </td>
                <td>
                  <div>
                    <h6 class="mb-0">{{ $p->nama_prestasi }}</h6>
                    <small class="text-muted">{{ $p->penyelenggara }}</small>
                  </div>
                </td>
                <td>
                  <span class="badge bg-light-primary text-primary">{{ $p->kategori->nama_kategori }}</span>
                </td>
                <td>
                  <span class="badge bg-light-info text-info">{{ $p->tingkat->tingkat }}</span>
                </td>
                <td>
                  @if($p->ekskul)
                    <span class="badge bg-light-success text-success">{{ $p->ekskul->nama }}</span>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>
                  @php
                    $statusBadge = match($p->status) {
                      'diterima' => 'success',
                      'menunggu_validasi' => 'warning',
                      'draft' => 'secondary',
                      'ditolak' => 'danger',
                      default => 'secondary'
                    };
                    $statusText = match($p->status) {
                      'diterima' => 'Diterima',
                      'menunggu_validasi' => 'Menunggu',
                      'draft' => 'Draft',
                      'ditolak' => 'Ditolak',
                      default => ucfirst($p->status)
                    };
                  @endphp
                  <span class="badge bg-light-{{ $statusBadge }} text-{{ $statusBadge }}">{{ $statusText }}</span>
                </td>
                <td>
                  <small class="text-muted">{{ \Carbon\Carbon::parse($p->tanggal_prestasi)->format('d/m/Y') }}</small>
                </td>
                <td>
                  <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal{{ $p->id }}">
                    <i class="ti ti-eye"></i> Detail
                  </button>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="9" class="text-center">Tidak ada data prestasi</td>
              </tr>
              @endforelse
            </tbody>
          </table>
          
          {{ $prestasi->appends(request()->all())->links("pagination::bootstrap-4") }}
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Detail -->
@foreach($prestasi as $p)
<div class="modal fade" id="detailModal{{ $p->id }}" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Prestasi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <h6 class="fw-semibold">Informasi Siswa</h6>
            <table class="table table-borderless">
              <tr>
                <td width="120">Nama</td>
                <td>: {{ $p->siswa->nama }}</td>
              </tr>
              <tr>
                <td>NISN</td>
                <td>: {{ $p->siswa->nisn }}</td>
              </tr>
              <tr>
                <td>Kelas</td>
                <td>: {{ $p->siswa->kelas->nama_kelas ?? '-' }}</td>
              </tr>
            </table>
          </div>
          <div class="col-md-6">
            <h6 class="fw-semibold">Informasi Prestasi</h6>
            <table class="table table-borderless">
              <tr>
                <td width="120">Nama Prestasi</td>
                <td>: {{ $p->nama_prestasi }}</td>
              </tr>
              <tr>
                <td>Penyelenggara</td>
                <td>: {{ $p->penyelenggara }}</td>
              </tr>
              <tr>
                <td>Tanggal</td>
                <td>: {{ \Carbon\Carbon::parse($p->tanggal_prestasi)->format('d F Y') }}</td>
              </tr>
            </table>
          </div>
        </div>
        
        <div class="row mt-3">
          <div class="col-md-6">
            <h6 class="fw-semibold">Kategori & Tingkat</h6>
            <table class="table table-borderless">
              <tr>
                <td width="120">Kategori</td>
                <td>: {{ $p->kategori->nama_kategori }}</td>
              </tr>
              <tr>
                <td>Tingkat</td>
                <td>: {{ $p->tingkat->tingkat }}</td>
              </tr>
              <tr>
                <td>Ekstrakurikuler</td>
                <td>: {{ $p->ekskul->nama ?? '-' }}</td>
              </tr>
            </table>
          </div>
          <div class="col-md-6">
            <h6 class="fw-semibold">Status & Validasi</h6>
            <table class="table table-borderless">
              <tr>
                <td width="120">Status</td>
                <td>: 
                  @php
                    $statusBadge = match($p->status) {
                      'diterima' => 'success',
                      'menunggu_validasi' => 'warning',
                      'draft' => 'secondary',
                      'ditolak' => 'danger',
                      default => 'secondary'
                    };
                    $statusText = match($p->status) {
                      'diterima' => 'Diterima',
                      'menunggu_validasi' => 'Menunggu',
                      'draft' => 'Draft',
                      'ditolak' => 'Ditolak',
                      default => ucfirst($p->status)
                    };
                  @endphp
                  <span class="badge bg-{{ $statusBadge }}">{{ $statusText }}</span>
                </td>
              </tr>
              <tr>
                <td>Divalidasi Oleh</td>
                <td>: {{ $p->validator->nama ?? '-' }}</td>
              </tr>
              <tr>
                <td>Tanggal Validasi</td>
                <td>: {{ $p->validated_at ? \Carbon\Carbon::parse($p->validated_at)->format('d/m/Y H:i') : '-' }}</td>
              </tr>
            </table>
          </div>
        </div>
        
        @if($p->keterangan)
        <div class="mt-3">
          <h6 class="fw-semibold">Keterangan</h6>
          <p class="text-muted">{{ $p->keterangan }}</p>
        </div>
        @endif
        
        @if($p->alasan_tolak)
        <div class="mt-3">
          <h6 class="fw-semibold text-danger">Alasan Penolakan</h6>
          <p class="text-danger">{{ $p->alasan_tolak }}</p>
        </div>
        @endif
        
        @if($p->dokumen_url)
        <div class="mt-3">
          <h6 class="fw-semibold">Dokumen Bukti</h6>
          <a href="{{ asset($p->dokumen_url) }}" target="_blank" class="btn btn-sm btn-primary">
            <i class="ti ti-download"></i> Lihat Dokumen
          </a>
        </div>
        @endif
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
@endforeach

@endsection 