<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\KategoriPrestasi;

class KategoriPrestasiSeeder extends Seeder
{
    public function run()
    {
        $kategoris = [
            [
                'nama_kategori' => 'Prestasi Akademik',
                'jenis_prestasi' => 'akademik',
                'tingkat_kompetisi' => 'sekolah',
                'bidang_prestasi' => 'akademik',
                'deskripsi' => 'Prestasi di bidang akademik seperti olimpiade sains, matematika, dll',
                'is_active' => true
            ],
            [
                'nama_kategori' => 'Olahraga',
                'jenis_prestasi' => 'non_akademik',
                'tingkat_kompetisi' => 'kabupaten',
                'bidang_prestasi' => 'olahraga',
                'deskripsi' => 'Prestasi di bidang olahraga seperti sepak bola, basket, voli, dll',
                'is_active' => true
            ],
            [
                'nama_kategori' => 'Seni dan Budaya',
                'jenis_prestasi' => 'non_akademik',
                'tingkat_kompetisi' => 'provinsi',
                'bidang_prestasi' => 'seni',
                'deskripsi' => 'Prestasi di bidang seni seperti musik, tari, lukis, teater, dll',
                'is_active' => true
            ],
            [
                'nama_kategori' => 'Lomba Umum',
                'jenis_prestasi' => 'non_akademik',
                'tingkat_kompetisi' => 'nasional',
                'bidang_prestasi' => 'lomba',
                'deskripsi' => 'Berbagai lomba seperti debat, pidato, karya tulis, dll',
                'is_active' => true
            ],
            [
                'nama_kategori' => 'Organisasi',
                'jenis_prestasi' => 'non_akademik',
                'tingkat_kompetisi' => 'sekolah',
                'bidang_prestasi' => 'organisasi',
                'deskripsi' => 'Prestasi dalam keorganisasian seperti OSIS, MPK, ekstrakurikuler',
                'is_active' => true
            ],
            [
                'nama_kategori' => 'Keagamaan',
                'jenis_prestasi' => 'non_akademik',
                'tingkat_kompetisi' => 'kabupaten',
                'bidang_prestasi' => 'keagamaan',
                'deskripsi' => 'Prestasi di bidang keagamaan seperti tartil, hafalan, dll',
                'is_active' => true
            ]
        ];

        foreach ($kategoris as $kategori) {
            KategoriPrestasi::create($kategori);
        }
    }
}