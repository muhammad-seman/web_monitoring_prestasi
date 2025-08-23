<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Ekstrakurikuler;

class EkstrakurikulerSeeder extends Seeder
{
    public function run()
    {
        // Use factory to create comprehensive extracurricular activities data
        Ekstrakurikuler::factory(18)->create();
        
        // Also create some specific important extracurriculars
        $specificExtracurriculars = [
            ['nama_ekstrakurikuler' => 'OSIS', 'pembina' => 'Drs. Ahmad Wijaya', 'deskripsi' => 'Organisasi Siswa Intra Sekolah'],
            ['nama_ekstrakurikuler' => 'Pramuka', 'pembina' => 'Siti Nurhaliza, S.Pd', 'deskripsi' => 'Praja Muda Karana - wajib untuk kelas X'],
            ['nama_ekstrakurikuler' => 'PMR', 'pembina' => 'dr. Budi Santoso', 'deskripsi' => 'Palang Merah Remaja'],
        ];
        
        foreach ($specificExtracurriculars as $ekskul) {
            Ekstrakurikuler::firstOrCreate(
                ['nama_ekstrakurikuler' => $ekskul['nama_ekstrakurikuler']],
                $ekskul
            );
        }
        
        $totalEkskul = Ekstrakurikuler::count();
        echo "✅ Created {$totalEkskul} extracurricular activities\n";
    }
}