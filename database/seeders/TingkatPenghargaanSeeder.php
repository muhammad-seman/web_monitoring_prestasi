<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\TingkatPenghargaan;

class TingkatPenghargaanSeeder extends Seeder
{
    public function run()
    {
        // Create comprehensive award levels data (using correct column name 'tingkat')
        $tingkatPenghargaan = [
            ['tingkat' => 'Juara 1'],
            ['tingkat' => 'Juara 2'],
            ['tingkat' => 'Juara 3'],
            ['tingkat' => 'Juara Harapan 1'],
            ['tingkat' => 'Juara Harapan 2'],
            ['tingkat' => 'Peserta Terbaik'],
            ['tingkat' => 'Peserta'],
            ['tingkat' => 'Medali Emas'],
            ['tingkat' => 'Medali Perak'],
            ['tingkat' => 'Medali Perunggu'],
        ];
        
        foreach ($tingkatPenghargaan as $tingkat) {
            TingkatPenghargaan::firstOrCreate(
                ['tingkat' => $tingkat['tingkat']],
                $tingkat
            );
        }
        
        echo "✅ Created comprehensive award levels data\n";
    }
}