@extends('layouts.app')
@section('title', 'Dokumen Prestasi')
@section('content')

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Dokumen Prestasi Anak</h4>
        <p class="card-subtitle mb-4">Dokumen bukti prestasi anak-anak yang Anda wali</p>

        <!-- Filter -->
        <div class="row mb-4">
          <div class="col-md-12">
            <form method="GET" class="row g-3">
              <div class="col-md-3">
                <label class="form-label">Kategori</label>
                <select name="kategori" class="form-select">
                  <option value="">Semua Kategori</option>
                  @foreach($prestasi->pluck('kategori.nama_kategori', 'kategori.id')->unique() as $id => $nama)
                    @if($id && $nama)
                      <option value="{{ $id }}" {{ request('kategori') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                    @endif
                  @endforeach
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                  <option value="">Semua Status</option>
                  <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                  <option value="menunggu_validasi" {{ request('status') == 'menunggu_validasi' ? 'selected' : '' }}>Menunggu Validasi</option>
                  <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                  <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">Anak</label>
                <select name="anak" class="form-select">
                  <option value="">Semua Anak</option>
                  @foreach($prestasi->pluck('siswa.nama', 'siswa.id')->unique() as $id => $nama)
                    @if($id && $nama)
                      <option value="{{ $id }}" {{ request('anak') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                    @endif
                  @endforeach
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div>
                  <button type="submit" class="btn btn-primary">
                    <i class="ti ti-filter"></i> Filter
                  </button>
                  <a href="{{ route('wali.dokumen.index') }}" class="btn btn-secondary">
                    <i class="ti ti-refresh"></i> Reset
                  </a>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Tabel Dokumen -->
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>No</th>
                <th>Anak</th>
                <th>Prestasi</th>
                <th>Kategori</th>
                <th>Status</th>
                <th>Tanggal Prestasi</th>
                <th>Dokumen</th>
                <th>Aksi</th>
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
                  @if($p->dokumen_url)
                    @php
                      $extension = pathinfo($p->dokumen_url, PATHINFO_EXTENSION);
                      $icon = match(strtolower($extension)) {
                        'pdf' => 'ti ti-file-text text-danger',
                        'jpg', 'jpeg', 'png', 'gif' => 'ti ti-photo text-success',
                        'doc', 'docx' => 'ti ti-file text-primary',
                        default => 'ti ti-file text-secondary'
                      };
                    @endphp
                    <i class="{{ $icon }}"></i>
                    <small class="text-muted">{{ strtoupper($extension) }}</small>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>
                  @if($p->dokumen_url)
                    <a href="{{ route('wali.dokumen.download', $p->id) }}" class="btn btn-sm btn-primary" title="Download">
                      <i class="ti ti-download"></i>
                    </a>
                    <a href="{{ asset($p->dokumen_url) }}" target="_blank" class="btn btn-sm btn-info" title="Lihat">
                      <i class="ti ti-eye"></i>
                    </a>
                  @else
                    <span class="text-muted">Tidak ada dokumen</span>
                  @endif
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="8" class="text-center">Tidak ada dokumen prestasi</td>
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

@endsection 