<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Siswa>
 */
class SiswaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nisn' => $this->faker->unique()->numerify('##########'),
            'nama' => $this->faker->name(),
            'jenis_kelamin' => $this->faker->randomElement(['L', 'P']),
            'tanggal_lahir' => $this->faker->dateTimeBetween('-18 years', '-15 years')->format('Y-m-d'),
            'tempat_lahir' => $this->faker->city(),
            'id_kelas' => null, // Will be set by seeder
            'alamat' => $this->faker->address(),
            'tahun_masuk' => $this->faker->numberBetween(2020, 2024),
        ];
    }
}
