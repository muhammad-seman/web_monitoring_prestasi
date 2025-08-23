<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KenaikanKelas>
 */
class KenaikanKelasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $kriteria = [
            'prestasi_count' => $this->faker->numberBetween(2, 10),
            'kehadiran_percentage' => $this->faker->numberBetween(80, 100),
            'nilai_rata_rata' => $this->faker->randomFloat(2, 75, 90),
            'ekstrakurikuler_participation' => $this->faker->boolean(70)
        ];
        
        return [
            'id_siswa' => null, // Will be set by seeder
            'kelas_asal' => null, // Will be set by seeder (XI class)
            'kelas_tujuan' => null, // Will be set by seeder (XII class)
            'tahun_ajaran_id' => null, // Will be set by seeder
            'status' => $this->faker->randomElement(['naik', 'naik', 'naik', 'tidak_naik', 'pending']), // Bias toward naik
            'kriteria_kelulusan' => json_encode($kriteria),
            'tanggal_kenaikan' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'keterangan' => $this->faker->optional(0.5)->sentence(),
            'created_by' => null, // Will be set by seeder
        ];
    }
}
