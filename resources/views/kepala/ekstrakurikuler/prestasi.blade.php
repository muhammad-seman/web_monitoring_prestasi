@extends('layouts.app')
@section('title', 'Prestasi Siswa Ekskul')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3">Prestasi Siswa - {{ $ekstrakurikuler->nama }}</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Kategori Prestasi</th>
                                <th>Tingkat Penghargaan</th>
                                <th>Tahun</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($prestasi as $p)
                            <tr>
                                <td>{{ ($prestasi->currentPage() - 1) * $prestasi->perPage() + $loop->iteration }}</td>
                                <td>{{ $p->siswa->nama ?? '-' }}</td>
                                <td>{{ $p->siswa->kelas->nama_kelas ?? '-' }}</td>
                                <td>{{ $p->kategoriPrestasi->nama_kategori ?? '-' }}</td>
                                <td>{{ $p->tingkatPenghargaan->tingkat ?? '-' }}</td>
                                <td>{{ $p->tanggal_prestasi ? substr($p->tanggal_prestasi,0,4) : '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data prestasi</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $prestasi->links('pagination::bootstrap-4') }}
                </div>
                <a href="{{ route('kepala_sekolah.ekstrakurikuler.index') }}" class="btn btn-secondary mt-3">Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection 