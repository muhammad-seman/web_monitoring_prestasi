@extends('layouts.app')
@section('title', 'Daftar Kelas')
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Daftar Kelas</h4>
        <form class="row g-3 mb-4" method="GET" action="">
          <div class="col-md-3">
            <select name="tahun_ajaran" class="form-control">
              <option value="">-- Semua Tahun Ajaran --</option>
              @foreach($tahunAjaran as $th)
                <option value="{{ $th }}" {{ request('tahun_ajaran') == $th ? 'selected' : '' }}>{{ $th }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <button type="submit" class="btn btn-primary">Filter</button>
          </div>
        </form>
        <div class="table-responsive mt-4">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama Kelas</th>
                <th>Wali Kelas</th>
                <th>Tahun Ajaran</th>
                <th>Jumlah Siswa</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($kelas as $i => $k)
              <tr>
                <td>{{ $kelas->firstItem() + $i }}</td>
                <td>{{ $k->nama_kelas }}</td>
                <td>{{ $k->guru->name ?? '-' }}</td>
                <td>{{ $k->tahun_ajaran }}</td>
                <td>{{ $k->siswa_count }}</td>
                <td>
                  <a href="{{ route('kepala_sekolah.kelas.show', $k->id) }}" class="btn btn-info btn-sm">Detail</a>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
          {{ $kelas->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection 