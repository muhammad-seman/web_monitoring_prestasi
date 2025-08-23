<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Kelas;

class KelasSeeder extends Seeder
{
    public function run()
    {
        // Create comprehensive class data for all grades and programs
        $tingkats = ['X', 'XI', 'XII'];
        $jurusans = ['IPA', 'IPS', 'BAHASA'];
        $nomors = [1, 2, 3];
        
        foreach ($tingkats as $tingkat) {
            foreach ($jurusans as $jurusan) {
                // For IPA and IPS, create 3 classes each, for BAHASA only 1
                $max_nomor = ($jurusan === 'BAHASA') ? 1 : 3;
                for ($nomor = 1; $nomor <= $max_nomor; $nomor++) {
                    Kelas::create([
                        'nama_kelas' => "{$tingkat} {$jurusan} {$nomor}",
                        'tahun_ajaran' => '2024/2025',
                    ]);
                }
            }
        }
        
        // Also create some classes for previous years for testing multi-year analytics
        foreach (['2022/2023', '2023/2024'] as $tahunAjaran) {
            foreach (['X', 'XI', 'XII'] as $tingkat) {
                foreach (['IPA', 'IPS'] as $jurusan) {
                    for ($nomor = 1; $nomor <= 2; $nomor++) {
                        Kelas::create([
                            'nama_kelas' => "{$tingkat} {$jurusan} {$nomor}",
                            'tahun_ajaran' => $tahunAjaran,
                        ]);
                    }
                }
            }
        }
        
        echo "✅ Created comprehensive class data for multiple academic years\n";
    }
}