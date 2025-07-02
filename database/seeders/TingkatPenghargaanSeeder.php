<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\TingkatPenghargaan;

class TingkatPenghargaanSeeder extends Seeder
{
    public function run()
    {
        TingkatPenghargaan::insert([
            ['tingkat' => 'Sekolah',       'created_at' => now(), 'updated_at' => now()],
            ['tingkat' => 'Kabupaten/Kota','created_at' => now(), 'updated_at' => now()],
            ['tingkat' => 'Provinsi',      'created_at' => now(), 'updated_at' => now()],
            ['tingkat' => 'Nasional',      'created_at' => now(), 'updated_at' => now()],
            ['tingkat' => 'Internasional', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}