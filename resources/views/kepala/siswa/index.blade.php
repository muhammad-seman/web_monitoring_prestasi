@extends('layouts.app')
@section('title', 'Daftar Siswa')
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Daftar Siswa</h4>
        <form class="row g-3 mb-4" method="GET" action="">
          <div class="col-md-3">
            <select name="kelas_id" class="form-control">
              <option value="">-- Semua Kelas --</option>
              @foreach($kelas as $k)
                <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <button type="submit" class="btn btn-primary">Filter</button>
          </div>
          <div class="col-md-3">
            <a href="{{ route('kepala_sekolah.siswa.cetak', request()->all()) }}" target="_blank" class="btn btn-success"><i class="ti ti-printer"></i> Cetak</a>
          </div>
        </form>
        <div class="table-responsive mt-4">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama</th>
                <th>NISN</th>
                <th>Kelas</th>
                <th>Jenis Kelamin</th>
                <th>Tahun Masuk</th>
              </tr>
            </thead>
            <tbody>
              @foreach($siswa as $i => $s)
              <tr>
                <td>{{ $siswa->firstItem() + $i }}</td>
                <td>{{ $s->nama }}</td>
                <td>{{ $s->nisn }}</td>
                <td>{{ $s->kelas->nama_kelas ?? '-' }}</td>
                <td>{{ $s->jenis_kelamin }}</td>
                <td>{{ $s->tahun_masuk }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
          {{ $siswa->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection 