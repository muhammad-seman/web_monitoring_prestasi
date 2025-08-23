<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TahunAjaran;

class TahunAjaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tahunAjaranData = [
            [
                'nama_tahun_ajaran' => '2022/2023',
                'tanggal_mulai' => '2022-07-01',
                'tanggal_selesai' => '2023-06-30',
                'semester_aktif' => 'genap',
                'is_active' => false,
                'keterangan' => 'Tahun ajaran 2022/2023'
            ],
            [
                'nama_tahun_ajaran' => '2023/2024',
                'tanggal_mulai' => '2023-07-01',
                'tanggal_selesai' => '2024-06-30',
                'semester_aktif' => 'genap',
                'is_active' => false,
                'keterangan' => 'Tahun ajaran 2023/2024'
            ],
            [
                'nama_tahun_ajaran' => '2024/2025',
                'tanggal_mulai' => '2024-07-01',
                'tanggal_selesai' => '2025-06-30',
                'semester_aktif' => 'ganjil',
                'is_active' => true,
                'keterangan' => 'Tahun ajaran aktif saat ini'
            ],
            [
                'nama_tahun_ajaran' => '2025/2026',
                'tanggal_mulai' => '2025-07-01',
                'tanggal_selesai' => '2026-06-30',
                'semester_aktif' => 'ganjil',
                'is_active' => false,
                'keterangan' => 'Tahun ajaran mendatang'
            ]
        ];

        foreach ($tahunAjaranData as $data) {
            TahunAjaran::firstOrCreate(
                ['nama_tahun_ajaran' => $data['nama_tahun_ajaran']],
                $data
            );
        }
        
        echo "✅ Created academic years data\n";
    }
}
