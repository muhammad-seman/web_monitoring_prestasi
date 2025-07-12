@extends('layouts.app')
@section('title', 'Data Prestasi Siswa')
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Data Prestasi Siswa</h4>
        <!-- Filter dan Tombol Cetak -->
        <form class="row g-3 mb-4" method="GET" action="">
          <div class="col-md-2">
            <select name="kelas_id" class="form-control">
              <option value="">-- Semua Kelas --</option>
              @foreach($kelas as $k)
                <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <select name="kategori_id" class="form-control">
              <option value="">-- Semua Kategori --</option>
              @foreach($kategori as $kat)
                <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama_kategori }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <select name="tingkat_id" class="form-control">
              <option value="">-- Semua Tingkat --</option>
              @foreach($tingkat as $t)
                <option value="{{ $t->id }}" {{ request('tingkat_id') == $t->id ? 'selected' : '' }}>{{ $t->tingkat }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <select name="status" class="form-control">
              <option value="">-- Semua Status --</option>
              <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
              <option value="menunggu_validasi" {{ request('status') == 'menunggu_validasi' ? 'selected' : '' }}>Menunggu Validasi</option>
              <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
              <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
          </div>
          <div class="col-md-2">
            <input type="date" name="from" class="form-control" value="{{ request('from') }}" placeholder="Dari Tanggal">
          </div>
          <div class="col-md-2">
            <input type="date" name="to" class="form-control" value="{{ request('to') }}" placeholder="Sampai Tanggal">
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
          </div>
          <div class="col-md-2">
            <a href="{{ route('kepala_sekolah.prestasi_siswa.cetak', request()->all()) }}" target="_blank" class="btn btn-success w-100"><i class="ti ti-printer"></i> Cetak</a>
          </div>
        </form>
        <div class="table-responsive mt-4">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Kategori</th>
                <th>Tingkat</th>
                <th>Nama Prestasi</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($prestasi as $p)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $p->siswa->nama ?? '-' }}</td>
                <td>{{ $p->siswa->kelas->nama_kelas ?? '-' }}</td>
                <td>{{ $p->kategoriPrestasi->nama_kategori ?? '-' }}</td>
                <td>{{ $p->tingkatPenghargaan->tingkat ?? '-' }}</td>
                <td>{{ $p->nama_prestasi }}</td>
                <td>{{ ucfirst($p->status) }}</td>
                <td>
                  <!-- Aksi detail/modal dst -->
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
          {{ $prestasi->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection 