<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\KategoriPrestasi;

class KategoriPrestasiSeeder extends Seeder
{
    public function run()
    {
        KategoriPrestasi::insert([
            ['nama_kategori' => 'Akademik',  'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Olahraga',  'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Seni',      'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Keagamaan', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Ekskul',    'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}