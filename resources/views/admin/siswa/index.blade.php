@extends('layouts.app')
@section('title', 'Manajemen Siswa')
@section('content')
<script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
<div class="row">
    <div class="col-lg-12">
        <div class="card w-100">
            <div class="card-body">

                {{-- HEADER + FILTER --}}
                <div class="d-md-flex align-items-center justify-content-between mb-3">
                    <h4 class="card-title mb-0">Manajemen Siswa</h4>
                    <div class="d-flex gap-2">
                        <form method="GET" class="d-flex align-items-center gap-2">
                            <select name="kelas" class="form-select" style="min-width:140px">
                                <option value="">Semua Kelas</option>
                                @foreach($kelas as $id => $nama_kelas)
                                <option value="{{ $id }}" {{ request('kelas')==$id ? 'selected' : '' }}>
                                    {{ $nama_kelas }}
                                </option>
                                @endforeach
                            </select>
                            <select name="jenis_kelamin" class="form-select" style="min-width:140px">
                                <option value="">Semua Gender</option>
                                <option value="L" {{ request('jenis_kelamin')=='L' ? 'selected' : '' }}>Laki-laki
                                </option>
                                <option value="P" {{ request('jenis_kelamin')=='P' ? 'selected' : '' }}>Perempuan
                                </option>
                            </select>
                            <input type="text" name="tahun_masuk" class="form-control" style="min-width:110px"
                                placeholder="Tahun Masuk" value="{{ request('tahun_masuk') }}">
                            <button type="submit" class="btn btn-secondary" title="Terapkan Filter">
                                <span class="iconify" data-icon="mdi:filter-variant" data-width="20"></span>
                            </button>
                        </form>
                        <a href="{{ route('admin.siswa.index') }}" class="btn btn-light" title="Reset Filter">
                            <span class="iconify" data-icon="mdi:refresh" data-width="20"></span>
                        </a>
                        <a href="{{ route('admin.siswa.cetak', request()->all()) }}" class="btn btn-success"
                            target="_blank" title="Cetak PDF">
                            <span class="iconify" data-icon="mdi:printer" data-width="20"></span>
                        </a>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSiswaModal"
                            title="Tambah Siswa">
                            <span class="iconify" data-icon="mdi:plus" data-width="20"></span>
                        </button>
                    </div>
                </div>

                {{-- ERROR --}}
                @if ($errors->any())
                <div class="alert alert-danger mt-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- TABLE --}}
                <div class="table-responsive mt-4">
                    <table class="table table-bordered align-middle">
                        <table class="table table-bordered">
                            <tr>
                                <th>#</th>
                                <th>NISN</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>Gender</th>
                                <th>Tanggal Lahir</th>
                                <th>Alamat</th>
                                <th>Tahun Masuk</th>
                                <th>Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                                @forelse($siswa as $s)
                                <tr>
                                    <td>{{ $loop->iteration + ($siswa->perPage() * ($siswa->currentPage()-1)) }}</td>
                                    <td>{{ $s->nisn }}</td>
                                    <td>{{ $s->nama }}</td>
                                    <td>{{ $s->kelas->nama_kelas ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $s->jenis_kelamin == 'L' ? 'primary' : 'warning' }}">
                                            <span class="iconify"
                                                data-icon="mdi:{{ $s->jenis_kelamin == 'L' ? 'gender-male' : 'gender-female' }}"></span>
                                            {{ $s->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                        </span>
                                    </td>
                                    <td>{{ $s->tanggal_lahir ? \Carbon\Carbon::parse($s->tanggal_lahir)->format('d-m-Y')
                                        :
                                        '-' }}</td>
                                    <td>{{ $s->alamat ?? '-' }}</td>
                                    <td>{{ $s->tahun_masuk ?? '-' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#editSiswaModal{{ $s->id }}" title="Edit">
                                            <span class="iconify" data-icon="mdi:pencil" data-width="18"></span>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="confirmDelete({{ $s->id }})"
                                            title="Hapus">
                                            <span class="iconify" data-icon="mdi:trash-can" data-width="18"></span>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada data siswa.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{ $siswa->appends(request()->all())->links("pagination::bootstrap-4") }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EDIT --}}
@foreach($siswa as $s)
<div class="modal fade" id="editSiswaModal{{ $s->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="POST" action="{{ route('admin.siswa.update', $s->id) }}">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>NISN</label>
                    <input type="text" name="nisn" class="form-control" value="{{ $s->nisn }}" required>
                </div>
                <div class="mb-3">
                    <label>Nama</label>
                    <input type="text" name="nama" class="form-control" value="{{ $s->nama }}" required>
                </div>
                <div class="mb-3">
                    <label>Kelas</label>
                    <select name="id_kelas" class="form-control" required>
                        <option value="">- Pilih Kelas -</option>
                        @foreach($kelas as $id => $nama_kelas)
                        <option value="{{ $id }}" {{ $s->id_kelas == $id ? 'selected' : '' }}>{{ $nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-control" required>
                        <option value="L" {{ $s->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ $s->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control" value="{{ $s->tanggal_lahir }}">
                </div>
                <div class="mb-3">
                    <label>Alamat</label>
                    <input type="text" name="alamat" class="form-control" value="{{ $s->alamat }}">
                </div>
                <div class="mb-3">
                    <label>Tahun Masuk</label>
                    <input type="text" name="tahun_masuk" class="form-control" value="{{ $s->tahun_masuk }}">
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

{{-- MODAL TAMBAH --}}
<div class="modal fade" id="createSiswaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="POST" action="{{ route('admin.siswa.store') }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>NISN</label>
                    <input type="text" name="nisn" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Nama</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Kelas</label>
                    <select name="id_kelas" class="form-control" required>
                        <option value="">- Pilih Kelas -</option>
                        @foreach($kelas as $id => $nama_kelas)
                        <option value="{{ $id }}">{{ $nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-control" required>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Alamat</label>
                    <input type="text" name="alamat" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Tahun Masuk</label>
                    <input type="text" name="tahun_masuk" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- SWEETALERT2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(siswaId) {
        Swal.fire({
            title: 'Yakin ingin menghapus siswa ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/siswa/${siswaId}`;
                form.innerHTML = '@csrf @method("DELETE")';
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endsection