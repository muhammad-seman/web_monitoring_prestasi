<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KenaikanKelas;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;

class KenaikanKelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get academic years and classes
        $tahunAjaran = TahunAjaran::where('nama_tahun_ajaran', '2023/2024')->first();
        if (!$tahunAjaran) {
            echo "❌ Academic year 2023/2024 not found! Please run TahunAjaranSeeder first.\n";
            return;
        }
        
        // Get XI classes as source (kelas_asal)
        $kelasXI = Kelas::where('nama_kelas', 'LIKE', 'XI%')->where('tahun_ajaran', '2024/2025')->get();
        
        // Get XII classes as destination (kelas_tujuan)
        $kelasXII = Kelas::where('nama_kelas', 'LIKE', 'XII%')->where('tahun_ajaran', '2024/2025')->get();
        
        if ($kelasXI->isEmpty() || $kelasXII->isEmpty()) {
            echo "❌ XI or XII classes not found! Please run KelasSeeder first.\n";
            return;
        }
        
        $kenaikanCount = 0;
        
        // For each XI class, get some students and create kenaikan kelas records
        foreach ($kelasXI as $kelasAsal) {
            // Get students from this XI class (take some of them, not all)
            $siswaList = Siswa::where('id_kelas', $kelasAsal->id)->take(20)->get();
            
            // Find corresponding XII class (same jurusan) - extract from nama_kelas
            $jurusanAsal = $this->extractJurusanFromNama($kelasAsal->nama_kelas);
            $kelasTujuan = $kelasXII->first(function($kelas) use ($jurusanAsal) {
                return str_contains($kelas->nama_kelas, $jurusanAsal);
            });
            
            if (!$kelasTujuan || $siswaList->isEmpty()) {
                continue;
            }
            
            foreach ($siswaList as $siswa) {
                // Create kenaikan kelas record using factory
                KenaikanKelas::factory()->create([
                    'id_siswa' => $siswa->id,
                    'kelas_asal' => $kelasAsal->id,
                    'kelas_tujuan' => $kelasTujuan->id,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'created_by' => 1, // Admin user
                ]);
                
                $kenaikanCount++;
            }
        }
        
        echo "✅ Created {$kenaikanCount} class progression records (XI to XII)\n";
    }
    
    private function extractJurusanFromNama($namaKelas)
    {
        // Extract jurusan from class name like "XI IPA 1" -> "IPA"
        $parts = explode(' ', $namaKelas);
        return isset($parts[1]) ? $parts[1] : 'IPA'; // Default to IPA if not found
    }
}
