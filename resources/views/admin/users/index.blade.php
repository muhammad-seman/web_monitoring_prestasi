@extends('layouts.app')
@section('title', 'Manajemen User')
@section('content')
<!-- Row 1 -->
<div class="row">
  <div class="col-lg-12">
    <div class="card w-100">
      <div class="card-body">
        <div class="d-md-flex align-items-center justify-content-between">
          <h4 class="card-title">Manajemen User</h4>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">Tambah User</button>
        </div>

        @if ($errors->any())
          <div class="alert alert-danger mt-3">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

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
                <th>Siswa</th>
                <th>Wali/Anak</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($users as $user)
              <tr>
                <td>{{ $loop->iteration }}</td>
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
                <td>
                  @if($user->role === 'siswa')
                    {{ $user->siswa->nama ?? '-' }}
                  @else
                    -
                  @endif
                </td>
                <td>
                  @if($user->role === 'wali')
                    @if($user->anak->count() > 0)
                      <ul class="list-unstyled mb-0">
                        @foreach($user->anak as $anak)
                          <li><small class="badge bg-light text-dark">{{ $anak->nama }}</small></li>
                        @endforeach
                      </ul>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  @else
                    -
                  @endif
                </td>
                <td>
                  <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                    data-bs-target="#editUserModal{{ $user->id }}">Edit</button>
                  <button class="btn btn-danger btn-sm" onclick="confirmDelete({{ $user->id }})">Hapus</button>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
          {{ $users->links("pagination::bootstrap-4") }}
        </div>
      </div>
    </div>
  </div>
</div>

@foreach($users as $user)
<!-- Modal Edit -->
<div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="{{ route('admin.users.update', $user->id) }}">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Edit User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Nama</label>
          <input type="text" name="nama" class="form-control" value="{{ $user->nama }}" required>
        </div>
        <div class="mb-3">
          <label>Username</label>
          <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
        </div>
        <div class="mb-3">
          <label>Email</label>
          <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
        </div>
        <div class="mb-3">
          <label>Role</label>
          <select name="role" class="form-control" required>
            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="kepala_sekolah" {{ $user->role == 'kepala_sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
            <option value="guru" {{ $user->role == 'guru' ? 'selected' : '' }}>Guru</option>
            <option value="siswa" {{ $user->role == 'siswa' ? 'selected' : '' }}>Siswa</option>
            <option value="wali" {{ $user->role == 'wali' ? 'selected' : '' }}>Wali Murid</option>
          </select>
        </div>
        <div class="mb-3">
          <label>Status</label>
          <select name="status" class="form-control" required>
            <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
          </select>
        </div>
        @if($user->role === 'siswa')
        <div class="mb-3">
          <label>Assign ke Siswa</label>
          <select name="siswa_id" class="form-control">
            <option value="">- Pilih Siswa -</option>
            @foreach($siswa as $id => $nama)
              <option value="{{ $id }}" {{ $user->siswa_id == $id ? 'selected' : '' }}>{{ $nama }}</option>
            @endforeach
            @if($user->siswa && !isset($siswa[$user->siswa->id]))
              <option value="{{ $user->siswa->id }}" selected>{{ $user->siswa->nama }}</option>
            @endif
          </select>
        </div>
        @endif
        @if($user->role === 'wali')
        <div class="mb-3">
          <label>Assign Anak (Wali Murid)</label>
          <select name="anak_ids[]" class="form-control" multiple>
            @foreach($siswaWali as $id => $nama)
              <option value="{{ $id }}" {{ $user->anak->contains('id', $id) ? 'selected' : '' }}>{{ $nama }}</option>
            @endforeach
            @foreach($user->anak as $anak)
              @if(!isset($siswaWali[$anak->id]))
                <option value="{{ $anak->id }}" selected>{{ $anak->nama }}</option>
              @endif
            @endforeach
          </select>
          <small class="form-text text-muted">Pilih siswa yang akan di-assign sebagai anak dari wali ini. Gunakan Ctrl/Cmd untuk memilih multiple.</small>
        </div>
        @endif
        <div class="mb-3">
          <label>Password (isi jika ingin mengubah)</label>
          <input type="password" name="password" class="form-control" autocomplete="new-password">
          <input type="password" name="password_confirmation" class="form-control mt-1"
            placeholder="Konfirmasi Password" autocomplete="new-password">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success">Update</button>
      </div>
    </form>
  </div>
</div>
@endforeach

<!-- Modal Tambah -->
<div class="modal fade" id="createUserModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="{{ route('admin.users.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Tambah User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Nama</label>
          <input type="text" name="nama" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Username</label>
          <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Password</label>
          <input type="password" name="password" class="form-control" required autocomplete="new-password">
          <input type="password" name="password_confirmation" class="form-control mt-1"
            placeholder="Konfirmasi Password" autocomplete="new-password">
        </div>
        <div class="mb-3">
          <label>Role</label>
          <select name="role" class="form-control" required>
            <option value="admin">Admin</option>
            <option value="kepala_sekolah">Kepala Sekolah</option>
            <option value="guru">Guru</option>
            <option value="siswa">Siswa</option>
            <option value="wali">Wali Murid</option>
          </select>
        </div>
        <div class="mb-3">
          <label>Status</label>
          <select name="status" class="form-control" required>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
        <div class="mb-3" id="siswaAssignDiv" style="display: none;">
          <label>Assign ke Siswa</label>
          <select name="siswa_id" class="form-control">
            <option value="">- Pilih Siswa -</option>
            @foreach($siswa as $id => $nama)
              <option value="{{ $id }}">{{ $nama }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-3" id="anakAssignDiv" style="display: none;">
          <label>Assign Anak (Wali Murid)</label>
          <select name="anak_ids[]" class="form-control" multiple>
            @foreach($siswaWali as $id => $nama)
              <option value="{{ $id }}">{{ $nama }}</option>
            @endforeach
          </select>
          <small class="text-muted">Tahan Ctrl untuk memilih beberapa anak sekaligus</small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  function confirmDelete(userId) {
    Swal.fire({
      title: 'Yakin ingin menghapus user ini?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, Hapus'
    }).then((result) => {
      if (result.isConfirmed) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/users/${userId}`;
        form.innerHTML = '@csrf @method("DELETE")';
        document.body.appendChild(form);
        form.submit();
      }
    });
  }

  // Function to show/hide assignment fields based on role
  function toggleAssignmentFields(roleSelect) {
    const siswaDiv = document.getElementById('siswaAssignDiv');
    const anakDiv = document.getElementById('anakAssignDiv');
    
    // Hide both divs initially
    siswaDiv.style.display = 'none';
    anakDiv.style.display = 'none';
    
    // Show appropriate div based on role
    if (roleSelect.value === 'siswa') {
      siswaDiv.style.display = 'block';
    } else if (roleSelect.value === 'wali') {
      anakDiv.style.display = 'block';
    }
  }

  // Add event listener to role select in create modal
  document.addEventListener('DOMContentLoaded', function() {
    const createRoleSelect = document.querySelector('#createUserModal select[name="role"]');
    if (createRoleSelect) {
      createRoleSelect.addEventListener('change', function() {
        toggleAssignmentFields(this);
      });
    }
  });
</script>
@endsection