<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\SiswaEkskul;

class SiswaEkskulSeeder extends Seeder
{
    public function run()
    {
        SiswaEkskul::insert([
            ['id_siswa' => 1, 'id_ekskul' => 1, 'jabatan' => 'Ketua',   'periode' => '2024/2025', 'created_at' => now(), 'updated_at' => now()],
            ['id_siswa' => 2, 'id_ekskul' => 2, 'jabatan' => 'Anggota', 'periode' => '2024/2025', 'created_at' => now(), 'updated_at' => now()],
            ['id_siswa' => 1, 'id_ekskul' => 3, 'jabatan' => 'Anggota', 'periode' => '2024/2025', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}