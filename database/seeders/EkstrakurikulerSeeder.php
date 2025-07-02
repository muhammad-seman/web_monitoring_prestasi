<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Ekstrakurikuler;

class EkstrakurikulerSeeder extends Seeder
{
    public function run()
    {
        Ekstrakurikuler::insert([
            ['nama' => 'Pramuka',   'pembina' => 'Pak Budi',    'keterangan' => 'Wajib kelas X', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'PMR',       'pembina' => 'Bu Siti',     'keterangan' => 'Pilihan',        'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Futsal',    'pembina' => 'Pak Tono',    'keterangan' => null,             'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}