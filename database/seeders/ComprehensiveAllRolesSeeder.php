<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\PrestasiSiswa;
use App\Models\KategoriPrestasi;
use App\Models\TingkatPenghargaan;
use App\Models\Ekstrakurikuler;
use App\Models\SiswaEkskul;

class ComprehensiveAllRolesSeeder extends Seeder
{
    public function run()
    {
        echo "🚀 Starting comprehensive all-roles seeder...\n\n";
        
        // Get current academic year and classes
        $currentTahunAjaran = TahunAjaran::where('is_active', true)->first();
        $classes = Kelas::where('tahun_ajaran', '2024/2025')->get();
        
        if ($classes->isEmpty()) {
            echo "❌ No classes found! Please run KelasSeeder first.\n";
            return;
        }
        
        // Get categories and achievement levels for prestasi
        $kategoriPrestasi = KategoriPrestasi::limit(10)->get();
        $tingkatPenghargaan = TingkatPenghargaan::limit(5)->get();
        $ekstrakurikuler = Ekstrakurikuler::limit(10)->get();
        
        // Clear existing data first
        echo "🔄 Clearing existing data...\n";
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Clear in proper order
        \Illuminate\Support\Facades\DB::table('prestasi_siswa')->delete();
        \Illuminate\Support\Facades\DB::table('siswa_ekskul')->delete();
        \Illuminate\Support\Facades\DB::table('users')->delete();
        \Illuminate\Support\Facades\DB::table('siswa')->delete();
        
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        echo "✨ Creating all role users and data...\n\n";
        
        // 1. CREATE ADMIN ACCOUNTS
        echo "👑 Creating Admin accounts...\n";
        $adminData = [
            [
                'nama' => 'Super Administrator',
                'username' => 'admin',
                'email' => 'admin@sekolah.sch.id',
                'role' => 'admin'
            ],
            [
                'nama' => 'Admin IT System',
                'username' => 'admin_it',
                'email' => 'admin.it@sekolah.sch.id',
                'role' => 'admin'
            ]
        ];
        
        foreach ($adminData as $admin) {
            User::create([
                'nama' => $admin['nama'],
                'username' => $admin['username'],
                'email' => $admin['email'],
                'password' => Hash::make('admin123'),
                'role' => $admin['role'],
                'status' => 'active',
            ]);
            echo "   ✓ Created admin: {$admin['nama']}\n";
        }
        
        // 2. CREATE KEPALA SEKOLAH ACCOUNT
        echo "\n🎓 Creating Kepala Sekolah account...\n";
        User::create([
            'nama' => 'Dr. H. Bambang Sutrisno, M.Pd',
            'username' => 'kepsek',
            'email' => 'kepala.sekolah@sekolah.sch.id',
            'password' => Hash::make('kepsek123'),
            'role' => 'kepala_sekolah',
            'status' => 'active',
        ]);
        echo "   ✓ Created kepala sekolah: Dr. H. Bambang Sutrisno, M.Pd\n";
        
        // 3. CREATE GURU ACCOUNTS
        echo "\n👨‍🏫 Creating Guru accounts...\n";
        $guruData = [
            [
                'nama' => 'Dra. Siti Nurhaliza, M.Pd',
                'username' => 'guru_siti',
                'email' => 'siti.nurhaliza@sekolah.sch.id',
                'mata_pelajaran' => 'Matematika',
                'kelas_wali' => 'XII IPA 1'
            ],
            [
                'nama' => 'Ir. Ahmad Rahman, M.T',
                'username' => 'guru_ahmad',
                'email' => 'ahmad.rahman@sekolah.sch.id',
                'mata_pelajaran' => 'Fisika',
                'kelas_wali' => 'XII IPA 2'
            ],
            [
                'nama' => 'Dr. Indira Kencana, S.Si, M.Si',
                'username' => 'guru_indira',
                'email' => 'indira.kencana@sekolah.sch.id',
                'mata_pelajaran' => 'Kimia',
                'kelas_wali' => 'XI IPA 1'
            ],
            [
                'nama' => 'Drs. Muhammad Fajar, M.Pd',
                'username' => 'guru_fajar',
                'email' => 'muhammad.fajar@sekolah.sch.id',
                'mata_pelajaran' => 'Sejarah',
                'kelas_wali' => 'XI IPS 1'
            ],
            [
                'nama' => 'S.Pd. Dewi Kusumawati, M.Pd',
                'username' => 'guru_dewi',
                'email' => 'dewi.kusumawati@sekolah.sch.id',
                'mata_pelajaran' => 'Bahasa Indonesia',
                'kelas_wali' => 'X BAHASA 1'
            ],
            [
                'nama' => 'Drs. Arif Wijaya, M.Pd',
                'username' => 'guru_arif',
                'email' => 'arif.wijaya@sekolah.sch.id',
                'mata_pelajaran' => 'Bahasa Inggris',
                'kelas_wali' => null
            ],
            [
                'nama' => 'S.Pd. Putri Maharani, M.Pd',
                'username' => 'guru_putri',
                'email' => 'putri.maharani@sekolah.sch.id',
                'mata_pelajaran' => 'Biologi',
                'kelas_wali' => null
            ]
        ];
        
        foreach ($guruData as $guru) {
            $waliKelasId = null;
            if ($guru['kelas_wali']) {
                $kelas = $classes->where('nama_kelas', $guru['kelas_wali'])->first();
                if ($kelas) {
                    $waliKelasId = $kelas->id;
                    // Update kelas with wali kelas
                    $kelas->update(['id_wali_kelas' => null]); // Will be updated after user creation
                }
            }
            
            $guruUser = User::create([
                'nama' => $guru['nama'],
                'username' => $guru['username'],
                'email' => $guru['email'],
                'password' => Hash::make('guru123'),
                'role' => 'guru',
                'status' => 'active',
            ]);
            
            // Update kelas with wali kelas ID
            if ($waliKelasId && $kelas) {
                $kelas->update(['id_wali_kelas' => $guruUser->id]);
            }
            
            echo "   ✓ Created guru: {$guru['nama']} - {$guru['mata_pelajaran']}" . 
                 ($guru['kelas_wali'] ? " (Wali Kelas {$guru['kelas_wali']})" : "") . "\n";
        }
        
        // 4. CREATE COMPREHENSIVE STUDENT AND PARENT DATA
        echo "\n👨‍👩‍👧‍👦 Creating Students and Parents...\n";
        $studentsData = [
            [
                'nisn' => '2024001001',
                'nama' => 'Ahmad Rizky Pratama',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '2006-03-15',
                'tempat_lahir' => 'Jakarta',
                'alamat' => 'Jl. Sudirman No. 45, Jakarta Pusat',
                'tahun_masuk' => 2022,
                'kelas_preference' => 'XII IPA 1',
                'prestasi_count' => 4,
                'ekskul_ids' => [1, 2],
                'wali_data' => [
                    'nama' => 'H. Budi Pratama, S.E',
                    'username' => 'wali_ahmad',
                    'email' => 'budi.pratama@gmail.com',
                    'pekerjaan' => 'Pengusaha'
                ]
            ],
            [
                'nisn' => '2024001002',
                'nama' => 'Siti Nurhaliza Putri',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '2006-07-22',
                'tempat_lahir' => 'Bandung',
                'alamat' => 'Jl. Asia Afrika No. 12, Bandung',
                'tahun_masuk' => 2022,
                'kelas_preference' => 'XII IPA 1',
                'prestasi_count' => 3,
                'ekskul_ids' => [3, 4],
                'wali_data' => [
                    'nama' => 'Drs. Muhammad Yusuf',
                    'username' => 'wali_siti',
                    'email' => 'muhammad.yusuf@yahoo.com',
                    'pekerjaan' => 'Guru SMP'
                ]
            ],
            [
                'nisn' => '2024001003',
                'nama' => 'Muhammad Fajar Sidiq',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '2006-11-08',
                'tempat_lahir' => 'Surabaya',
                'alamat' => 'Jl. Pemuda No. 78, Surabaya',
                'tahun_masuk' => 2022,
                'kelas_preference' => 'XII IPA 2',
                'prestasi_count' => 5,
                'ekskul_ids' => [1, 5],
                'wali_data' => [
                    'nama' => 'Hj. Fatimah Zahra, S.Pd',
                    'username' => 'wali_fajar',
                    'email' => 'fatimah.zahra@hotmail.com',
                    'pekerjaan' => 'Kepala Sekolah SD'
                ]
            ],
            [
                'nisn' => '2024001004',
                'nama' => 'Dewi Sartika Maharani',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '2007-01-30',
                'tempat_lahir' => 'Yogyakarta',
                'alamat' => 'Jl. Malioboro No. 156, Yogyakarta',
                'tahun_masuk' => 2023,
                'kelas_preference' => 'XI IPA 1',
                'prestasi_count' => 3,
                'ekskul_ids' => [2, 6],
                'wali_data' => [
                    'nama' => 'Dr. Bambang Sutrisno, M.Pd',
                    'username' => 'wali_dewi',
                    'email' => 'bambang.sutrisno@univ.ac.id',
                    'pekerjaan' => 'Dosen Universitas'
                ]
            ],
            [
                'nisn' => '2024001005',
                'nama' => 'Arif Rahman Hakim',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '2007-05-17',
                'tempat_lahir' => 'Medan',
                'alamat' => 'Jl. Gatot Subroto No. 89, Medan',
                'tahun_masuk' => 2023,
                'kelas_preference' => 'XI IPS 1',
                'prestasi_count' => 4,
                'ekskul_ids' => [3, 7],
                'wali_data' => [
                    'nama' => 'Ir. Suharto Wijaya, M.T',
                    'username' => 'wali_arif',
                    'email' => 'suharto.wijaya@company.com',
                    'pekerjaan' => 'Manager Teknik'
                ]
            ],
            [
                'nisn' => '2024001006',
                'nama' => 'Putri Melati Kusuma',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '2007-09-03',
                'tempat_lahir' => 'Semarang',
                'alamat' => 'Jl. Pandanaran No. 234, Semarang',
                'tahun_masuk' => 2023,
                'kelas_preference' => 'XI IPS 1',
                'prestasi_count' => 2,
                'ekskul_ids' => [4, 8],
                'wali_data' => [
                    'nama' => 'Dra. Sri Wahyuni, M.M',
                    'username' => 'wali_putri',
                    'email' => 'sri.wahyuni@bank.co.id',
                    'pekerjaan' => 'Manager Bank'
                ]
            ],
            [
                'nisn' => '2024001007',
                'nama' => 'Ryan Aditya Pratama',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '2008-02-14',
                'tempat_lahir' => 'Malang',
                'alamat' => 'Jl. Ijen Boulevard No. 67, Malang',
                'tahun_masuk' => 2024,
                'kelas_preference' => 'X IPA 1',
                'prestasi_count' => 2,
                'ekskul_ids' => [1, 9],
                'wali_data' => [
                    'nama' => 'H. Agus Salim, S.E, M.M',
                    'username' => 'wali_ryan',
                    'email' => 'agus.salim@business.com',
                    'pekerjaan' => 'Direktur Perusahaan'
                ]
            ],
            [
                'nisn' => '2024001008',
                'nama' => 'Indira Kenzo Putri',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '2008-06-25',
                'tempat_lahir' => 'Denpasar',
                'alamat' => 'Jl. Gajah Mada No. 123, Denpasar',
                'tahun_masuk' => 2024,
                'kelas_preference' => 'X IPA 2',
                'prestasi_count' => 2,
                'ekskul_ids' => [2, 10],
                'wali_data' => [
                    'nama' => 'Made Wirawan, S.H, M.H',
                    'username' => 'wali_indira',
                    'email' => 'made.wirawan@law.co.id',
                    'pekerjaan' => 'Pengacara'
                ]
            ],
            [
                'nisn' => '2024001009',
                'nama' => 'Bayu Setiawan Nugroho',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '2008-10-11',
                'tempat_lahir' => 'Solo',
                'alamat' => 'Jl. Slamet Riyadi No. 345, Solo',
                'tahun_masuk' => 2024,
                'kelas_preference' => 'X IPS 1',
                'prestasi_count' => 3,
                'ekskul_ids' => [5, 6],
                'wali_data' => [
                    'nama' => 'Drs. Joko Susilo, M.Si',
                    'username' => 'wali_bayu',
                    'email' => 'joko.susilo@pemda.go.id',
                    'pekerjaan' => 'PNS Dinas Pendidikan'
                ]
            ],
            [
                'nisn' => '2024001010',
                'nama' => 'Aisha Syakira Rahman',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '2008-12-28',
                'tempat_lahir' => 'Makassar',
                'alamat' => 'Jl. Pettarani No. 567, Makassar',
                'tahun_masuk' => 2024,
                'kelas_preference' => 'X BAHASA 1',
                'prestasi_count' => 4,
                'ekskul_ids' => [7, 8],
                'wali_data' => [
                    'nama' => 'Prof. Dr. Abdul Rahman, M.A',
                    'username' => 'wali_aisha',
                    'email' => 'abdul.rahman@unhas.ac.id',
                    'pekerjaan' => 'Profesor Universitas'
                ]
            ],
            // Additional students for different classes
            [
                'nisn' => '2024001011',
                'nama' => 'Rizki Dwi Saputra',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '2007-04-20',
                'tempat_lahir' => 'Palembang',
                'alamat' => 'Jl. Sudirman No. 789, Palembang',
                'tahun_masuk' => 2023,
                'kelas_preference' => 'XI IPA 2',
                'prestasi_count' => 2,
                'ekskul_ids' => [1, 3],
                'wali_data' => [
                    'nama' => 'Drs. Heri Saputra',
                    'username' => 'wali_rizki',
                    'email' => 'heri.saputra@email.com',
                    'pekerjaan' => 'Wiraswasta'
                ]
            ],
            [
                'nisn' => '2024001012',
                'nama' => 'Nadia Putri Anggraini',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '2007-08-15',
                'tempat_lahir' => 'Padang',
                'alamat' => 'Jl. Ahmad Yani No. 321, Padang',
                'tahun_masuk' => 2023,
                'kelas_preference' => 'XI IPS 2',
                'prestasi_count' => 3,
                'ekskul_ids' => [4, 9],
                'wali_data' => [
                    'nama' => 'Hj. Rina Anggraini, S.Pd',
                    'username' => 'wali_nadia',
                    'email' => 'rina.anggraini@email.com',
                    'pekerjaan' => 'Guru'
                ]
            ]
        ];
        
        foreach ($studentsData as $index => $studentData) {
            $studentNumber = $index + 1;
            echo "   👤 Creating student {$studentNumber}/12: {$studentData['nama']}\n";
            
            // Create parent/wali user account
            $waliUser = User::create([
                'nama' => $studentData['wali_data']['nama'],
                'username' => $studentData['wali_data']['username'],
                'email' => $studentData['wali_data']['email'],
                'password' => Hash::make('wali123'),
                'role' => 'wali',
                'status' => 'active',
            ]);
            
            // Find appropriate class
            $targetClass = $classes->where('nama_kelas', $studentData['kelas_preference'])->first();
            if (!$targetClass) {
                $level = explode(' ', $studentData['kelas_preference'])[0];
                $targetClass = $classes->where('nama_kelas', 'like', "{$level}%")->first();
            }
            
            // Create student record
            $siswa = Siswa::create([
                'nisn' => $studentData['nisn'],
                'nama' => $studentData['nama'],
                'jenis_kelamin' => $studentData['jenis_kelamin'],
                'tanggal_lahir' => $studentData['tanggal_lahir'],
                'tempat_lahir' => $studentData['tempat_lahir'],
                'id_kelas' => $targetClass->id,
                'alamat' => $studentData['alamat'],
                'tahun_masuk' => $studentData['tahun_masuk'],
                'wali_id' => $waliUser->id,
            ]);
            
            // Create student user account
            User::create([
                'nama' => $studentData['nama'],
                'username' => "siswa{$studentNumber}",
                'email' => "siswa{$studentNumber}@sekolah.test",
                'password' => Hash::make('siswa123'),
                'role' => 'siswa',
                'status' => 'active',
                'siswa_id' => $siswa->id,
            ]);
            
            // Create extracurricular assignments
            if (!empty($studentData['ekskul_ids']) && $ekstrakurikuler->count() > 0) {
                foreach ($studentData['ekskul_ids'] as $ekskulId) {
                    if ($ekskulId <= $ekstrakurikuler->count()) {
                        SiswaEkskul::create([
                            'id_siswa' => $siswa->id,
                            'id_ekskul' => $ekskulId,
                            'tanggal_mulai' => now()->subMonths(rand(1, 12)),
                            'status_keaktifan' => 'aktif',
                            'tahun_ajaran' => '2024/2025'
                        ]);
                    }
                }
            }
            
            // Create achievements/prestasi
            if ($studentData['prestasi_count'] > 0 && $kategoriPrestasi->count() > 0 && $tingkatPenghargaan->count() > 0) {
                $prestasiNames = $this->getRealisticPrestasiNames($studentData['prestasi_count']);
                
                for ($p = 0; $p < $studentData['prestasi_count']; $p++) {
                    PrestasiSiswa::create([
                        'id_siswa' => $siswa->id,
                        'id_kategori_prestasi' => $kategoriPrestasi->random()->id,
                        'id_tingkat_penghargaan' => $tingkatPenghargaan->random()->id,
                        'id_tahun_ajaran' => $currentTahunAjaran ? $currentTahunAjaran->id : null,
                        'nama_prestasi' => $prestasiNames[$p] ?? "Prestasi " . ($p + 1),
                        'penyelenggara' => $this->getRandomOrganizer(),
                        'tanggal_prestasi' => now()->subMonths(rand(1, 18)),
                        'keterangan' => 'Prestasi yang diraih dengan usaha keras dan dedikasi tinggi',
                        'status' => 'diterima',
                        'rata_rata_nilai' => rand(85, 95),
                    ]);
                }
            }
        }
        
        // Summary
        $totalUsers = User::count();
        $totalSiswa = Siswa::count();
        $totalPrestasi = PrestasiSiswa::count();
        $totalEkskul = SiswaEkskul::count();
        
        echo "\n🎉 COMPREHENSIVE ALL-ROLES SEEDER COMPLETED!\n";
        echo str_repeat("=", 60) . "\n";
        echo "📊 Summary:\n";
        echo "   👑 Admins: " . User::where('role', 'admin')->count() . "\n";
        echo "   🎓 Kepala Sekolah: " . User::where('role', 'kepala_sekolah')->count() . "\n";
        echo "   👨‍🏫 Guru: " . User::where('role', 'guru')->count() . "\n";
        echo "   👥 Students: {$totalSiswa}\n";
        echo "   👨‍👩‍👧‍👦 Parents: " . User::where('role', 'wali')->count() . "\n";
        echo "   🔐 Total User accounts: {$totalUsers}\n";
        echo "   🏆 Achievement records: {$totalPrestasi}\n";
        echo "   🎯 Extracurricular assignments: {$totalEkskul}\n";
        echo "\n🎓 Login credentials for all roles:\n";
        echo "   👑 Admin: admin/admin123, admin_it/admin123\n";
        echo "   🎓 Kepala Sekolah: kepsek/kepsek123\n";
        echo "   👨‍🏫 Guru: guru_siti/guru123, guru_ahmad/guru123, etc.\n";
        echo "   📝 Students: siswa1-siswa12 / siswa123\n";
        echo "   👨‍👩‍👧‍👦 Parents: wali_ahmad, wali_siti, etc. / wali123\n";
        echo "\n✅ All roles have realistic accounts and complete data ready for testing!\n";
    }
    
    private function getRealisticPrestasiNames($count)
    {
        $prestasiTemplates = [
            'Juara 1 Olimpiade Matematika Tingkat Kota',
            'Juara 2 Kompetisi Sains Nasional',
            'Juara 3 Lomba Karya Tulis Ilmiah',
            'Juara 1 Turnamen Debat Bahasa Indonesia',
            'Juara 2 Festival Seni dan Budaya',
            'Juara 1 Kompetisi Robotika Regional',
            'Juara 3 Lomba Fotografi Tingkat Provinsi',
            'Juara 1 Turnamen Olahraga Basket',
            'Juara 2 Kompetisi Desain Grafis',
            'Juara 1 Lomba Pidato Bahasa Inggris',
            'Juara 3 Festival Musik dan Tari',
            'Juara 2 Olimpiade Fisika Tingkat Nasional',
            'Juara 1 Lomba Cerpen Tingkat SMA',
            'Juara 3 Kompetisi Catur Antar Sekolah',
            'Juara 2 Lomba Melukis Tingkat Daerah',
            'Juara 1 Turnamen Futsal Pelajar'
        ];
        
        shuffle($prestasiTemplates);
        return array_slice($prestasiTemplates, 0, $count);
    }
    
    private function getRandomOrganizer()
    {
        $organizers = [
            'Dinas Pendidikan Kota Jakarta',
            'Kementerian Pendidikan dan Kebudayaan RI',
            'Universitas Indonesia',
            'Institut Teknologi Bandung',
            'Yayasan Pendidikan Indonesia',
            'OSIS SMA Negeri 1 Jakarta',
            'Dinas Pemuda dan Olahraga',
            'Komunitas Sains Indonesia',
            'Persatuan Guru Republik Indonesia',
            'Lembaga Seni Budaya Nusantara',
            'Olimpiade Sains Nasional (OSN)',
            'Festival Lomba Seni Siswa Nasional (FLS2N)'
        ];
        
        return $organizers[array_rand($organizers)];
    }
}