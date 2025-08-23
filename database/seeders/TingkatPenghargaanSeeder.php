<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\TingkatPenghargaan;

class TingkatPenghargaanSeeder extends Seeder
{
    public function run()
    {
        // Create comprehensive award levels data
        $tingkatPenghargaan = [
            ['nama_tingkat' => 'Juara 1', 'poin' => 100],
            ['nama_tingkat' => 'Juara 2', 'poin' => 85],
            ['nama_tingkat' => 'Juara 3', 'poin' => 70],
            ['nama_tingkat' => 'Juara Harapan 1', 'poin' => 60],
            ['nama_tingkat' => 'Juara Harapan 2', 'poin' => 50],
            ['nama_tingkat' => 'Peserta Terbaik', 'poin' => 40],
            ['nama_tingkat' => 'Peserta', 'poin' => 25],
            ['nama_tingkat' => 'Medali Emas', 'poin' => 100],
            ['nama_tingkat' => 'Medali Perak', 'poin' => 85],
            ['nama_tingkat' => 'Medali Perunggu', 'poin' => 70],
        ];
        
        foreach ($tingkatPenghargaan as $tingkat) {
            TingkatPenghargaan::create($tingkat);
        }
        
        echo "✅ Created comprehensive award levels data\n";
    }
}