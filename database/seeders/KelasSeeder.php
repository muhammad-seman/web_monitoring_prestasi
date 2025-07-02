<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Kelas;

class KelasSeeder extends Seeder
{
    public function run()
    {
        Kelas::create([
            'nama_kelas'    => 'X IPA 1',
            // 'id_wali_kelas' => 2, // Asumsi id 2 user guru
            'tahun_ajaran'  => '2024/2025',
        ]);
        Kelas::create([
            'nama_kelas'    => 'X IPS 1',
            // 'id_wali_kelas' => 3, // Asumsi id 3 user guru
            'tahun_ajaran'  => '2024/2025',
        ]);
    }
}