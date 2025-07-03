<aside class="left-sidebar">
  <div>
    <div class="brand-logo d-flex align-items-center justify-content-between">
      <a href="#" class="text-nowrap logo-img">
        <img src="{{ asset('assets/images/logos/logo.svg') }}" alt="Logo" />
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

        <!-- ======= DATA PRESTASI SISWA ======= -->
        <li class="sidebar-title mt-3 mb-1 fw-bold text-secondary">TRANSAKSI</li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('admin.prestasi_siswa.index') }}" aria-expanded="false">
            <i class="ti ti-trophy"></i>
            <span class="hide-menu">Data Prestasi Siswa</span>
          </a>
        </li>
        @endif

      </ul>
    </nav>
  </div>
</aside>