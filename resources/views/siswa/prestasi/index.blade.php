@extends('layouts.app')
@section('title', 'Prestasi Saya')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3">Daftar Prestasi Saya</h4>
                <!-- Filter -->
                <form method="GET" class="row mb-3 g-2">
                    <div class="col-md-2">
                        <select name="kategori" class="form-control">
                            <option value="">Semua Kategori</option>
                            @foreach($kategori as $id => $nama)
                                <option value="{{ $id }}" {{ request('kategori') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="tingkat" class="form-control">
                            <option value="">Semua Tingkat</option>
                            @foreach($tingkat as $id => $nama)
                                <option value="{{ $id }}" {{ request('tingkat') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="ekskul" class="form-control">
                            <option value="">Semua Ekskul</option>
                            @foreach($ekskul as $id => $nama)
                                <option value="{{ $id }}" {{ request('ekskul') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="menunggu_validasi" {{ request('status') == 'menunggu_validasi' ? 'selected' : '' }}>Menunggu Validasi</option>
                            <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-secondary" type="submit">Filter</button>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Prestasi</th>
                                <th>Kategori</th>
                                <th>Tingkat</th>
                                <th>Ekskul</th>
                                <th>Penyelenggara</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($prestasi as $p)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $p->nama_prestasi }}</td>
                                <td>{{ $p->kategoriPrestasi->nama_kategori ?? '-' }}</td>
                                <td>{{ $p->tingkatPenghargaan->tingkat ?? '-' }}</td>
                                <td>{{ $p->ekskul->nama ?? '-' }}</td>
                                <td>{{ $p->penyelenggara }}</td>
                                <td>{{ $p->tanggal_prestasi }}</td>
                                <td>
                                    <span class="badge bg-{{ $p->status == 'diterima' ? 'success' : ($p->status == 'ditolak' ? 'danger' : 'warning') }}">
                                        {{ ucfirst(str_replace('_', ' ', $p->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailPrestasiModal{{ $p->id }}">Detail</button>
                                    <a href="{{ route('siswa.prestasi.cetakSurat', $p->id) }}" class="btn btn-success btn-sm" target="_blank">Cetak Surat</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center">Belum ada data prestasi.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($prestasi as $p)
<!-- Modal Detail -->
<div class="modal fade" id="detailPrestasiModal{{ $p->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Prestasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless">
                    <tr>
                        <th>Nama Prestasi</th>
                        <td>{{ $p->nama_prestasi }}</td>
                    </tr>
                    <tr>
                        <th>Kategori</th>
                        <td>{{ $p->kategoriPrestasi->nama_kategori ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Tingkat</th>
                        <td>{{ $p->tingkatPenghargaan->tingkat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Ekskul</th>
                        <td>{{ $p->ekskul->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Penyelenggara</th>
                        <td>{{ $p->penyelenggara }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>{{ $p->tanggal_prestasi }}</td>
                    </tr>
                    <tr>
                        <th>Keterangan</th>
                        <td>{{ $p->keterangan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Dokumen</th>
                        <td>
                            @if($p->dokumen_url)
                            <a href="{{ asset($p->dokumen_url) }}" target="_blank">Lihat Dokumen</a>
                            @else
                            -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge bg-{{ $p->status == 'diterima' ? 'success' : ($p->status == 'ditolak' ? 'danger' : 'warning') }}">
                                {{ ucfirst(str_replace('_', ' ', $p->status)) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Nilai Rata-rata</th>
                        <td>{{ $p->rata_rata_nilai ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Alasan Tolak</th>
                        <td>{{ $p->alasan_tolak ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection 