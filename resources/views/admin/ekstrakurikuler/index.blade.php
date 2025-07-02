@extends('layouts.app')
@section('title', 'Ekstrakurikuler')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card w-100">
            <div class="card-body">
                <div class="d-md-flex align-items-center justify-content-between">
                    <h4 class="card-title">Ekstrakurikuler</h4>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createEkskulModal">Tambah
                        Ekskul</button>
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
                                <th>Nama Ekskul</th>
                                <th>Pembina</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ekskul as $ek)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $ek->nama }}</td>
                                <td>{{ $ek->pembina ?? '-' }}</td>
                                <td>{{ $ek->keterangan ?? '-' }}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editEkskulModal{{ $ek->id }}">Edit</button>
                                    <button class="btn btn-danger btn-sm"
                                        onclick="confirmDelete({{ $ek->id }})">Hapus</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $ekskul->links("pagination::bootstrap-4") }}
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($ekskul as $ek)
<!-- Modal Edit -->
<div class="modal fade" id="editEkskulModal{{ $ek->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="POST" action="{{ route('admin.ekstrakurikuler.update', $ek->id) }}">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Ekskul</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Nama Ekskul</label>
                    <input type="text" name="nama" class="form-control" value="{{ $ek->nama }}" required>
                </div>
                <div class="mb-3">
                    <label>Pembina</label>
                    <input type="text" name="pembina" class="form-control" value="{{ $ek->pembina }}">
                </div>
                <div class="mb-3">
                    <label>Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2">{{ $ek->keterangan }}</textarea>
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
<div class="modal fade" id="createEkskulModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="POST" action="{{ route('admin.ekstrakurikuler.store') }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Ekskul</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Nama Ekskul</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Pembina</label>
                    <input type="text" name="pembina" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2"></textarea>
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
    function confirmDelete(ekskulId) {
    Swal.fire({
      title: 'Yakin ingin menghapus ekskul ini?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, Hapus'
    }).then((result) => {
      if (result.isConfirmed) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/ekstrakurikuler/${ekskulId}`;
        form.innerHTML = '@csrf @method("DELETE")';
        document.body.appendChild(form);
        form.submit();
      }
    });
  }
</script>
@endsection