@extends('layouts.app')
@section('title', 'Ekstrakurikuler')

@section('content')
<script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
<div class="row">
    <div class="col-lg-12">
        <div class="card w-100">
            <div class="card-body">
                <div class="d-md-flex align-items-center justify-content-between">
                    <h4 class="card-title">Ekstrakurikuler</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('guru.dashboard') }}" class="btn btn-secondary">
                            <span class="iconify" data-icon="mdi:arrow-left" data-width="20"></span> Kembali
                        </a>
                    </div>
                </div>

                <div class="alert alert-info mt-3">
                    <span class="iconify" data-icon="mdi:information" data-width="20"></span>
                    <strong>Info:</strong> Anda dapat melihat informasi ekstrakurikuler dan siswa peserta dari kelas yang Anda ampu.
                </div>

                <div class="table-responsive mt-4">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Ekstrakurikuler</th>
                                <th>Pembina</th>
                                <th>Keterangan</th>
                                <th>Siswa dari Kelas Saya</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ekstrakurikuler as $ek)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $ek->nama }}</td>
                                <td>{{ $ek->pembina ?? '-' }}</td>
                                <td>{{ $ek->keterangan ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $ek->jumlah_siswa_kelas }} siswa</span>
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#detailEkskulModal{{ $ek->id }}" title="Detail">
                                        <span class="iconify" data-icon="mdi:eye" data-width="18" data-height="18"></span>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data ekstrakurikuler.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($ekstrakurikuler as $ek)
<!-- Modal Detail Ekstrakurikuler -->
<div class="modal fade" id="detailEkskulModal{{ $ek->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Ekstrakurikuler: {{ $ek->nama }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Informasi Ekstrakurikuler -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 120px;">Nama Ekskul</th>
                                <td>{{ $ek->nama }}</td>
                            </tr>
                            <tr>
                                <th>Pembina</th>
                                <td>{{ $ek->pembina ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Keterangan</th>
                                <td>{{ $ek->keterangan ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6>Total Siswa dari Kelas Anda</h6>
                                <h3 class="text-primary">{{ $ek->jumlah_siswa_kelas }}</h3>
                                <small class="text-muted">siswa</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Daftar Siswa Peserta -->
                <div class="mt-4">
                    <h6>Siswa Peserta dari Kelas Anda</h6>
                    @php
                        $user = Auth::user();
                        $kelasGuru = \App\Models\Kelas::where('id_wali_kelas', $user->id)->pluck('id');
                        $siswaPeserta = \App\Models\SiswaEkskul::with(['siswa.kelas'])
                            ->where('id_ekskul', $ek->id)
                            ->whereHas('siswa', function($q) use ($kelasGuru) {
                                $q->whereIn('id_kelas', $kelasGuru);
                            })
                            ->orderBy('created_at', 'desc')
                            ->get();
                    @endphp

                    @if($siswaPeserta->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Tanggal Bergabung</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($siswaPeserta as $i => $peserta)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $peserta->siswa->nama ?? '-' }}</td>
                                    <td>{{ $peserta->siswa->kelas->nama_kelas ?? '-' }}</td>
                                    <td>{{ $peserta->created_at ? \Carbon\Carbon::parse($peserta->created_at)->format('d-m-Y') : '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <span class="iconify" data-icon="mdi:alert-circle" data-width="20"></span>
                        Belum ada siswa dari kelas Anda yang bergabung dengan ekstrakurikuler ini.
                    </div>
                    @endif
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