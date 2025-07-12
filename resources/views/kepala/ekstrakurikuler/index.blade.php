@extends('layouts.app')
@section('title', 'Ekstrakurikuler')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card w-100">
            <div class="card-body">
                <div class="d-md-flex align-items-center justify-content-between">
                    <h4 class="card-title">Ekstrakurikuler</h4>
                </div>
                <div class="table-responsive mt-4">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Ekskul</th>
                                <th>Pembina</th>
                                <th>Keterangan</th>
                                <th>Jumlah Siswa</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ekstrakurikuler as $ek)
                            <tr>
                                <td>{{ ($ekstrakurikuler->currentPage() - 1) * $ekstrakurikuler->perPage() + $loop->iteration }}</td>
                                <td>{{ $ek->nama }}</td>
                                <td>{{ $ek->pembina ?? '-' }}</td>
                                <td>{{ $ek->keterangan ?? '-' }}</td>
                                <td>{{ $ek->siswa_count }}</td>
                                <td>
                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal{{ $ek->id }}">Detail</button>
                                    <a href="{{ route('kepala_sekolah.ekstrakurikuler.prestasi', $ek->id) }}" class="btn btn-success btn-sm">Prestasi</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $ekstrakurikuler->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($ekstrakurikuler as $ek)
<!-- Modal Detail -->
<div class="modal fade" id="detailModal{{ $ek->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Ekstrakurikuler - {{ $ek->nama }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Nama Ekskul:</strong><br>
                        {{ $ek->nama }}
                    </div>
                    <div class="col-md-6">
                        <strong>Pembina:</strong><br>
                        {{ $ek->pembina ?? '-' }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <strong>Keterangan:</strong><br>
                        {{ $ek->keterangan ?? '-' }}
                    </div>
                </div>
                <hr>
                <h6>Daftar Siswa ({{ $ek->siswa_count }} orang)</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ek->siswa as $siswa)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $siswa->nama }}</td>
                                <td>{{ $siswa->kelas->nama ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">Belum ada siswa terdaftar</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection 