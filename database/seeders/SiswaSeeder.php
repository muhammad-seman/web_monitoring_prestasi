<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\Kelas;

class SiswaSeeder extends Seeder
{
    public function run()
    {
        // Get all classes to assign students
        $kelas = Kelas::where('tahun_ajaran', '2024/2025')->get();
        
        if ($kelas->isEmpty()) {
            echo "❌ No classes found! Please run KelasSeeder first.\n";
            return;
        }
        
        // Create students for each class (around 25-30 students per class)
        foreach ($kelas as $kelasItem) {
            $studentCount = rand(25, 30);
            
            // Use factory to create students and assign them to this class
            Siswa::factory($studentCount)->create([
                'id_kelas' => $kelasItem->id,
                'tahun_masuk' => $this->getTahunMasukForTingkat($kelasItem->tingkat),
            ]);
        }
        
        // Also create some students for previous years' classes
        $kelasPrevious = Kelas::whereIn('tahun_ajaran', ['2022/2023', '2023/2024'])->get();
        foreach ($kelasPrevious as $kelasItem) {
            $studentCount = rand(20, 25);
            
            Siswa::factory($studentCount)->create([
                'id_kelas' => $kelasItem->id,
                'tahun_masuk' => $this->getTahunMasukForPreviousYear($kelasItem->tingkat, $kelasItem->tahun_ajaran),
            ]);
        }
        
        $totalStudents = Siswa::count();
        echo "✅ Created {$totalStudents} students across all classes\n";
    }
    
    private function getTahunMasukForTingkat($tingkat)
    {
        return match($tingkat) {
            'X' => 2024,
            'XI' => 2023,
            'XII' => 2022,
            default => 2024
        };
    }
    
    private function getTahunMasukForPreviousYear($tingkat, $tahunAjaran)
    {
        $year = explode('/', $tahunAjaran)[0];
        return match($tingkat) {
            'X' => (int)$year,
            'XI' => (int)$year - 1,
            'XII' => (int)$year - 2,
            default => (int)$year
        };
    }
}