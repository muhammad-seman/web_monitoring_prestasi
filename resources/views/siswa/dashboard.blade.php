@extends('layouts.app')
@section('title', 'Dashboard Siswa')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3">Dashboard Siswa</h4>
                <p>Selamat datang di dashboard! Di sini Anda dapat melihat ringkasan prestasi pribadi dan data diri Anda.</p>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <strong>Ringkasan Prestasi:</strong>
                            <ul class="mb-0">
                                <li>Total Prestasi: {{ $total }}</li>
                                <li>Prestasi Diterima: {{ $diterima }}</li>
                                <li>Prestasi Pending: {{ $pending }}</li>
                                <li>Prestasi Ditolak: {{ $ditolak }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-secondary">
                            <strong>Data Diri:</strong>
                            <ul class="mb-0">
                                <li>Nama: {{ $siswa->nama ?? '-' }}</li>
                                <li>Kelas: {{ $siswa && $siswa->kelas ? $siswa->kelas->nama_kelas : '-' }}</li>
                                <li>Email: {{ auth()->user()->email }}</li>
                            </ul>
                        </div>
                        @if($ekskulList && $ekskulList->count())
                        <div class="alert alert-success mt-3">
                            <strong>Ekstrakurikuler Diikuti:</strong>
                            <ul class="mb-0">
                                @foreach($ekskulList as $ek)
                                    <li>{{ $ek->nama }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 