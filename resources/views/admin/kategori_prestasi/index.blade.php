@extends('layouts.app')
@section('title', 'Kategori Prestasi')
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card w-100">
      <div class="card-body">
        <div class="d-md-flex align-items-center justify-content-between">
          <h4 class="card-title">Kategori Prestasi</h4>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createKategoriModal">Tambah Kategori</button>
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
                <th>Nama Kategori</th>
                <th>Deskripsi</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($kategori as $kat)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $kat->nama_kategori }}</td>
                <td>{{ $kat->deskripsi ?? '-' }}</td>
                <td>
                  <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                    data-bs-target="#editKategoriModal{{ $kat->id }}">Edit</button>
                  <button class="btn btn-danger btn-sm" onclick="confirmDelete({{ $kat->id }})">Hapus</button>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
          {{ $kategori->links("pagination::bootstrap-4") }}
        </div>
      </div>
    </div>
  </div>
</div>

@foreach($kategori as $kat)
<!-- Modal Edit -->
<div class="modal fade" id="editKategoriModal{{ $kat->id }}" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="{{ route('admin.kategori_prestasi.update', $kat->id) }}">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Edit Kategori</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Nama Kategori</label>
          <input type="text" name="nama_kategori" class="form-control" value="{{ $kat->nama_kategori }}" required>
        </div>
        <div class="mb-3">
          <label>Deskripsi</label>
          <textarea name="deskripsi" class="form-control" rows="2">{{ $kat->deskripsi }}</textarea>
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
<div class="modal fade" id="createKategoriModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="{{ route('admin.kategori_prestasi.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Tambah Kategori</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Nama Kategori</label>
          <input type="text" name="nama_kategori" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Deskripsi</label>
          <textarea name="deskripsi" class="form-control" rows="2"></textarea>
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
  function confirmDelete(kategoriId) {
    Swal.fire({
      title: 'Yakin ingin menghapus kategori ini?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, Hapus'
    }).then((result) => {
      if (result.isConfirmed) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/kategori_prestasi/${kategoriId}`;
        form.innerHTML = '@csrf @method("DELETE")';
        document.body.appendChild(form);
        form.submit();
      }
    });
  }
</script>
@endsection