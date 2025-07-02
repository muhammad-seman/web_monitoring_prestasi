@extends('layouts.app')
@section('title', 'Tingkat Penghargaan')
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card w-100">
      <div class="card-body">
        <div class="d-md-flex align-items-center justify-content-between">
          <h4 class="card-title">Tingkat Penghargaan</h4>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTingkatModal">Tambah
            Tingkat</button>
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
                <th>Tingkat</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($tingkat as $tg)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $tg->tingkat }}</td>
                <td>
                  <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                    data-bs-target="#editTingkatModal{{ $tg->id }}">Edit</button>
                  <button class="btn btn-danger btn-sm" onclick="confirmDelete({{ $tg->id }})">Hapus</button>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
          {{ $tingkat->links("pagination::bootstrap-4") }}
        </div>
      </div>
    </div>
  </div>
</div>

@foreach($tingkat as $tg)
<!-- Modal Edit -->
<div class="modal fade" id="editTingkatModal{{ $tg->id }}" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="{{ route('admin.tingkat_penghargaan.update', $tg->id) }}">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Edit Tingkat</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Tingkat</label>
          <input type="text" name="tingkat" class="form-control" value="{{ $tg->tingkat }}" required>
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
<div class="modal fade" id="createTingkatModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="{{ route('admin.tingkat_penghargaan.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Tambah Tingkat</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Tingkat</label>
          <input type="text" name="tingkat" class="form-control" required>
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
  function confirmDelete(tingkatId) {
    Swal.fire({
      title: 'Yakin ingin menghapus tingkat ini?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, Hapus'
    }).then((result) => {
      if (result.isConfirmed) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/tingkat_penghargaan/${tingkatId}`;
        form.innerHTML = '@csrf @method("DELETE")';
        document.body.appendChild(form);
        form.submit();
      }
    });
  }
</script>
@endsection