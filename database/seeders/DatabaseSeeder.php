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
        echo "\n🚀 Starting comprehensive database seeding...\n\n";
        
        $this->call([
            // Core data first
            TahunAjaranSeeder::class,
            KelasSeeder::class,
            SiswaSeeder::class,
            UserSeeder::class,
            
            // Achievement system data
            KategoriPrestasiSeeder::class,
            TingkatPenghargaanSeeder::class,
            
            // Extracurricular system data
            EkstrakurikulerSeeder::class,
            SiswaEkskulSeeder::class,
            
            // Achievement records
            PrestasiSiswaSeeder::class,
            DokumenPrestasiSeeder::class,
            RiwayatPrestasiSiswaSeeder::class,
            
            // Class progression system (depends on all above data)
            KenaikanKelasSeeder::class,
        ]);
        
        echo "\n✅ Database seeding completed successfully!\n";
        echo "📊 System is ready for testing with comprehensive data.\n\n";
    }
}