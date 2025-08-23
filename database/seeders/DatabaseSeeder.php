<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TahunAjaranSeeder::class,
            KelasSeeder::class,
            SiswaSeeder::class,
            UserSeeder::class,
            KategoriPrestasiSeeder::class,
            TingkatPenghargaanSeeder::class,
            EkstrakurikulerSeeder::class,
            SiswaEkskulSeeder::class,
            PrestasiSiswaSeeder::class,
            DokumenPrestasiSeeder::class,
            RiwayatPrestasiSiswaSeeder::class,
        ]);
    }
}