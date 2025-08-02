@extends('layouts.app')
@section('title', 'Prestasi Saya')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Daftar Prestasi Saya</h4>
                    <div>
                        <button class="btn btn-secondary me-2" onclick="cetakLaporan()" title="Cetak Laporan">
                            <i class="ti ti-printer"></i> Cetak Laporan
                        </button>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahPrestasiModal">
                            <i class="ti ti-plus"></i> Tambah Prestasi
                        </button>
                    </div>
                </div>
                <!-- Filter -->
                <form method="GET" class="row mb-3 g-2">
                    <div class="col-md-2">
                        <select name="kategori" class="form-control">
                            <option value="">Semua Kategori</option>
                            @foreach($kategori as $id => $nama)
                                <option value="{{ $id }}" {{ request('kategori') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="tingkat" class="form-control">
                            <option value="">Semua Tingkat</option>
                            @foreach($tingkat as $id => $nama)
                                <option value="{{ $id }}" {{ request('tingkat') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="ekskul" class="form-control">
                            <option value="">Semua Ekskul</option>
                            @foreach($ekskul as $id => $nama)
                                <option value="{{ $id }}" {{ request('ekskul') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="menunggu_validasi" {{ request('status') == 'menunggu_validasi' ? 'selected' : '' }}>Menunggu Validasi</option>
                            <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-secondary" type="submit">Filter</button>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Prestasi</th>
                                <th>Kategori</th>
                                <th>Tingkat</th>
                                <th>Ekskul</th>
                                <th>Penyelenggara</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($prestasi as $p)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $p->nama_prestasi }}</td>
                                <td>{{ $p->kategoriPrestasi->nama_kategori ?? '-' }}</td>
                                <td>{{ $p->tingkatPenghargaan->tingkat ?? '-' }}</td>
                                <td>{{ $p->ekskul->nama ?? '-' }}</td>
                                <td>{{ $p->penyelenggara }}</td>
                                <td>{{ $p->tanggal_prestasi }}</td>
                                <td>
                                    <span class="badge bg-{{ $p->status == 'diterima' ? 'success' : ($p->status == 'ditolak' ? 'danger' : 'warning') }}">
                                        {{ ucfirst(str_replace('_', ' ', $p->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailPrestasiModal{{ $p->id }}">Detail</button>
                                    @if($p->status == 'draft')
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editPrestasiModal{{ $p->id }}">Edit</button>
                                    <form action="{{ route('siswa.prestasi.submit', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin mengajukan prestasi ini untuk validasi?')">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm">Ajukan</button>
                                    </form>
                                    <form action="{{ route('siswa.prestasi.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus prestasi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center">Belum ada data prestasi.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($prestasi as $p)
<!-- Modal Detail -->
<div class="modal fade" id="detailPrestasiModal{{ $p->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Prestasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless">
                    <tr>
                        <th>Nama Prestasi</th>
                        <td>{{ $p->nama_prestasi }}</td>
                    </tr>
                    <tr>
                        <th>Kategori</th>
                        <td>{{ $p->kategoriPrestasi->nama_kategori ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Tingkat</th>
                        <td>{{ $p->tingkatPenghargaan->tingkat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Ekskul</th>
                        <td>{{ $p->ekskul->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Penyelenggara</th>
                        <td>{{ $p->penyelenggara }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>{{ $p->tanggal_prestasi }}</td>
                    </tr>
                    <tr>
                        <th>Keterangan</th>
                        <td>{{ $p->keterangan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Dokumen</th>
                        <td>
                            @if($p->dokumen_url)
                            <a href="{{ asset($p->dokumen_url) }}" target="_blank">Lihat Dokumen</a>
                            @else
                            -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Surat Tugas</th>
                        <td>
                            @if($p->surat_tugas_url)
                            <a href="{{ asset($p->surat_tugas_url) }}" target="_blank">Lihat Surat Tugas</a>
                            @else
                            -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge bg-{{ $p->status == 'diterima' ? 'success' : ($p->status == 'ditolak' ? 'danger' : 'warning') }}">
                                {{ ucfirst(str_replace('_', ' ', $p->status)) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Nilai Rata-rata</th>
                        <td>{{ $p->rata_rata_nilai ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Alasan Tolak</th>
                        <td>{{ $p->alasan_tolak ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Modal Tambah Prestasi -->
<div class="modal fade" id="tambahPrestasiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Prestasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('siswa.prestasi.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Prestasi <span class="text-danger">*</span></label>
                                <input type="text" name="nama_prestasi" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select name="id_kategori_prestasi" class="form-control" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($kategori as $id => $nama)
                                        <option value="{{ $id }}">{{ $nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tingkat <span class="text-danger">*</span></label>
                                <select name="id_tingkat_penghargaan" class="form-control" required>
                                    <option value="">Pilih Tingkat</option>
                                    @foreach($tingkat as $id => $nama)
                                        <option value="{{ $id }}">{{ $nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ekstrakurikuler</label>
                                <select name="id_ekskul" class="form-control">
                                    <option value="">Pilih Ekskul</option>
                                    @foreach($ekskul as $id => $nama)
                                        <option value="{{ $id }}">{{ $nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Penyelenggara</label>
                                <input type="text" name="penyelenggara" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Prestasi</label>
                                <input type="date" name="tanggal_prestasi" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Rata-rata Nilai</label>
                                <input type="number" name="rata_rata_nilai" class="form-control" step="0.01" min="0" max="100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Dokumen Sertifikat</label>
                                <input type="file" name="dokumen_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Format: PDF, JPG, PNG. Max: 2MB</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Surat Tugas</label>
                                <input type="file" name="surat_tugas_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Format: PDF, JPG, PNG. Max: 2MB</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach($prestasi as $p)
<!-- Modal Edit Prestasi -->
<div class="modal fade" id="editPrestasiModal{{ $p->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Prestasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('siswa.prestasi.update', $p->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Prestasi <span class="text-danger">*</span></label>
                                <input type="text" name="nama_prestasi" class="form-control" value="{{ $p->nama_prestasi }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select name="id_kategori_prestasi" class="form-control" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($kategori as $id => $nama)
                                        <option value="{{ $id }}" {{ $p->id_kategori_prestasi == $id ? 'selected' : '' }}>{{ $nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tingkat <span class="text-danger">*</span></label>
                                <select name="id_tingkat_penghargaan" class="form-control" required>
                                    <option value="">Pilih Tingkat</option>
                                    @foreach($tingkat as $id => $nama)
                                        <option value="{{ $id }}" {{ $p->id_tingkat_penghargaan == $id ? 'selected' : '' }}>{{ $nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ekstrakurikuler</label>
                                <select name="id_ekskul" class="form-control">
                                    <option value="">Pilih Ekskul</option>
                                    @foreach($ekskul as $id => $nama)
                                        <option value="{{ $id }}" {{ $p->id_ekskul == $id ? 'selected' : '' }}>{{ $nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Penyelenggara</label>
                                <input type="text" name="penyelenggara" class="form-control" value="{{ $p->penyelenggara }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Prestasi</label>
                                <input type="date" name="tanggal_prestasi" class="form-control" value="{{ $p->tanggal_prestasi }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Rata-rata Nilai</label>
                                <input type="number" name="rata_rata_nilai" class="form-control" step="0.01" min="0" max="100" value="{{ $p->rata_rata_nilai }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Dokumen Sertifikat</label>
                                <input type="file" name="dokumen_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Format: PDF, JPG, PNG. Max: 2MB</small>
                                @if($p->dokumen_url)
                                    <br><small class="text-info">File saat ini: <a href="{{ asset($p->dokumen_url) }}" target="_blank">Lihat</a></small>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Surat Tugas</label>
                                <input type="file" name="surat_tugas_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Format: PDF, JPG, PNG. Max: 2MB</small>
                                @if($p->surat_tugas_url)
                                    <br><small class="text-info">File saat ini: <a href="{{ asset($p->surat_tugas_url) }}" target="_blank">Lihat</a></small>
                                @endif
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="3">{{ $p->keterangan }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<script>
function cetakLaporan() {
    // Get current filter values
    const kategori = document.querySelector('select[name="kategori"]').value;
    const tingkat = document.querySelector('select[name="tingkat"]').value;
    const ekskul = document.querySelector('select[name="ekskul"]').value;
    const status = document.querySelector('select[name="status"]').value;
    const from = document.querySelector('input[name="from"]').value;
    const to = document.querySelector('input[name="to"]').value;
    
    // Build query string with current filters
    const params = new URLSearchParams();
    if (kategori) params.append('kategori', kategori);
    if (tingkat) params.append('tingkat', tingkat);
    if (ekskul) params.append('ekskul', ekskul);
    if (status) params.append('status', status);
    if (from) params.append('from', from);
    if (to) params.append('to', to);
    
    // Open print page with current filters
    window.open('{{ route("siswa.prestasi.cetak") }}?' + params.toString(), '_blank');
}
</script>

@endsection 