@extends('layouts.app')
@section('title', 'Manajemen Kelas')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card w-100">
            <div class="card-body">
                <div class="d-md-flex align-items-center justify-content-between">
                    <h4 class="card-title">Manajemen Kelas</h4>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createKelasModal">Tambah
                        Kelas</button>
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
                                <th>Nama Kelas</th>
                                <th>Wali Kelas</th>
                                <th>Tahun Ajaran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kelas as $k)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $k->nama_kelas }}</td>
                                <td>{{ $k->wali->nama ?? '-' }}</td>
                                <td>{{ $k->tahun_ajaran }}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editKelasModal{{ $k->id }}">Edit</button>
                                    <button class="btn btn-danger btn-sm"
                                        onclick="confirmDelete({{ $k->id }})">Hapus</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $kelas->links("pagination::bootstrap-4") }}
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($kelas as $k)
<!-- Modal Edit -->
<div class="modal fade" id="editKelasModal{{ $k->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="POST" action="{{ route('admin.kelas.update', $k->id) }}">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Nama Kelas</label>
                    <input type="text" name="nama_kelas" class="form-control" value="{{ $k->nama_kelas }}" required>
                </div>
                <div class="mb-3">
                    <label>Wali Kelas</label>
                    <select name="id_wali_kelas" class="form-control">
                        <option value="">- Pilih Wali Kelas -</option>
                        @foreach($wali as $id => $nama)
                        <option value="{{ $id }}" {{ $k->id_wali_kelas == $id ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" class="form-control" value="{{ $k->tahun_ajaran }}" required>
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
<div class="modal fade" id="createKelasModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="POST" action="{{ route('admin.kelas.store') }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Nama Kelas</label>
                    <input type="text" name="nama_kelas" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Wali Kelas</label>
                    <select name="id_wali_kelas" class="form-control">
                        <option value="">- Pilih Wali Kelas -</option>
                        @foreach($wali as $id => $nama)
                        <option value="{{ $id }}">{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" class="form-control" required>
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
    function confirmDelete(kelasId) {
    Swal.fire({
      title: 'Yakin ingin menghapus kelas ini?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, Hapus'
    }).then((result) => {
      if (result.isConfirmed) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/kelas/${kelasId}`;
        form.innerHTML = '@csrf @method("DELETE")';
        document.body.appendChild(form);
        form.submit();
      }
    });
  }
</script>
@endsection