<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriPrestasi;

class KategoriPrestasiSeeder extends Seeder
{
    public function run()
    {
        echo "🏆 Creating Achievement Categories...\n";
        
        $kategoriData = [
            // AKADEMIK CATEGORIES
            [
                'nama_kategori' => 'Olimpiade Matematika',
                'jenis_prestasi' => 'akademik',
                'tingkat_kompetisi' => 'nasional',
                'bidang_prestasi' => 'matematika',
                'deskripsi' => 'Kompetisi matematika tingkat nasional dan internasional',
                'is_active' => true
            ],
            [
                'nama_kategori' => 'Olimpiade Fisika',
                'jenis_prestasi' => 'akademik',
                'tingkat_kompetisi' => 'nasional',
                'bidang_prestasi' => 'fisika',
                'deskripsi' => 'Kompetisi fisika tingkat nasional dan internasional',
                'is_active' => true
            ],
            [
                'nama_kategori' => 'Olimpiade Kimia',
                'jenis_prestasi' => 'akademik',
                'tingkat_kompetisi' => 'nasional',
                'bidang_prestasi' => 'kimia',
                'deskripsi' => 'Kompetisi kimia tingkat nasional dan internasional',
                'is_active' => true
            ],
            [
                'nama_kategori' => 'Kompetisi Sains Nasional (KSN)',
                'jenis_prestasi' => 'akademik',
                'tingkat_kompetisi' => 'nasional',
                'bidang_prestasi' => 'sains',
                'deskripsi' => 'Kompetisi Sains Nasional untuk siswa SMA',
                'is_active' => true
            ],
            [
                'nama_kategori' => 'Lomba Karya Tulis Ilmiah',
                'jenis_prestasi' => 'akademik',
                'tingkat_kompetisi' => 'provinsi',
                'bidang_prestasi' => 'penelitian',
                'deskripsi' => 'Kompetisi penulisan karya ilmiah siswa',
                'is_active' => true
            ],
            [
                'nama_kategori' => 'Debat Bahasa Indonesia',
                'jenis_prestasi' => 'akademik',
                'tingkat_kompetisi' => 'kabupaten',
                'bidang_prestasi' => 'bahasa',
                'deskripsi' => 'Kompetisi debat dalam bahasa Indonesia',
                'is_active' => true
            ],
            [
                'nama_kategori' => 'English Speech Contest',
                'jenis_prestasi' => 'akademik',
                'tingkat_kompetisi' => 'provinsi',
                'bidang_prestasi' => 'bahasa',
                'deskripsi' => 'Kompetisi pidato bahasa Inggris',
                'is_active' => true
            ],

            // NON-AKADEMIK CATEGORIES - OLAHRAGA
            [
                'nama_kategori' => 'Turnamen Basket Pelajar',
                'jenis_prestasi' => 'non_akademik',
                'tingkat_kompetisi' => 'kabupaten',
                'bidang_prestasi' => 'olahraga',
                'deskripsi' => 'Kompetisi basket antar sekolah menengah',
                'is_active' => true
            ],
            [
                'nama_kategori' => 'Futsal Championship',
                'jenis_prestasi' => 'non_akademik',
                'tingkat_kompetisi' => 'provinsi',
                'bidang_prestasi' => 'olahraga',
                'deskripsi' => 'Kejuaraan futsal tingkat provinsi',
                'is_active' => true
            ],
            [
                'nama_kategori' => 'Volley Ball Competition',
                'jenis_prestasi' => 'non_akademik',
                'tingkat_kompetisi' => 'kabupaten',
                'bidang_prestasi' => 'olahraga',
                'deskripsi' => 'Kompetisi bola voli siswa SMA',
                'is_active' => true
            ],
            [
                'nama_kategori' => 'Badminton Tournament',
                'jenis_prestasi' => 'non_akademik',
                'tingkat_kompetisi' => 'nasional',
                'bidang_prestasi' => 'olahraga',
                'deskripsi' => 'Turnamen bulutangkis pelajar nasional',
                'is_active' => true
            ],

            // NON-AKADEMIK CATEGORIES - SENI
            [
                'nama_kategori' => 'Festival Seni dan Budaya',
                'jenis_prestasi' => 'non_akademik',
                'tingkat_kompetisi' => 'provinsi',
                'bidang_prestasi' => 'seni',
                'deskripsi' => 'Festival seni dan budaya daerah',
                'is_active' => true
            ],
            [
                'nama_kategori' => 'Lomba Musik Tradisional',
                'jenis_prestasi' => 'non_akademik',
                'tingkat_kompetisi' => 'kabupaten',
                'bidang_prestasi' => 'seni',
                'deskripsi' => 'Kompetisi musik tradisional Indonesia',
                'is_active' => true
            ],
            [
                'nama_kategori' => 'Kompetisi Tari Modern',
                'jenis_prestasi' => 'non_akademik',
                'tingkat_kompetisi' => 'provinsi',
                'bidang_prestasi' => 'seni',
                'deskripsi' => 'Kompetisi tari kontemporer dan modern',
                'is_active' => true
            ],
            [
                'nama_kategori' => 'Lomba Fotografi',
                'jenis_prestasi' => 'non_akademik',
                'tingkat_kompetisi' => 'nasional',
                'bidang_prestasi' => 'seni',
                'deskripsi' => 'Kompetisi fotografi dengan berbagai tema',
                'is_active' => true
            ],
            [
                'nama_kategori' => 'Desain Grafis Competition',
                'jenis_prestasi' => 'non_akademik',
                'tingkat_kompetisi' => 'provinsi',
                'bidang_prestasi' => 'seni',
                'deskripsi' => 'Kompetisi desain grafis dan visual',
                'is_active' => true
            ],

            // NON-AKADEMIK CATEGORIES - ORGANISASI & KEPEMIMPINAN
            [
                'nama_kategori' => 'Leadership Camp',
                'jenis_prestasi' => 'non_akademik',
                'tingkat_kompetisi' => 'provinsi',
                'bidang_prestasi' => 'organisasi',
                'deskripsi' => 'Program pelatihan kepemimpinan siswa',
                'is_active' => true
            ],
            [
                'nama_kategori' => 'Student Council Competition',
                'jenis_prestasi' => 'non_akademik',
                'tingkat_kompetisi' => 'kabupaten',
                'bidang_prestasi' => 'organisasi',
                'deskripsi' => 'Kompetisi program kerja OSIS',
                'is_active' => true
            ],

            // NON-AKADEMIK CATEGORIES - TEKNOLOGI
            [
                'nama_kategori' => 'Robotika Competition',
                'jenis_prestasi' => 'non_akademik',
                'tingkat_kompetisi' => 'nasional',
                'bidang_prestasi' => 'teknologi',
                'deskripsi' => 'Kompetisi robotika dan automasi',
                'is_active' => true
            ],
            [
                'nama_kategori' => 'Programming Contest',
                'jenis_prestasi' => 'non_akademik',
                'tingkat_kompetisi' => 'provinsi',
                'bidang_prestasi' => 'teknologi',
                'deskripsi' => 'Kompetisi pemrograman komputer',
                'is_active' => true
            ],

            // SEKOLAH LEVEL COMPETITIONS
            [
                'nama_kategori' => 'Prestasi Akademik Sekolah',
                'jenis_prestasi' => 'akademik',
                'tingkat_kompetisi' => 'sekolah',
                'bidang_prestasi' => 'akademik',
                'deskripsi' => 'Prestasi akademik tingkat sekolah',
                'is_active' => true
            ],
            [
                'nama_kategori' => 'Kegiatan Ekstrakurikuler Sekolah',
                'jenis_prestasi' => 'non_akademik',
                'tingkat_kompetisi' => 'sekolah',
                'bidang_prestasi' => 'organisasi',
                'deskripsi' => 'Prestasi dalam kegiatan ekstrakurikuler sekolah',
                'is_active' => true
            ]
        ];

        foreach ($kategoriData as $kategori) {
            KategoriPrestasi::create($kategori);
            $indicator = $kategori['jenis_prestasi'] === 'akademik' ? '📚' : '🎯';
            echo "   ✓ {$indicator} Created category: {$kategori['nama_kategori']} ({$kategori['tingkat_kompetisi']})\n";
        }
        
        echo "🏆 Achievement Categories seeding completed!\n\n";
    }
}