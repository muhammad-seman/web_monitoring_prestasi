<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'nama'      => 'Admin Utama',
            'username'  => 'admin',
            'email'     => 'admin@sekolah.test',
            'password'  => Hash::make('admin123'),
            'role'      => 'admin',
        ]);
        User::create([
            'nama'      => 'Guru Satu',
            'username'  => 'guru1',
            'email'     => 'guru1@sekolah.test',
            'password'  => Hash::make('guru123'),
            'role'      => 'guru',
            'kelas_id'  => 1, // asumsi kelas 1 sudah ada
        ]);
        User::create([
            'nama'      => 'Siswa Satu',
            'username'  => 'siswa1',
            'email'     => 'siswa1@sekolah.test',
            'password'  => Hash::make('siswa123'),
            'role'      => 'siswa',
            'siswa_id'  => 1, // asumsi siswa 1 sudah ada
        ]);
        User::create([
            'nama'      => 'Wali Siswa Satu',
            'username'  => 'wali1',
            'email'     => 'wali1@sekolah.test',
            'password'  => Hash::make('wali123'),
            'role'      => 'wali',
        ]);
        User::create([
            'nama'      => 'Kepala Sekolah',
            'username'  => 'kepsek',
            'email'     => 'kepsek@sekolah.test',
            'password'  => Hash::make('kepsek123'),
            'role'      => 'kepala_sekolah',
        ]);
    }
}