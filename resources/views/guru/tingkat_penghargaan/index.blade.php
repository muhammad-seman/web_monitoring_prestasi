@extends('layouts.app')
@section('title', 'Tingkat Penghargaan')
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card w-100">
      <div class="card-body">
        <div class="d-md-flex align-items-center justify-content-between">
          <h4 class="card-title">Daftar Tingkat Penghargaan</h4>
          <div class="text-muted">Halaman ini menampilkan semua tingkat penghargaan yang tersedia</div>
        </div>

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
                  <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                    data-bs-target="#detailTingkatModal{{ $tg->id }}">Detail</button>
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
<!-- Modal Detail -->
<div class="modal fade" id="detailTingkatModal{{ $tg->id }}" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Tingkat Penghargaan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <table class="table table-borderless">
              <tr>
                <td width="30%"><strong>Tingkat</strong></td>
                <td width="5%">:</td>
                <td>{{ $tg->tingkat }}</td>
              </tr>
              <tr>
                <td><strong>Dibuat Pada</strong></td>
                <td>:</td>
                <td>{{ $tg->created_at ? $tg->created_at->format('d/m/Y H:i') : '-' }}</td>
              </tr>
              <tr>
                <td><strong>Terakhir Diupdate</strong></td>
                <td>:</td>
                <td>{{ $tg->updated_at ? $tg->updated_at->format('d/m/Y H:i') : '-' }}</td>
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

@if($tingkat->isEmpty())
<div class="row mt-4">
  <div class="col-12">
    <div class="alert alert-info text-center">
      <i class="ti ti-info-circle me-2"></i>
      Belum ada tingkat penghargaan yang tersedia.
    </div>
  </div>
</div>
@endif
@endsection 