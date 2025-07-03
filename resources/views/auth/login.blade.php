<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login </title>
  <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/favicon.png') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />

  {{-- SweetAlert2 CDN --}}
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <div
      class="position-relative overflow-hidden text-bg-light min-vh-100 d-flex align-items-center justify-content-center">
      <div class="d-flex align-items-center justify-content-center w-100">
        <div class="row justify-content-center w-100">
          <div class="col-md-8 col-lg-6 col-xxl-3">
            <div class="card mb-0">
              <div class="card-body">
                <a href="/" class="text-nowrap logo-img text-center d-block py-3 w-100">
                  {{-- Ganti dengan logo sistem akademik Anda --}}
                  {{-- <img src="{{ asset('assets/images/logos/logo.svg') }}" width="180"
                    alt="Logo Sistem Informasi Akademik"> --}}
                </a>
                <h1 class="text-center mb-4">Login</h1>

                {{-- Form Login --}}
                <form method="POST" action="{{ route('login') }}">
                  @csrf

                  {{-- Input Username atau Email --}}
                  <div class="mb-3">
                    <label for="login" class="form-label">Username atau Email</label>
                    <input type="text" class="form-control @error('login') is-invalid @enderror" id="login" name="login"
                      value="{{ old('login') }}" required autocomplete="username" autofocus>
                    @error('login')
                    <div class="invalid-feedback">
                      {{ $message }}
                    </div>
                    @enderror
                  </div>


                  {{-- Input Password --}}
                  <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                      name="password" required autocomplete="current-password">
                    @error('password')
                    <div class="invalid-feedback">
                      {{ $message }}
                    </div>
                    @enderror
                  </div>


                  {{-- Remember Me --}}
                  <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="form-check">
                      <input class="form-check-input primary" type="checkbox" name="remember" id="remember" {{
                        old('remember') ? 'checked' : '' }}>
                      <label class="form-check-label text-dark" for="remember">
                        Ingat Saya
                      </label>
                    </div>
                    {{-- <a class="text-primary fw-bold" href="{{ route('password.request') }}">Lupa Password?</a> --}}
                  </div>

                  {{-- Tombol Sign In --}}
                  <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Masuk</button>

                  {{-- Teks Tambahan --}}
                  <div class="d-flex align-items-center justify-content-center">
                    <p class="fs-4 mb-0 fw-bold">Belum punya akun?</p>
                    <span class="ms-2">Hubungi Admin</span>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

  {{-- SweetAlert Script --}}
  <script>
    @if (session('status'))
      Swal.fire({
        icon: 'success',
        title: 'Sukses',
        text: "{{ session('status') }}",
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'OK'
      });
    @endif
  </script>
</body>

</html>