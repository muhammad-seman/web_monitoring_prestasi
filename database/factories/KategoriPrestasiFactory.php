<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KategoriPrestasi>
 */
class KategoriPrestasiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jenis = $this->faker->randomElement(['akademik', 'non_akademik']);
        $kategoriAkademik = ['Matematika', 'Fisika', 'Kimia', 'Biologi', 'Bahasa Indonesia', 'Bahasa Inggris'];
        $kategoriNonAkademik = ['Olahraga', 'Seni', 'Organisasi', 'Kepemimpinan', 'Keterampilan'];
        
        $nama_kategori = $jenis === 'akademik' 
            ? $this->faker->randomElement($kategoriAkademik)
            : $this->faker->randomElement($kategoriNonAkademik);
            
        return [
            'nama_kategori' => $nama_kategori,
            'jenis_prestasi' => $jenis,
            'tingkat_kompetisi' => $this->faker->randomElement(['sekolah', 'kabupaten', 'provinsi', 'nasional', 'internasional']),
            'bidang_prestasi' => $jenis === 'akademik' ? 'akademik' : $this->faker->randomElement(['olahraga', 'seni', 'organisasi']),
            'deskripsi' => $this->faker->sentence(),
            'is_active' => $this->faker->boolean(90), // 90% chance to be active
        ];
    }
}
