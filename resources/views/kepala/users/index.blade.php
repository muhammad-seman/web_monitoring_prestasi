@extends('layouts.app')
@section('title', 'Daftar User')
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card w-100">
      <div class="card-body">
        <div class="d-md-flex align-items-center justify-content-between">
          <h4 class="card-title">Daftar User</h4>
        </div>
        <div class="table-responsive mt-4">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($users as $user)
              <tr>
                <td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                <td>{{ $user->nama }}</td>
                <td>{{ $user->username }}</td>
                <td>{{ $user->email }}</td>
                <td>
                  @php
                    $badge = match($user->role) {
                      'admin'          => 'primary',
                      'kepala_sekolah' => 'danger',
                      'guru'           => 'success',
                      'siswa'          => 'warning',
                      'wali'           => 'info',
                      default          => 'secondary'
                    };
                  @endphp
                  <span class="badge bg-{{ $badge }}">
                    {{ ucwords(str_replace('_', ' ', $user->role)) }}
                  </span>
                </td>
                <td>
                  <span class="badge bg-{{ $user->status == 'active' ? 'success' : 'danger' }}">
                    {{ ucfirst($user->status) }}
                  </span>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
          {{ $users->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection 