<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Siswa;

class SiswaSeeder extends Seeder
{
    public function run()
    {
        Siswa::create([
            'nisn'          => '0012345678',
            'nama'          => 'Ahmad Fadli',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '2007-07-01',
            // 'id_kelas'      => 1,
            'alamat'        => 'Jl. Merdeka No. 10',
            'tahun_masuk'   => 2023,
        ]);
        Siswa::create([
            'nisn'          => '0012345679',
            'nama'          => 'Siti Aminah',
            'jenis_kelamin' => 'P',
            'tanggal_lahir' => '2007-08-15',
            // 'id_kelas'      => 2,
            'alamat'        => 'Jl. Pancasila No. 5',
            'tahun_masuk'   => 2023,
        ]);
    }
}