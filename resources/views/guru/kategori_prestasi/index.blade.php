@extends('layouts.app')
@section('title', 'Kategori Prestasi')
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card w-100">
      <div class="card-body">
        <div class="d-md-flex align-items-center justify-content-between">
          <h4 class="card-title">Daftar Kategori Prestasi</h4>
          <div class="text-muted">Halaman ini menampilkan semua kategori prestasi yang tersedia</div>
        </div>

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
                  <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                    data-bs-target="#detailKategoriModal{{ $kat->id }}">Detail</button>
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
<!-- Modal Detail -->
<div class="modal fade" id="detailKategoriModal{{ $kat->id }}" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Kategori Prestasi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <table class="table table-borderless">
              <tr>
                <td width="30%"><strong>Nama Kategori</strong></td>
                <td width="5%">:</td>
                <td>{{ $kat->nama_kategori }}</td>
              </tr>
              <tr>
                <td><strong>Deskripsi</strong></td>
                <td>:</td>
                <td>{{ $kat->deskripsi ?? 'Tidak ada deskripsi' }}</td>
              </tr>
              <tr>
                <td><strong>Dibuat Pada</strong></td>
                <td>:</td>
                <td>{{ $kat->created_at ? $kat->created_at->format('d/m/Y H:i') : '-' }}</td>
              </tr>
              <tr>
                <td><strong>Terakhir Diupdate</strong></td>
                <td>:</td>
                <td>{{ $kat->updated_at ? $kat->updated_at->format('d/m/Y H:i') : '-' }}</td>
              </tr>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
@endforeach

@if($kategori->isEmpty())
<div class="row mt-4">
  <div class="col-12">
    <div class="alert alert-info text-center">
      <i class="ti ti-info-circle me-2"></i>
      Belum ada kategori prestasi yang tersedia.
    </div>
  </div>
</div>
@endif
@endsection 