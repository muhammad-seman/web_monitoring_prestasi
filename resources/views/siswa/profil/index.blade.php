@extends('layouts.app')
@section('title', 'Profil Saya')
@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3">Profil Saya</h4>
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <form method="POST" action="{{ route('siswa.profil.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label>Nama</label>
                        <input type="text" class="form-control" value="{{ $siswa->nama ?? '-' }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" class="form-control" value="{{ $user->username ?? '-' }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="text" class="form-control" value="{{ $user->email ?? '-' }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label>NISN</label>
                        <input type="text" class="form-control" value="{{ $siswa->nisn ?? '-' }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label>Jenis Kelamin</label>
                        <input type="text" class="form-control" value="{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : ($siswa->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label>Tanggal Lahir</label>
                        <input type="text" class="form-control" value="{{ $siswa->tanggal_lahir ?? '-' }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label>Kelas</label>
                        <input type="text" class="form-control" value="{{ $siswa && $siswa->kelas ? $siswa->kelas->nama_kelas : '-' }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label>Tahun Masuk</label>
                        <input type="text" class="form-control" value="{{ $siswa->tahun_masuk ?? '-' }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label>Alamat</label>
                        <input type="text" name="alamat" class="form-control" value="{{ old('alamat', $siswa->alamat ?? '') }}">
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 