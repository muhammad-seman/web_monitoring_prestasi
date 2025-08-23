<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SiswaEkskul>
 */
class SiswaEkskulFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tahunAjaran = $this->faker->randomElement(['2022/2023', '2023/2024', '2024/2025']);
        $semester = $this->faker->randomElement(['ganjil', 'genap']);
        
        return [
            'id_siswa' => null, // Will be set by seeder
            'id_ekskul' => null, // Will be set by seeder
            'jabatan' => $this->faker->optional(0.3)->randomElement(['Ketua', 'Wakil Ketua', 'Sekretaris', 'Bendahara', 'Anggota']),
            'periode' => "{$semester} {$tahunAjaran}",
            'tahun_ajaran' => $tahunAjaran,
            'semester' => $semester,
            'tanggal_mulai' => $semester === 'ganjil' ? $this->faker->dateTimeBetween('-2 years', '-1 year')->format('Y-m-d') : $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'tanggal_selesai' => $this->faker->optional(0.5)->dateTimeBetween('now', '+6 months')->format('Y-m-d'),
            'status_keaktifan' => $this->faker->randomElement(['aktif', 'non_aktif', 'graduated']),
            'keterangan' => $this->faker->optional(0.4)->sentence(),
        ];
    }
}
