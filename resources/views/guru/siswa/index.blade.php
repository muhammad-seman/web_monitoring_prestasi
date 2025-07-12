@extends('layouts.app')
@section('title', 'Daftar Siswa Kelas Saya')
@section('content')
<script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
<div class="row">
    <div class="col-lg-12">
        <div class="card w-100">
            <div class="card-body">

                {{-- HEADER --}}
                <div class="d-md-flex align-items-center justify-content-between mb-3">
                    <h4 class="card-title mb-0">Daftar Siswa Kelas Saya</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('guru.siswa.cetak') }}" class="btn btn-success" target="_blank" title="Cetak PDF">
                            <span class="iconify" data-icon="mdi:printer" data-width="20"></span> Cetak Data Siswa
                        </a>
                    </div>
                </div>

                {{-- SUCCESS MESSAGE --}}
                @if(session('success'))
                <div class="alert alert-success mt-3">
                    {{ session('success') }}
                </div>
                @endif

                {{-- KELAS INFO --}}
                @if(isset($kelas) && $kelas->count() > 0)
                <div class="mb-3">
                    <strong>Kelas yang diampu:</strong>
                    @foreach($kelas as $kls)
                        <span class="badge bg-primary me-2">{{ $kls->nama_kelas }}</span>
                    @endforeach
                </div>
                @endif

                {{-- TABLE --}}
                <div class="table-responsive mt-4">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>NISN</th>
                                <th>Nama</th>
                                <th>Gender</th>
                                <th>Tempat, Tanggal Lahir</th>
                                <th>Alamat</th>
                                <th>Tahun Masuk</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($siswa as $i => $s)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $s->nisn }}</td>
                                <td>{{ $s->nama }}</td>
                                <td>
                                    <span class="badge bg-{{ $s->jenis_kelamin == 'L' ? 'primary' : 'warning' }}">
                                        <span class="iconify"
                                            data-icon="mdi:{{ $s->jenis_kelamin == 'L' ? 'gender-male' : 'gender-female' }}"></span>
                                        {{ $s->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    </span>
                                </td>
                                <td>
                                    {{ $s->tempat_lahir ?? '-' }},
                                    {{ $s->tanggal_lahir ? \Carbon\Carbon::parse($s->tanggal_lahir)->format('d-m-Y') : '-' }}
                                </td>
                                <td>{{ $s->alamat ?? '-' }}</td>
                                <td>{{ $s->tahun_masuk ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">Belum ada data siswa di kelas ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
