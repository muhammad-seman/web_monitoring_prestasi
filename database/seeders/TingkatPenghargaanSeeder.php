<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TingkatPenghargaan;

class TingkatPenghargaanSeeder extends Seeder
{
    public function run()
    {
        echo "🥇 Creating Award Levels...\n";
        
        $tingkatData = [
            ['tingkat' => 'Juara 1'],
            ['tingkat' => 'Juara 2'],
            ['tingkat' => 'Juara 3'],
            ['tingkat' => 'Juara Harapan 1'],
            ['tingkat' => 'Juara Harapan 2'],
            ['tingkat' => 'Juara Harapan 3'],
            ['tingkat' => 'Finalis'],
            ['tingkat' => 'Semi Finalis'],
            ['tingkat' => 'Peserta Terbaik'],
            ['tingkat' => 'Partisipan'],
            ['tingkat' => 'Medali Emas'],
            ['tingkat' => 'Medali Perak'],
            ['tingkat' => 'Medali Perunggu'],
            ['tingkat' => 'Best Player'],
            ['tingkat' => 'Best Performer'],
            ['tingkat' => 'Rising Star'],
            ['tingkat' => 'Sekolah'],
            ['tingkat' => 'Kabupaten'],
            ['tingkat' => 'Provinsi'],
            ['tingkat' => 'Nasional'],
            ['tingkat' => 'Internasional']
        ];

        foreach ($tingkatData as $tingkat) {
            TingkatPenghargaan::create($tingkat);
            echo "   ✓ 🏅 Created award level: {$tingkat['tingkat']}\n";
        }
        
        echo "🥇 Award Levels seeding completed!\n\n";
    }
}