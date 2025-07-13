<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Web Monitoring Prestasi

Sistem monitoring prestasi siswa berbasis web dengan role-based access control.

## Fitur Dashboard Admin

Dashboard admin menyediakan monitoring komprehensif untuk seluruh sistem dengan fitur-fitur berikut:

### 📊 Statistik Utama
- **Total Siswa**: Jumlah siswa terdaftar dalam sistem
- **Total Prestasi**: Jumlah prestasi dengan breakdown status (tervalidasi, pending, ditolak)
- **Total Kelas**: Jumlah kelas dengan rata-rata siswa per kelas
- **Total Ekstrakurikuler**: Jumlah ekskul aktif dengan total anggota

### 👥 Statistik Pengguna
- Breakdown pengguna berdasarkan role:
  - Guru
  - Wali Kelas
  - Kepala Sekolah
  - Admin

### 🏆 Status Prestasi
- Prestasi Tervalidasi
- Prestasi Pending
- Prestasi Ditolak

### 📈 Grafik dan Visualisasi
- **Tren Prestasi**: Grafik area 6 bulan terakhir
- **Prestasi per Kategori**: Donut chart distribusi kategori prestasi

### 📋 Tabel Ranking
- **Top 5 Kelas**: Kelas dengan prestasi terbanyak
- **Top 5 Ekstrakurikuler**: Ekskul dengan anggota terbanyak

### ⏰ Aktivitas Terbaru
- Timeline aktivitas sistem 10 terbaru
- Prestasi terbaru yang ditambahkan

### 🎨 Fitur UI/UX
- Responsive design untuk semua device
- Loading states dan empty states
- Error handling yang graceful
- Interactive charts dengan ApexCharts
- Modern card-based layout

## Teknologi yang Digunakan

- **Backend**: Laravel 10
- **Frontend**: Bootstrap 5, Tabler Icons
- **Charts**: ApexCharts
- **Database**: MySQL/PostgreSQL

## Struktur Data Dashboard

Dashboard admin mengakses data dari model-model berikut:
- `User` - Data pengguna dan role
- `Siswa` - Data siswa
- `PrestasiSiswa` - Data prestasi dengan relasi
- `Kelas` - Data kelas dan wali kelas
- `Ekstrakurikuler` - Data ekskul dan anggota
- `ActivityLog` - Log aktivitas sistem
- `KategoriPrestasi` - Kategori prestasi
- `TingkatPenghargaan` - Tingkat penghargaan

## Akses Dashboard

Dashboard admin dapat diakses oleh user dengan role `admin` melalui route:
```
/admin/dashboard
```

## Screenshot Dashboard

Dashboard admin menampilkan:
1. **Row 1**: 4 card statistik utama dengan icon dan warna berbeda
2. **Row 2**: 2 card statistik pengguna dan status prestasi
3. **Row 3**: Grafik tren prestasi dan distribusi kategori
4. **Row 4**: Tabel ranking kelas dan ekstrakurikuler
5. **Row 5**: Timeline aktivitas dan prestasi terbaru

Semua komponen responsive dan menangani kasus data kosong dengan graceful fallback.
