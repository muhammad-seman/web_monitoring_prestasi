@extends('layouts.app')
@section('title', 'Kelas yang Diampu')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card w-100">
            <div class="card-body">
                <div class="d-md-flex align-items-center justify-content-between">
                    <h4 class="card-title">Kelas yang Diampu</h4>
                </div>

                @if(session('success'))
                <div class="alert alert-success mt-3">
                    {{ session('success') }}
                </div>
                @endif

                <div class="table-responsive mt-4">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kelas</th>
                                <th>Tahun Ajaran</th>
                                <th>Jumlah Siswa</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kelas as $k)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $k->nama_kelas }}</td>
                                <td>{{ $k->tahun_ajaran }}</td>
                                <td>{{ $k->siswa()->count() }}</td>
                                <td>
                                    <a href="{{ route('guru.siswa.index', ['kelas' => $k->id]) }}" class="btn btn-info btn-sm">
                                        Lihat Siswa
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada kelas yang diampu.</td>
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
