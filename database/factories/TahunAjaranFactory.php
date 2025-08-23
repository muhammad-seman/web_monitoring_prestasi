<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TahunAjaran>
 */
class TahunAjaranFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tahun = $this->faker->numberBetween(2022, 2025);
        $tahun_berikut = $tahun + 1;
        
        return [
            'nama_tahun_ajaran' => "{$tahun}/{$tahun_berikut}",
            'tanggal_mulai' => "{$tahun}-07-15",
            'tanggal_selesai' => "{$tahun_berikut}-06-30",
            'semester_aktif' => $this->faker->randomElement(['ganjil', 'genap']),
            'is_active' => false,
        ];
    }
}
