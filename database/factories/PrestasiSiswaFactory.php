<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PrestasiSiswa>
 */
class PrestasiSiswaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $prestasi_names = [
            'Juara 1 Olimpiade Matematika', 'Juara 2 Lomba Fisika', 'Juara 3 Kompetisi Biologi',
            'Juara 1 Lomba Basket', 'Juara 2 Turnamen Voli', 'Juara 3 Kompetisi Futsal',
            'Juara 1 Festival Seni', 'Juara 2 Lomba Musik', 'Juara 3 Kompetisi Tari',
            'Peserta Terbaik Debat Bahasa Inggris', 'Juara Harapan 1 Lomba Essay',
            'Best Speaker English Competition', 'Juara 1 Robotika Competition'
        ];
        
        return [
            'id_siswa' => null, // Will be set by seeder
            'id_kategori_prestasi' => null, // Will be set by seeder  
            'id_tingkat_penghargaan' => null, // Will be set by seeder
            'id_ekskul' => null, // Will be set by seeder (nullable)
            'id_tahun_ajaran' => null, // Will be set by seeder
            'nama_prestasi' => $this->faker->randomElement($prestasi_names),
            'penyelenggara' => $this->faker->randomElement(['Dinas Pendidikan', 'SMAN 1 Jakarta', 'Universitas Indonesia', 'Kemendikbud']),
            'tanggal_prestasi' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'keterangan' => $this->faker->optional(0.7)->paragraph(),
            'dokumen_url' => $this->faker->optional(0.6)->imageUrl(640, 480, 'certificates'),
            'status' => $this->faker->randomElement(['diterima', 'menunggu_validasi', 'diterima']), // Bias toward accepted
            'rata_rata_nilai' => $this->faker->optional(0.3)->randomFloat(2, 75, 95), // Only for academic achievements
            'created_by' => null, // Will be set by seeder
            'validated_by' => null, // Will be set by seeder
            'validated_at' => $this->faker->optional(0.8)->dateTimeBetween('-1 year', 'now'),
            'alasan_tolak' => null,
        ];
    }
}
