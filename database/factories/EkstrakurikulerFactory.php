<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ekstrakurikuler>
 */
class EkstrakurikulerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ekstrakurikuler = [
            'Pramuka', 'PMR', 'OSIS', 'Rohis', 'Basket', 'Voli', 'Futsal', 
            'Badminton', 'Tenis Meja', 'Karate', 'Taekwondo', 'Band', 'Paduan Suara',
            'Teater', 'Tari', 'English Club', 'Jurnalistik', 'Fotografi', 'Robotika'
        ];
        
        return [
            'nama_ekstrakurikuler' => $this->faker->unique()->randomElement($ekstrakurikuler),
            'deskripsi' => $this->faker->paragraph(),
            'pembina' => $this->faker->name(),
        ];
    }
}
