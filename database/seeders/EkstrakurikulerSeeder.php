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
            ['nama' => 'OSIS', 'pembina' => 'Drs. Ahmad Wijaya', 'keterangan' => 'Organisasi Siswa Intra Sekolah'],
            ['nama' => 'Pramuka', 'pembina' => 'Siti Nurhaliza, S.Pd', 'keterangan' => 'Praja Muda Karana - wajib untuk kelas X'],
            ['nama' => 'PMR', 'pembina' => 'dr. Budi Santoso', 'keterangan' => 'Palang Merah Remaja'],
        ];
        
        foreach ($specificExtracurriculars as $ekskul) {
            Ekstrakurikuler::firstOrCreate(
                ['nama' => $ekskul['nama']],
                $ekskul
            );
        }
        
        $totalEkskul = Ekstrakurikuler::count();
        echo "✅ Created {$totalEkskul} extracurricular activities\n";
    }
}