<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\PrestasiSiswa;

class PrestasiSiswaSeeder extends Seeder
{
    public function run()
    {
        PrestasiSiswa::insert([
            [
                'id_siswa'              => 1,
                'id_kategori_prestasi'  => 1, // Akademik
                'id_tingkat_penghargaan'=> 3, // Provinsi
                'id_ekskul'             => null, // <--- TAMBAHKAN INI!
                'nama_prestasi'         => 'Juara 1 Olimpiade Matematika',
                'penyelenggara'         => 'Dinas Pendidikan',
                'tanggal_prestasi'      => '2024-03-15',
                'keterangan'            => 'Olimpiade tingkat provinsi',
                'dokumen_url'           => 'uploads/sertifikat/juara1_olimpiade.pdf',
                'status'                => 'diterima',
                'rata_rata_nilai'       => 93.50,
                'created_by'            => 3, // user siswa
                'validated_by'          => 2, // user guru
                'validated_at'          => now(),
                'alasan_tolak'          => null,
                'created_at'            => now(),
                'updated_at'            => now(),
            ],
            [
                'id_siswa'              => 2,
                'id_kategori_prestasi'  => 2, // Olahraga
                'id_tingkat_penghargaan'=> 2, // Kab/Kota
                'id_ekskul'             => 3, // Futsal
                'nama_prestasi'         => 'Juara 2 Turnamen Futsal',
                'penyelenggara'         => 'Pemda',
                'tanggal_prestasi'      => '2024-01-20',
                'keterangan'            => 'Tingkat kabupaten',
                'dokumen_url'           => 'uploads/sertifikat/juara2_futsal.pdf',
                'status'                => 'diterima',
                'rata_rata_nilai'       => null,
                'created_by'            => 4,
                'validated_by'          => 2,
                'validated_at'          => now(),
                'alasan_tolak'          => null,
                'created_at'            => now(),
                'updated_at'            => now(),
            ]
        ]);
    }
}