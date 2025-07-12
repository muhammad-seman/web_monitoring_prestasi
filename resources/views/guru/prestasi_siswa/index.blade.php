@extends('layouts.app')
@section('title', 'Prestasi Siswa Kelas Saya')

@section('content')
<!-- Iconify CDN -->
<script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
<div class="row">
    <div class="col-lg-12">
        <div class="card w-100">
            <div class="card-body">
                <!-- Filter & Action -->
                <div class="d-md-flex align-items-center justify-content-between mb-3">
                    <h4 class="card-title">Prestasi Siswa Kelas Saya</h4>
                    <div class="d-flex gap-2">
                        <form method="GET" class="d-flex align-items-center gap-2">
                            <select name="kategori" class="form-select">
                                <option value="">Semua Kategori</option>
                                @foreach($kategori as $id => $nama_kategori)
                                <option value="{{ $id }}" {{ request('kategori')==$id ? 'selected' : '' }}>
                                    {{ $nama_kategori }}
                                </option>
                                @endforeach
                            </select>
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="draft" {{ request('status')=='draft' ? 'selected' : '' }}>Draft</option>
                                <option value="menunggu_validasi" {{ request('status')=='menunggu_validasi' ? 'selected' : '' }}>Menunggu Validasi</option>
                                <option value="diterima" {{ request('status')=='diterima' ? 'selected' : '' }}>Diterima</option>
                                <option value="ditolak" {{ request('status')=='ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                            <input type="date" name="from" class="form-control" value="{{ request('from') }}" placeholder="Dari">
                            <span class="mx-1">s/d</span>
                            <input type="date" name="to" class="form-control" value="{{ request('to') }}" placeholder="Sampai">
                            <button type="submit" class="btn btn-secondary" title="Terapkan Filter">
                                <span class="iconify" data-icon="mdi:filter-variant" data-width="20" data-height="20"></span>
                            </button>
                        </form>
                        <a href="{{ route('guru.prestasi_siswa.cetak', request()->all()) }}" class="btn btn-success"
                            target="_blank" title="Cetak PDF">
                            <span class="iconify" data-icon="mdi:printer" data-width="20" data-height="20"></span>
                        </a>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPrestasiModal">
                            <span class="iconify" data-icon="mdi:plus" data-width="20" data-height="20"></span>
                        </button>
                    </div>
                </div>

                @if(session('success'))
                <div class="alert alert-success mt-3">
                    {{ session('success') }}
                </div>
                @endif

                @if ($errors->any())
                <div class="alert alert-danger mt-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="table-responsive mt-4">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Siswa</th>
                                <th>Nama Prestasi</th>
                                <th>Kategori</th>
                                <th>Tingkat</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($prestasi as $p)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $p->siswa->nama ?? '-' }}</td>
                                <td>{{ $p->nama_prestasi }}</td>
                                <td>{{ $p->kategori->nama_kategori ?? '-' }}</td>
                                <td>{{ $p->tingkat->tingkat ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $p->status == 'draft' ? 'secondary' :
                                        ($p->status == 'menunggu_validasi' ? 'warning' :
                                        ($p->status == 'diterima' ? 'success' :
                                        ($p->status == 'ditolak' ? 'danger' : 'secondary')))
                                    }}">
                                        {{ ucwords(str_replace('_', ' ', $p->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#detailPrestasiModal{{ $p->id }}" title="Detail">
                                        <span class="iconify" data-icon="mdi:eye" data-width="18" data-height="18"></span>
                                    </button>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editPrestasiModal{{ $p->id }}" title="Edit">
                                        <span class="iconify" data-icon="mdi:pencil" data-width="18" data-height="18"></span>
                                    </button>
                                    @if($p->status == 'menunggu_validasi')
                                    <button class="btn btn-success btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#validasiPrestasiModal{{ $p->id }}" title="Validasi">
                                        <span class="iconify" data-icon="mdi:check-circle" data-width="18" data-height="18"></span>
                                    </button>
                                    @endif
                                    @if(!$p->dokumen_url)
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#uploadDokumenModal{{ $p->id }}" title="Upload Dokumen">
                                        <span class="iconify" data-icon="mdi:upload" data-width="18" data-height="18"></span>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">Belum ada data prestasi siswa di kelas Anda.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $prestasi->links("pagination::bootstrap-4") }}
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
                <h5 class="modal-title">Detail Prestasi Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless">
                    <tr>
                        <th>Nama Siswa</th>
                        <td>{{ $p->siswa->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Nama Prestasi</th>
                        <td>{{ $p->nama_prestasi }}</td>
                    </tr>
                    <tr>
                        <th>Kategori</th>
                        <td>{{ $p->kategori->nama_kategori ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Tingkat</th>
                        <td>{{ $p->tingkat->tingkat ?? '-' }}</td>
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
                        <td>{{ $p->tanggal_prestasi ? \Carbon\Carbon::parse($p->tanggal_prestasi)->format('d-m-Y') : '-'
                            }}</td>
                    </tr>
                    <tr>
                        <th>Keterangan</th>
                        <td>{{ $p->keterangan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Dokumen</th>
                        <td>
                            @if($p->dokumen_url)
                            <a href="{{ asset($p->dokumen_url) }}" target="_blank">
                                <span class="iconify text-danger" data-icon="mdi:file-pdf-box" data-width="20" data-height="20"></span> Lihat Dokumen
                            </a>
                            @else
                            <span class="text-muted">Belum ada dokumen</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge bg-{{ 
                                $p->status == 'draft' ? 'secondary'
                                : ($p->status == 'menunggu_validasi' ? 'warning'
                                : ($p->status == 'diterima' ? 'success'
                                : ($p->status == 'ditolak' ? 'danger' : 'secondary')))
                              }}">
                                {{ ucwords(str_replace('_', ' ', $p->status)) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Nilai Rata-rata</th>
                        <td>{{ $p->rata_rata_nilai ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Validator</th>
                        <td>{{ $p->validator->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Waktu Validasi</th>
                        <td>{{ $p->validated_at ? \Carbon\Carbon::parse($p->validated_at)->format('d-m-Y H:i') : '-'
                            }}</td>
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

@foreach($prestasi as $p)
<!-- Modal Edit -->
<div class="modal fade" id="editPrestasiModal{{ $p->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="POST" enctype="multipart/form-data"
            action="{{ route('guru.prestasi_siswa.update', $p->id) }}">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Prestasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Siswa</label>
                    <select name="id_siswa" class="form-control" required>
                        <option value="">- Pilih Siswa -</option>
                        @foreach($siswa as $id => $nama)
                        <option value="{{ $id }}" {{ $p->id_siswa == $id ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Kategori</label>
                    <select name="id_kategori_prestasi" class="form-control" required>
                        <option value="">- Pilih Kategori -</option>
                        @foreach($kategori as $id => $nama_kategori)
                        <option value="{{ $id }}" {{ $p->id_kategori_prestasi == $id ? 'selected' : '' }}>{{
                            $nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Tingkat</label>
                    <select name="id_tingkat_penghargaan" class="form-control" required>
                        <option value="">- Pilih Tingkat -</option>
                        @foreach($tingkat as $id => $nama_tingkat)
                        <option value="{{ $id }}" {{ $p->id_tingkat_penghargaan == $id ? 'selected' : '' }}>{{
                            $nama_tingkat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Ekstrakurikuler</label>
                    <select name="id_ekskul" class="form-control">
                        <option value="">- Tidak Ada -</option>
                        @foreach($ekskul as $id => $nama_ekskul)
                        <option value="{{ $id }}" {{ $p->id_ekskul == $id ? 'selected' : '' }}>{{ $nama_ekskul }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Nama Prestasi</label>
                    <input type="text" name="nama_prestasi" class="form-control" value="{{ $p->nama_prestasi }}"
                        required>
                </div>
                <div class="mb-3">
                    <label>Penyelenggara</label>
                    <input type="text" name="penyelenggara" class="form-control" value="{{ $p->penyelenggara }}"
                        required>
                </div>
                <div class="mb-3">
                    <label>Tanggal Prestasi</label>
                    <input type="date" name="tanggal_prestasi" class="form-control" value="{{ $p->tanggal_prestasi }}">
                </div>
                <div class="mb-3">
                    <label>Keterangan</label>
                    <input type="text" name="keterangan" class="form-control" value="{{ $p->keterangan }}">
                </div>
                <div class="mb-3">
                    <label>Dokumen Sertifikat (PDF/JPG/PNG, opsional)</label>
                    <input type="file" name="dokumen_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    @if($p->dokumen_url)
                    <div class="mt-2">
                        <a href="{{ asset($p->dokumen_url) }}" target="_blank">
                            <span class="iconify text-danger" data-icon="mdi:file-pdf-box" data-width="20" data-height="20"></span> Lihat Dokumen Lama
                        </a>
                    </div>
                    @endif
                </div>
                <div class="mb-3">
                    <label>Status</label>
                    <select name="status" class="form-control" required>
                        <option value="draft" {{ $p->status == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="menunggu_validasi" {{ $p->status == 'menunggu_validasi' ? 'selected' : ''
                            }}>Menunggu Validasi</option>
                        <option value="diterima" {{ $p->status == 'diterima' ? 'selected' : '' }}>Diterima</option>
                        <option value="ditolak" {{ $p->status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Nilai Rata-rata (jika akademik)</label>
                    <input type="number" name="rata_rata_nilai" step="0.01" class="form-control"
                        value="{{ $p->rata_rata_nilai }}">
                </div>
                <div class="mb-3">
                    <label>Alasan Tolak</label>
                    <input type="text" name="alasan_tolak" class="form-control" value="{{ $p->alasan_tolak }}">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success">Update</button>
            </div>
        </form>
    </div>
</div>
@endforeach

@foreach($prestasi as $p)
<!-- Modal Validasi -->
<div class="modal fade" id="validasiPrestasiModal{{ $p->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('guru.prestasi_siswa.validasi', $p->id) }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Validasi Prestasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Prestasi:</strong> {{ $p->nama_prestasi }}</p>
                <p><strong>Siswa:</strong> {{ $p->siswa->nama ?? '-' }}</p>
                
                <div class="mb-3">
                    <label>Status Validasi</label>
                    <select name="status" class="form-control" required onchange="toggleAlasanTolak(this.value)">
                        <option value="">- Pilih Status -</option>
                        <option value="diterima">Diterima</option>
                        <option value="ditolak">Ditolak</option>
                    </select>
                </div>
                <div class="mb-3" id="alasanTolakDiv" style="display: none;">
                    <label>Alasan Tolak</label>
                    <textarea name="alasan_tolak" class="form-control" rows="3" placeholder="Masukkan alasan penolakan..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Validasi</button>
            </div>
        </form>
    </div>
</div>
@endforeach

@foreach($prestasi as $p)
<!-- Modal Upload Dokumen -->
<div class="modal fade" id="uploadDokumenModal{{ $p->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" enctype="multipart/form-data" action="{{ route('guru.prestasi_siswa.upload', $p->id) }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Upload Dokumen Prestasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Prestasi:</strong> {{ $p->nama_prestasi }}</p>
                <p><strong>Siswa:</strong> {{ $p->siswa->nama ?? '-' }}</p>
                
                <div class="mb-3">
                    <label>Dokumen Bukti (PDF/JPG/PNG, max 2MB)</label>
                    <input type="file" name="dokumen_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                    <small class="text-muted">Upload sertifikat, piagam, atau dokumen bukti prestasi</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Upload</button>
            </div>
        </form>
    </div>
</div>
@endforeach

<!-- Modal Tambah -->
<div class="modal fade" id="createPrestasiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="POST" enctype="multipart/form-data"
            action="{{ route('guru.prestasi_siswa.store') }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Prestasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Siswa</label>
                    <select name="id_siswa" class="form-control" required>
                        <option value="">- Pilih Siswa -</option>
                        @foreach($siswa as $id => $nama)
                        <option value="{{ $id }}">{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Kategori</label>
                    <select name="id_kategori_prestasi" class="form-control" required>
                        <option value="">- Pilih Kategori -</option>
                        @foreach($kategori as $id => $nama_kategori)
                        <option value="{{ $id }}">{{ $nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Tingkat</label>
                    <select name="id_tingkat_penghargaan" class="form-control" required>
                        <option value="">- Pilih Tingkat -</option>
                        @foreach($tingkat as $id => $nama_tingkat)
                        <option value="{{ $id }}">{{ $nama_tingkat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Ekstrakurikuler</label>
                    <select name="id_ekskul" class="form-control">
                        <option value="">- Tidak Ada -</option>
                        @foreach($ekskul as $id => $nama_ekskul)
                        <option value="{{ $id }}">{{ $nama_ekskul }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Nama Prestasi</label>
                    <input type="text" name="nama_prestasi" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Penyelenggara</label>
                    <input type="text" name="penyelenggara" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Tanggal Prestasi</label>
                    <input type="date" name="tanggal_prestasi" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Keterangan</label>
                    <input type="text" name="keterangan" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Dokumen Sertifikat (PDF/JPG/PNG, opsional)</label>
                    <input type="file" name="dokumen_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                </div>
                <div class="mb-3">
                    <label>Status</label>
                    <select name="status" class="form-control" required>
                        <option value="draft">Draft</option>
                        <option value="menunggu_validasi">Menunggu Validasi</option>
                        <option value="diterima">Diterima</option>
                        <option value="ditolak">Ditolak</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Nilai Rata-rata (jika akademik)</label>
                    <input type="number" name="rata_rata_nilai" step="0.01" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Alasan Tolak</label>
                    <input type="text" name="alasan_tolak" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleAlasanTolak(status) {
    const alasanDiv = document.getElementById('alasanTolakDiv');
    if (status === 'ditolak') {
        alasanDiv.style.display = 'block';
    } else {
        alasanDiv.style.display = 'none';
    }
}
</script>
@endsection 