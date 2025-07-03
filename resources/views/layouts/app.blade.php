<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title')</title>
  <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/favicon.png') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
  <!-- Font Awesome CDN -->
  {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-papm6kAMPx/+qvOB0fOkH1ZZ1xd6QbaO5jM90+hCbGyF/F7fs/3Gzdh0dX8GZFODdgNpTiFqouBZfyqCk41Z1w==" crossorigin="anonymous" referrerpolicy="no-referrer" /> --}}
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">

    {{-- @include('layouts.partials.topstrip') --}}
    
    @include('layouts.partials.sidebar')
    <!--  Main wrapper -->
    <div class="body-wrapper">
      <div class="body-wrapper-inner">
        @include('layouts.partials.header')
        <div class="container-fluid">
          @yield('content')
          @include('layouts.partials.footer')
        </div>
      </div>
    </div>
  </div>
  <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/sidebarmenu.js') }}"></script>
  <script src="{{ asset('assets/js/app.min.js') }}"></script>
  <script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
  <script src="{{ asset('assets/libs/simplebar/dist/simplebar.js') }}"></script>
  <script src="{{ asset('assets/js/dashboard.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    // Logic for SweetAlert2 on successful login
      $(document).ready(function() {
          @if(session('success'))
              Swal.fire({
                  icon: 'success',
                  title: 'Berhasil!',
                  text: '{{ session('success') }}',
                  showConfirmButton: false,
                  timer: 2000 // Notifikasi akan hilang setelah 2 detik
              });
          @endif
      });
  </script>

  <!-- solar icons -->
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
</body>

</html>