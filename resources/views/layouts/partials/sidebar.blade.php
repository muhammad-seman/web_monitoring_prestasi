<aside class="left-sidebar">
  <div>
    <div class="brand-logo d-flex align-items-center justify-content-between">
      <a href="#" class="text-nowrap logo-img">
        {{-- <img src="{{ asset('assets/images/logos/logo.svg') }}" alt="Logo" /> --}}
      </a>
      <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
        <i class="ti ti-x fs-6"></i>
      </div>
    </div>
    <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
      <ul id="sidebarnav">
        <li class="nav-small-cap">
          <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
          <span class="hide-menu">Menu Utama</span>
        </li>
        <!-- Hanya admin -->
        @if(auth()->user()->role === 'admin')

        <!-- ======= DASHBOARD ======= -->
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('admin.dashboard') }}" aria-expanded="false">
            <i class="ti ti-atom"></i>
            <span class="hide-menu">Dashboard</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('admin.logs.index') }}" aria-expanded="false">
            <i class="ti ti-list-details"></i>
            <span class="hide-menu">Riwayat Aktivitas</span>
          </a>
        </li>

        <!-- ======= MASTER DATA ======= -->
        <li class="sidebar-title mt-3 mb-1 fw-bold text-secondary">MASTER DATA</li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('admin.users.index') }}" aria-expanded="false">
            <i class="ti ti-users"></i>
            <span class="hide-menu">Manajemen User</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('admin.kelas.index') }}" aria-expanded="false">
            <i class="ti ti-school"></i>
            <span class="hide-menu">Manajemen Kelas</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('admin.siswa.index') }}" aria-expanded="false">
            <i class="ti ti-user"></i>
            <span class="hide-menu">Manajemen Siswa</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('admin.kategori_prestasi.index') }}" aria-expanded="false">
            <i class="ti ti-award"></i>
            <span class="hide-menu">Kategori Prestasi</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('admin.tingkat_penghargaan.index') }}" aria-expanded="false">
            <i class="ti ti-award"></i>
            <span class="hide-menu">Tingkat Penghargaan</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('admin.ekstrakurikuler.index') }}" aria-expanded="false">
            <i class="ti ti-school"></i>
            <span class="hide-menu">Ekstrakurikuler</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('admin.siswa_ekskul.index') }}" aria-expanded="false">
            <i class="ti ti-id-badge"></i>
            <span class="hide-menu">Siswa Ekstrakurikuler</span>
          </a>
        </li>

        <!-- ======= DATA PRESTASI SISWA ======= -->
        <li class="sidebar-title mt-3 mb-1 fw-bold text-secondary">TRANSAKSI</li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('admin.prestasi_siswa.index') }}" aria-expanded="false">
            <i class="ti ti-trophy"></i>
            <span class="hide-menu">Data Prestasi Siswa</span>
          </a>
        </li>
        @endif

        <!-- Hanya guru -->
        @if(auth()->user()->role === 'guru')
        <!-- ======= DASHBOARD GURU ======= -->
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('guru.dashboard') }}" aria-expanded="false">
            <i class="ti ti-atom"></i>
            <span class="hide-menu">Dashboard</span>
          </a>
        </li>

        <!-- ======= DATA KELAS ======= -->
        <li class="sidebar-title mt-3 mb-1 fw-bold text-secondary">DATA KELAS</li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('guru.siswa.index') }}" aria-expanded="false">
            <i class="ti ti-user"></i>
            <span class="hide-menu">Daftar Siswa</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('guru.kelas.index') }}" aria-expanded="false">
            <i class="ti ti-school"></i>
            <span class="hide-menu">Kelas yang Diampu</span>
          </a>
        </li>

        <!-- ======= PRESTASI SISWA ======= -->
        <li class="sidebar-title mt-3 mb-1 fw-bold text-secondary">PRESTASI SISWA</li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('guru.prestasi_siswa.index') }}" aria-expanded="false">
            <i class="ti ti-trophy"></i>
            <span class="hide-menu">Data Prestasi Siswa</span>
          </a>
        </li>
        {{-- Validasi & Upload dokumen: akses via detail prestasi, tidak perlu menu terpisah --}}

        <!-- ======= REFERENSI ======= -->
        <li class="sidebar-title mt-3 mb-1 fw-bold text-secondary">REFERENSI</li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('guru.ekstrakurikuler.index') }}" aria-expanded="false">
            <i class="ti ti-school"></i>
            <span class="hide-menu">Ekstrakurikuler</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('guru.kategori_prestasi.index') }}" aria-expanded="false">
            <i class="ti ti-award"></i>
            <span class="hide-menu">Kategori Prestasi</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('guru.tingkat_penghargaan.index') }}" aria-expanded="false">
            <i class="ti ti-award"></i>
            <span class="hide-menu">Tingkat Penghargaan</span>
          </a>
        </li>
        @endif

        <!-- Hanya kepala sekolah -->
        @if(auth()->user()->role === 'kepala_sekolah')
        <!-- ======= DASHBOARD KEPALA SEKOLAH ======= -->
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('kepala_sekolah.dashboard') }}" aria-expanded="false">
            <i class="ti ti-atom"></i>
            <span class="hide-menu">Dashboard</span>
          </a>
        </li>

        <!-- ======= REKAP PRESTASI ======= -->
        <li class="sidebar-title mt-3 mb-1 fw-bold text-secondary">REKAP PRESTASI</li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('kepala_sekolah.prestasi_siswa.index') }}" aria-expanded="false">
            <i class="ti ti-trophy"></i>
            <span class="hide-menu">Data Prestasi Siswa</span>
          </a>
        </li>

        <!-- ======= DATA SEKOLAH ======= -->
        <li class="sidebar-title mt-3 mb-1 fw-bold text-secondary">DATA SEKOLAH</li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('kepala_sekolah.siswa.index') }}" aria-expanded="false">
            <i class="ti ti-user"></i>
            <span class="hide-menu">Daftar Siswa</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('kepala_sekolah.kelas.index') }}" aria-expanded="false">
            <i class="ti ti-school"></i>
            <span class="hide-menu">Daftar Kelas</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('kepala_sekolah.ekstrakurikuler.index') }}" aria-expanded="false">
            <i class="ti ti-school"></i>
            <span class="hide-menu">Ekstrakurikuler</span>
          </a>
        </li>

        <!-- ======= SISTEM ======= -->
        <li class="sidebar-title mt-3 mb-1 fw-bold text-secondary">SISTEM</li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('kepala_sekolah.users.index') }}" aria-expanded="false">
            <i class="ti ti-users"></i>
            <span class="hide-menu">Daftar User</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('kepala_sekolah.logs.index') }}" aria-expanded="false">
            <i class="ti ti-list-details"></i>
            <span class="hide-menu">Log Aktivitas</span>
          </a>
        </li>
        @endif

        <!-- Hanya wali -->
        @if(auth()->user()->role === 'wali')
        <!-- ======= DASHBOARD WALI ======= -->
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('wali.dashboard') }}" aria-expanded="false">
            <i class="ti ti-atom"></i>
            <span class="hide-menu">Dashboard</span>
          </a>
        </li>

        <!-- ======= DATA ANAK ======= -->
        <li class="sidebar-title mt-3 mb-1 fw-bold text-secondary">DATA ANAK</li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('wali.siswa.index') }}" aria-expanded="false">
            <i class="ti ti-user"></i>
            <span class="hide-menu">Siswa (Anak Sendiri)</span>
          </a>
        </li>

        <!-- ======= PRESTASI ANAK ======= -->
        <li class="sidebar-title mt-3 mb-1 fw-bold text-secondary">PRESTASI ANAK</li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('wali.prestasi.index') }}" aria-expanded="false">
            <i class="ti ti-trophy"></i>
            <span class="hide-menu">Prestasi Siswa</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('wali.dokumen.index') }}" aria-expanded="false">
            <i class="ti ti-file-text"></i>
            <span class="hide-menu">Dokumen Prestasi</span>
          </a>
        </li>
        @endif

        @if(auth()->user()->role === 'siswa')
        <!-- ======= DASHBOARD SISWA ======= -->
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('siswa.dashboard') }}" aria-expanded="false">
            <i class="ti ti-atom"></i>
            <span class="hide-menu">Dashboard</span>
          </a>
        </li>

        <!-- ======= DATA DIRI ======= -->
        <li class="sidebar-title mt-3 mb-1 fw-bold text-secondary">DATA DIRI</li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('siswa.profil.index') }}" aria-expanded="false">
            <i class="ti ti-user"></i>
            <span class="hide-menu">Profil Saya</span>
          </a>
        </li>

        <!-- ======= PRESTASI SAYA ======= -->
        <li class="sidebar-title mt-3 mb-1 fw-bold text-secondary">PRESTASI SAYA</li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('siswa.prestasi.index') }}" aria-expanded="false">
            <i class="ti ti-trophy"></i>
            <span class="hide-menu">Prestasi Siswa</span>
          </a>
        </li>
        @endif

      </ul>
    </nav>
  </div>
</aside>