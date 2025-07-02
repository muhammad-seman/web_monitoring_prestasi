<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\DokumenPrestasi;

class DokumenPrestasiSeeder extends Seeder
{
    public function run()
    {
        DokumenPrestasi::insert([
            [
                'id_prestasi_siswa' => 1,
                'nama_file'         => 'sertifikat_olimpiade.pdf',
                'path_file'         => 'uploads/sertifikat/sertifikat_olimpiade.pdf',
                'uploaded_by'       => 3, // user siswa
                'uploaded_at'       => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'id_prestasi_siswa' => 2,
                'nama_file'         => 'sertifikat_futsal.pdf',
                'path_file'         => 'uploads/sertifikat/sertifikat_futsal.pdf',
                'uploaded_by'       => 4,
                'uploaded_at'       => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]
        ]);
    }
}