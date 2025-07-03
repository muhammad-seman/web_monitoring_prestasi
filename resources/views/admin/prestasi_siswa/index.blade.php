@extends('layouts.app')
@section('title', 'Prestasi Siswa')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card w-100">
            <div class="card-body">
                <div class="d-md-flex align-items-center justify-content-between">
                    <h4 class="card-title">Prestasi Siswa</h4>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPrestasiModal">Tambah
                        Prestasi</button>
                </div>

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
                            @foreach($prestasi as $p)
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
                                        data-bs-target="#detailPrestasiModal{{ $p->id }}">Detail</button>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editPrestasiModal{{ $p->id }}">Edit</button>
                                    <button class="btn btn-danger btn-sm"
                                        onclick="confirmDelete({{ $p->id }})">Hapus</button>
                                </td>
                            </tr>
                            @endforeach
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
                            <a href="{{ asset($p->dokumen_url) }}" target="_blank">Lihat Dokumen</a>
                            @else
                            -
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
                        <td>{{ $p->validated_at ? \Carbon\Carbon::parse($p->validated_at)->format('d-m-Y H:i') : '-' }}
                        </td>
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
            action="{{ route('admin.prestasi_siswa.update', $p->id) }}">
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
                    @if(isset($p) && $p->dokumen_url)
                    <div class="mt-2">
                        <a href="{{ asset($p->dokumen_url) }}" target="_blank">Lihat Dokumen Lama</a>
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

<!-- Modal Tambah -->
<div class="modal fade" id="createPrestasiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="POST" enctype="multipart/form-data" action="{{ route('admin.prestasi_siswa.store') }}">
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
                    @if(isset($p) && $p->dokumen_url)
                    <div class="mt-2">
                        <a href="{{ asset($p->dokumen_url) }}" target="_blank">Lihat Dokumen Lama</a>
                    </div>
                    @endif
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

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(prestasiId) {
    Swal.fire({
      title: 'Yakin ingin menghapus data ini?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, Hapus'
    }).then((result) => {
      if (result.isConfirmed) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/prestasi-siswa/${prestasiId}`;
        form.innerHTML = '@csrf @method("DELETE")';
        document.body.appendChild(form);
        form.submit();
      }
    });
  }
</script>
@endsection