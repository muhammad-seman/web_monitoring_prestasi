<!-- Header Start -->
<header class="app-header">
  <nav class="navbar navbar-expand-lg navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item d-block d-xl-none">
        <a class="nav-link sidebartoggler" id="headerCollapse" href="javascript:void(0)">
          <i class="ti ti-menu-2"></i>
        </a>
      </li>
    </ul>

    <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
      <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
        @if(auth()->user()->role === 'admin')
          <li class="nav-item dropdown me-3">
            <a class="nav-link position-relative" href="#" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="ti ti-bell fs-4"></i>
              @php
                $pendingCount = \App\Models\PrestasiSiswa::where('status', 'menunggu_validasi')->count();
              @endphp
              @if($pendingCount > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                  {{ $pendingCount }}
                </span>
              @endif
            </a>
            <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="notifDropdown" style="width: 300px;">
              <div class="message-body">
                <h6 class="fw-semibold fs-4 py-2 px-3 mb-0">Prestasi Menunggu Validasi</h6>
                <div class="dropdown-divider"></div>
                @php
                  $pendingPrestasi = \App\Models\PrestasiSiswa::with('siswa')
                    ->where('status', 'menunggu_validasi')
                    ->latest()
                    ->take(5)
                    ->get();
                @endphp
                @forelse($pendingPrestasi as $prestasi)
                  <a href="{{ route('admin.prestasi_siswa.index') }}" class="d-flex align-items-center dropdown-item py-2">
                    <div class="flex-shrink-0">
                      <div class="bg-primary-subtle rounded-circle p-2">
                        <i class="ti ti-trophy text-primary"></i>
                      </div>
                    </div>
                    <div class="ms-2 flex-grow-1">
                      <h6 class="mb-0 fs-3">{{ Str::limit($prestasi->nama_prestasi, 25) }}</h6>
                      <span class="fs-2 text-muted">{{ $prestasi->siswa->nama ?? 'Siswa' }}</span>
                    </div>
                  </a>
                @empty
                  <div class="px-3 py-2 text-center text-muted">
                    <i class="ti ti-bell-off fs-6"></i>
                    <p class="mb-0 fs-3">Tidak ada prestasi menunggu validasi</p>
                  </div>
                @endforelse
                @if($pendingCount > 5)
                  <div class="dropdown-divider"></div>
                  <a href="{{ route('admin.prestasi_siswa.index') }}" class="d-block text-center py-2 text-primary">
                    Lihat {{ $pendingCount - 5 }} lainnya
                  </a>
                @endif
              </div>
            </div>
          </li>
        @endif
        
        @if(auth()->user()->role === 'wali')
          <li class="nav-item dropdown me-3">
            <a class="nav-link position-relative" href="#" id="waliNotifDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="ti ti-bell fs-4"></i>
              @php
                $unreadCount = auth()->user()->unreadNotifications()->count();
              @endphp
              @if($unreadCount > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notification-badge">
                  {{ $unreadCount }}
                </span>
              @endif
            </a>
            <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="waliNotifDropdown" style="width: 350px;">
              <div class="message-body">
                <h6 class="fw-semibold fs-4 py-2 px-3 mb-0">Notifikasi Prestasi Anak</h6>
                <div class="dropdown-divider"></div>
                @php
                  $notifications = auth()->user()->notifications()->orderBy('created_at', 'desc')->take(5)->get();
                @endphp
                @forelse($notifications as $notification)
                  <a href="{{ route('wali.prestasi.index') }}" class="d-flex align-items-center dropdown-item py-2 {{ $notification->is_read ? '' : 'bg-light' }}" 
                     onclick="markAsRead({{ $notification->id }})">
                    <div class="flex-shrink-0">
                      <div class="bg-success-subtle rounded-circle p-2">
                        <i class="ti ti-trophy text-success"></i>
                      </div>
                    </div>
                    <div class="ms-2 flex-grow-1">
                      <h6 class="mb-0 fs-3">{{ $notification->title }}</h6>
                      <span class="fs-2 text-muted">{{ Str::limit($notification->message, 40) }}</span>
                      <div class="text-muted fs-1 mt-1">{{ $notification->created_at->diffForHumans() }}</div>
                    </div>
                    @if(!$notification->is_read)
                      <div class="flex-shrink-0">
                        <span class="badge bg-primary">Baru</span>
                      </div>
                    @endif
                  </a>
                @empty
                  <div class="px-3 py-2 text-center text-muted">
                    <i class="ti ti-bell-off fs-6"></i>
                    <p class="mb-0 fs-3">Tidak ada notifikasi</p>
                  </div>
                @endforelse
                @if($unreadCount > 5)
                  <div class="dropdown-divider"></div>
                  <a href="{{ route('wali.prestasi.index') }}" class="d-block text-center py-2 text-primary">
                    Lihat semua notifikasi
                  </a>
                @endif
              </div>
            </div>
          </li>
        @endif
        
        <li class="nav-item dropdown">
          <a class="nav-link" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="{{ asset('assets/images/profile/user-1.jpg') }}" alt="Foto Profil" width="35" height="35"
              class="rounded-circle">
          </a>
          <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
            <div class="message-body">
              {{-- <a href="{{ route('student-profile') }}" class="d-flex align-items-center gap-2 dropdown-item">
                <i class="ti ti-user fs-6"></i>
                <p class="mb-0 fs-3">Profil Saya</p>
              </a> --}}
              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
              </form>
              <button type="button" class="btn btn-outline-primary mx-3 mt-2 d-block" id="logout-btn">
                Logout
              </button>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </nav>
</header>
<!-- Header End -->

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
      logoutBtn.addEventListener('click', function () {
        Swal.fire({
          title: 'Yakin ingin logout?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Ya, logout!',
          cancelButtonText: 'Batal'
        }).then((result) => {
          if (result.isConfirmed) {
            document.getElementById('logout-form').submit();
          }
        });
      });
    }
  });
</script>