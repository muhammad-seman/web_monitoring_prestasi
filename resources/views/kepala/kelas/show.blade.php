@extends('layouts.app')
@section('title', 'Detail Kelas')
@section('content')
<div class="row">
  <div class="col-lg-8 mx-auto">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title mb-3">Detail Kelas</h4>
        <table class="table table-borderless mb-4">
          <tr>
            <th width="30%">Nama Kelas</th>
            <td>{{ $kelas->nama_kelas }}</td>
          </tr>
          <tr>
            <th>Wali Kelas</th>
            <td>{{ $kelas->guru->name ?? '-' }}</td>
          </tr>
          <tr>
            <th>Tahun Ajaran</th>
            <td>{{ $kelas->tahun_ajaran }}</td>
          </tr>
          <tr>
            <th>Jumlah Siswa</th>
            <td>{{ $kelas->siswa->count() }}</td>
          </tr>
        </table>
        <h5 class="mb-2">Daftar Siswa</h5>
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama</th>
                <th>NISN</th>
                <th>Jenis Kelamin</th>
                <th>Tahun Masuk</th>
              </tr>
            </thead>
            <tbody>
              @foreach($kelas->siswa as $i => $s)
              <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $s->nama }}</td>
                <td>{{ $s->nisn }}</td>
                <td>{{ $s->jenis_kelamin }}</td>
                <td>{{ $s->tahun_masuk }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection 