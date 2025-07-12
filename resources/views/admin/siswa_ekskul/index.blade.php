@extends('layouts.app')
@section('title', 'Siswa Ekstrakurikuler')

@section('content')
<script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
<div class="row">
    <div class="col-lg-12">
        <div class="card w-100">
            <div class="card-body">
                <!-- Filter & Action -->
                <div class="d-md-flex align-items-center justify-content-between mb-3">
                    <h4 class="card-title">Siswa Ekstrakurikuler</h4>
                    <div class="d-flex gap-2">
                        <form method="GET" class="d-flex align-items-center gap-2">
                            <select name="ekskul" class="form-select">
                                <option value="">Semua Ekstrakurikuler</option>
                                @foreach($ekskul as $id => $nama)
                                <option value="{{ $id }}" {{ request('ekskul')==$id ? 'selected' : '' }}>
                                    {{ $nama }}
                                </option>
                                @endforeach
                            </select>
                            <select name="kelas" class="form-select">
                                <option value="">Semua Kelas</option>
                                @foreach($kelas as $id => $nama_kelas)
                                <option value="{{ $id }}" {{ request('kelas')==$id ? 'selected' : '' }}>
                                    {{ $nama_kelas }}
                                </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-secondary" title="Terapkan Filter">
                                <span class="iconify" data-icon="mdi:filter-variant" data-width="20" data-height="20"></span>
                            </button>
                        </form>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSiswaEkskulModal">
                            <span class="iconify" data-icon="mdi:plus" data-width="20" data-height="20"></span>
                        </button>
                    </div>
                </div>

                @if(session('success'))
                <div class="alert alert-success mt-3">
                    {{ session('success') }}
                </div>
                @endif

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
                                <th>Nama Siswa</th>
                                <th>NISN</th>
                                <th>Kelas</th>
                                <th>Ekstrakurikuler</th>
                                <th>Pembina</th>
                                <th>Tanggal Bergabung</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($siswaEkskul as $se)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $se->siswa->nama ?? '-' }}</td>
                                <td>{{ $se->siswa->nisn ?? '-' }}</td>
                                <td>{{ $se->siswa->kelas->nama_kelas ?? '-' }}</td>
                                <td>{{ $se->ekskul->nama ?? '-' }}</td>
                                <td>{{ $se->ekskul->pembina ?? '-' }}</td>
                                <td>{{ $se->created_at ? \Carbon\Carbon::parse($se->created_at)->format('d-m-Y') : '-' }}</td>
                                <td>
                                    <button class="btn btn-danger btn-sm" onclick="confirmDelete({{ $se->id }})" title="Hapus">
                                        <span class="iconify" data-icon="mdi:trash-can" data-width="18" data-height="18"></span>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">Belum ada data siswa ekstrakurikuler.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $siswaEkskul->links("pagination::bootstrap-4") }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Siswa Ekstrakurikuler -->
<div class="modal fade" id="createSiswaEkskulModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="POST" action="{{ route('admin.siswa_ekskul.store') }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Siswa ke Ekstrakurikuler</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Siswa <span class="text-danger">*</span></label>
                    <select name="id_siswa" class="form-control @error('id_siswa') is-invalid @enderror" required>
                        <option value="">- Pilih Siswa -</option>
                        @foreach($siswa as $s)
                        <option value="{{ $s->id }}" {{ old('id_siswa') == $s->id ? 'selected' : '' }}>
                            {{ $s->nama }} ({{ $s->nisn }}) - {{ $s->kelas->nama_kelas ?? '-' }}
                        </option>
                        @endforeach
                    </select>
                    @error('id_siswa')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label>Ekstrakurikuler <span class="text-danger">*</span></label>
                    <select name="id_ekskul" class="form-control @error('id_ekskul') is-invalid @enderror" required>
                        <option value="">- Pilih Ekstrakurikuler -</option>
                        @foreach($ekskul as $id => $nama)
                        <option value="{{ $id }}" {{ old('id_ekskul') == $id ? 'selected' : '' }}>
                            {{ $nama }}
                        </option>
                        @endforeach
                    </select>
                    @error('id_ekskul')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
    function confirmDelete(siswaEkskulId) {
    Swal.fire({
      title: 'Yakin ingin menghapus data ini?',
      text: "Siswa akan dihapus dari ekstrakurikuler tersebut",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, Hapus'
    }).then((result) => {
      if (result.isConfirmed) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/siswa-ekskul/${siswaEkskulId}`;
        form.innerHTML = '@csrf @method("DELETE")';
        document.body.appendChild(form);
        form.submit();
      }
    });
  }
</script>
@endsection 