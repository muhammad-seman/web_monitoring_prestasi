@extends('layouts.app')
@section('title', 'Riwayat Aktivitas')
@section('content')
<div class="card">
    <div class="card-body">
        <h4 class="card-title">Riwayat Aktivitas</h4>

        <!-- Filter form (optional) -->
        <form method="GET" class="row mb-3 g-2">
            <div class="col">
                <select name="user_id" class="form-control">
                    <option value="">Semua User</option>
                    @foreach($users as $id => $nama)
                        <option value="{{ $id }}" {{ request('user_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col">
                <input type="text" name="action" class="form-control" placeholder="Action" value="{{ request('action') }}">
            </div>
            <div class="col">
                <input type="text" name="module" class="form-control" placeholder="Module" value="{{ request('module') }}">
            </div>
            <div class="col">
                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
            </div>
            <div class="col-auto">
                <button class="btn btn-secondary" type="submit">Filter</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Aksi</th>
                        <th>Modul</th>
                        <th>Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->created_at }}</td>
                        <td>{{ $log->user->nama ?? '-' }}</td>
                        <td>{{ ucfirst($log->action) }}</td>
                        <td>{{ ucfirst($log->module) }}</td>
                        <td>{{ $log->description }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada log aktivitas.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            {{ $logs->withQueryString()->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection