@extends('layouts.app')
@section('title', 'Profil Siswa')
@section('content')
<div class="row">
  <div class="col-lg-4">
    <div class="card text-center">
      <div class="card-body">
        <img src="{{ asset('assets/images/profile/user-1.jpg') }}" class="rounded-circle mb-3" width="120" height="120"
          alt="Foto Profil">
        <h5>{{ $student->user->name }}</h5>
        <p class="text-muted">{{ $student->nisn }}</p>

      </div>
    </div>
  </div>



  <div class="col-lg-8">
    <div class="row">

      <div class="col-md-12">
        <div class="card mb-3">
          <div class="card-body">
            @if($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
            @endif
            <div class="d-flex justify-content-center gap-2 mt-2">
              <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditUser">Edit
                Akun</button>
              <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalEditStudent">Edit
                Data Siswa</button>
              <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalEditParent">Edit Data
                Orang Tua</button>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-12">
        <div class="card mb-3">
          <div class="card-header fw-bold">Profil Siswa</div>
          <div class="card-body row">
            <div class="col-md-6"><strong>Nama Lengkap:</strong><br>{{ $student->user->name }}</div>
            <div class="col-md-6"><strong>Kelas:</strong><br>{{ $student->class }}</div>
            <div class="col-md-6 mt-3"><strong>NISN:</strong><br>{{ $student->nisn }}</div>
            <div class="col-md-6 mt-3"><strong>Angkatan:</strong><br>{{ $student->graduation_year }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card mb-3">
          <div class="card-header fw-bold">Data Siswa</div>
          <div class="card-body">
            <p><strong>Email:</strong> {{ $student->user->email }}</p>
            <p><strong>Telepon:</strong> {{ $student->phone }}</p>
            <p><strong>Tempat Lahir:</strong> {{ $student->place_of_birth }}</p>
            <p><strong>Tanggal Lahir:</strong> {{ $student->birth_date }}</p>
            <p><strong>Jenis Kelamin:</strong> {{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
            <p><strong>Agama:</strong> {{ $student->religion }}</p>
            <p><strong>Provinsi:</strong> {{ $student->province }}</p>
            <p><strong>Kabupaten:</strong> {{ $student->district }}</p>
            <p><strong>Kecamatan:</strong> {{ $student->sub_district }}</p>
            <p><strong>Kelurahan:</strong> {{ $student->village }}</p>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card mb-3">
          <div class="card-header fw-bold">Data Orang Tua</div>
          <div class="card-body">
            <h6 class="fw-semibold border-bottom pb-1 mb-2">Ayah</h6>
            <p><strong>Nama:</strong> {{ $student->parent->father_name ?? '-' }}</p>
            <p><strong>Telepon:</strong> {{ $student->parent->father_phone ?? '-' }}</p>
            <p><strong>Pekerjaan:</strong> {{ $student->parent->father_job ?? '-' }}</p>

            <h6 class="fw-semibold border-bottom pb-1 mt-4 mb-2">Ibu</h6>
            <p><strong>Nama:</strong> {{ $student->parent->mother_name ?? '-' }}</p>
            <p><strong>Telepon:</strong> {{ $student->parent->mother_phone ?? '-' }}</p>
            <p><strong>Pekerjaan:</strong> {{ $student->parent->mother_job ?? '-' }}</p>
          </div>
        </div>

        <div class="card mb-3">
          <div class="card-header fw-bold">Asal Sekolah</div>
          <div class="card-body">
            <p><strong>Nama Sekolah:</strong> {{ $student->origin_school_name }}</p>
            <p><strong>Alamat Sekolah:</strong> {{ $student->origin_school_address }}</p>
            <p><strong>Tahun Lulus:</strong> {{ $student->graduation_year }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalEditUser" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('student-profile.update-user') }}">
      @csrf @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Edit Akun</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="mb-3">
          <label>Nama</label>
          <input type="text" name="name" value="{{ $student->user->name }}" class="form-control" required>
        </div>

        <div class="mb-3">
          <label>Email</label>
          <input type="email" name="email" value="{{ $student->user->email }}" class="form-control" required>
        </div>

        <hr>

        <div class="mb-2">
          <label class="form-label">Password Baru (opsional)</label>
          <input type="password" name="password" class="form-control"
            placeholder="Biarkan kosong jika tidak ingin mengubah">
        </div>

        <div class="mb-2">
          <label class="form-label">Konfirmasi Password Baru</label>
          <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="modalEditStudent" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="{{ route('student-profile.update-student') }}">
      @csrf @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Edit Data Siswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body row">
        <div class="col-md-6 mb-3">
          <label>NISN</label>
          <input type="text" name="nisn" value="{{ $student->nisn }}" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
          <label>Kelas</label>
          <select name="class" class="form-control" disabled>
            <option value="10" {{ $student->class == 'X' ? 'selected' : '' }}>X</option>
            <option value="11" {{ $student->class == 'XI' ? 'selected' : '' }}>XI</option>
            <option value="12" {{ $student->class == 'XII' ? 'selected' : '' }}>XII</option>
          </select>
        </div>
        <div class="col-md-6 mb-3">
          <label>Tempat Lahir</label>
          <input type="text" name="place_of_birth" value="{{ $student->place_of_birth }}" class="form-control">
        </div>
        <div class="col-md-6 mb-3">
          <label>Tanggal Lahir</label>
          <input type="date" name="birth_date" value="{{ $student->birth_date }}" class="form-control">
        </div>
        <div class="col-md-6 mb-3">
          <label>Telepon</label>
          <input type="text" name="phone" value="{{ $student->phone }}" class="form-control">
        </div>
        <div class="col-md-6 mb-3">
          <label>Agama</label>
          <select name="religion" class="form-select" required>
            <option value="Islam" {{ $student->religion == 'Islam' ? 'selected' : '' }}>Islam</option>
            <option value="Kristen" {{ $student->religion == 'Kristen' ? 'selected' : '' }}>Kristen</option>
            <option value="Katolik" {{ $student->religion == 'Katolik' ? 'selected' : '' }}>Katolik</option>
            <option value="Hindu" {{ $student->religion == 'Hindu' ? 'selected' : '' }}>Hindu</option>
            <option value="Buddha" {{ $student->religion == 'Buddha' ? 'selected' : '' }}>Buddha</option>
            <option value="Konghucu" {{ $student->religion == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
          </select>
        </div>
        <div class="col-md-6 mb-3">
          <label>Provinsi</label>
          <input type="text" name="province" value="{{ $student->province }}" class="form-control">
        </div>
        <div class="col-md-6 mb-3">
          <label>Kabupaten</label>
          <input type="text" name="district" value="{{ $student->district }}" class="form-control">
        </div>
        <div class="col-md-6 mb-3">
          <label>Kecamatan</label>
          <input type="text" name="sub_district" value="{{ $student->sub_district }}" class="form-control">
        </div>
        <div class="col-md-6 mb-3">
          <label>Kelurahan</label>
          <input type="text" name="village" value="{{ $student->village }}" class="form-control">
        </div>
        <div class="col-md-6 mb-3">
          <label>Asal Sekolah</label>
          <input type="text" name="origin_school_name" value="{{ $student->origin_school_name }}" class="form-control">
        </div>
        <div class="col-md-6 mb-3">
          <label>Alamat Asal Sekolah</label>
          <input type="text" name="origin_school_address" value="{{ $student->origin_school_address }}"
            class="form-control">
        </div>
        <div class="col-md-6 mb-3">
          <label>Tahun Lulus</label>
          <input type="text" name="graduation_year" value="{{ $student->graduation_year }}" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button class="btn btn-success">Simpan</button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="modalEditParent" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('student-profile.update-parent') }}">
      @csrf @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Edit Data Orang Tua</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3"><label>Nama Ayah</label>
          <input type="text" name="father_name" class="form-control" value="{{ $student->parent->father_name ?? '' }}">
        </div>
        <div class="mb-3"><label>Telepon Ayah</label>
          <input type="text" name="father_phone" class="form-control"
            value="{{ $student->parent->father_phone ?? '' }}">
        </div>
        <div class="mb-3"><label>Pekerjaan Ayah</label>
          <input type="text" name="father_job" class="form-control" value="{{ $student->parent->father_job ?? '' }}">
        </div>
        <div class="mb-3"><label>Nama Ibu</label>
          <input type="text" name="mother_name" class="form-control" value="{{ $student->parent->mother_name ?? '' }}">
        </div>
        <div class="mb-3"><label>Telepon Ibu</label>
          <input type="text" name="mother_phone" class="form-control"
            value="{{ $student->parent->mother_phone ?? '' }}">
        </div>
        <div class="mb-3"><label>Pekerjaan Ibu</label>
          <input type="text" name="mother_job" class="form-control" value="{{ $student->parent->mother_job ?? '' }}">
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button class="btn btn-success">Simpan</button>
      </div>
    </form>
  </div>
</div>
@endsection