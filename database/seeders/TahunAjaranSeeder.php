<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TahunAjaran;

class TahunAjaranSeeder extends Seeder
{
    public function run()
    {
        echo "📅 Creating Academic Years...\n";
        
        $tahunAjarans = [
            [
                'nama_tahun_ajaran' => '2022/2023',
                'tanggal_mulai' => '2022-07-15',
                'tanggal_selesai' => '2023-06-30',
                'semester_aktif' => 'genap',
                'is_active' => false,
            ],
            [
                'nama_tahun_ajaran' => '2023/2024',
                'tanggal_mulai' => '2023-07-15',
                'tanggal_selesai' => '2024-06-30',
                'semester_aktif' => 'genap',
                'is_active' => false,
            ],
            [
                'nama_tahun_ajaran' => '2024/2025',
                'tanggal_mulai' => '2024-07-15',
                'tanggal_selesai' => '2025-06-30',
                'semester_aktif' => 'ganjil',
                'is_active' => true,
            ],
            [
                'nama_tahun_ajaran' => '2025/2026',
                'tanggal_mulai' => '2025-07-15',
                'tanggal_selesai' => '2026-06-30',
                'semester_aktif' => 'ganjil',
                'is_active' => false,
            ]
        ];

        foreach ($tahunAjarans as $tahun) {
            TahunAjaran::create($tahun);
            echo "   ✓ Created academic year: {$tahun['nama_tahun_ajaran']}" . 
                 ($tahun['is_active'] ? ' (ACTIVE)' : '') . "\n";
        }
        
        echo "📅 Academic Years seeding completed!\n\n";
    }
}