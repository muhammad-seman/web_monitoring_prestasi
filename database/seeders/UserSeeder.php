<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Siswa;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin users
        User::firstOrCreate(['username' => 'admin'], [
            'nama'      => 'Admin Utama',
            'username'  => 'admin',
            'email'     => 'admin@sekolah.test',
            'password'  => Hash::make('admin123'),
            'role'      => 'admin',
        ]);
        
        // Kepala Sekolah
        User::firstOrCreate(['username' => 'kepsek'], [
            'nama'      => 'Dr. Kepala Sekolah',
            'username'  => 'kepsek',
            'email'     => 'kepsek@sekolah.test',
            'password'  => Hash::make('kepsek123'),
            'role'      => 'kepala_sekolah',
        ]);
        
        // Create multiple teachers
        for ($i = 1; $i <= 5; $i++) {
            User::firstOrCreate(['username' => "guru{$i}"], [
                'nama'      => "Guru {$i}",
                'username'  => "guru{$i}",
                'email'     => "guru{$i}@sekolah.test",
                'password'  => Hash::make('guru123'),
                'role'      => 'guru',
            ]);
        }
        
        // Create student users for some students (first 10 students)
        $siswaList = Siswa::limit(10)->get();
        foreach ($siswaList as $index => $siswa) {
            $userNumber = $index + 1;
            User::firstOrCreate(['username' => "siswa{$userNumber}"], [
                'nama'      => $siswa->nama,
                'username'  => "siswa{$userNumber}",
                'email'     => "siswa{$userNumber}@sekolah.test",
                'password'  => Hash::make('siswa123'),
                'role'      => 'siswa',
                'siswa_id'  => $siswa->id,
            ]);
        }
        
        // Create some parent users
        for ($i = 1; $i <= 5; $i++) {
            User::firstOrCreate(['username' => "wali{$i}"], [
                'nama'      => "Wali Siswa {$i}",
                'username'  => "wali{$i}",
                'email'     => "wali{$i}@sekolah.test",
                'password'  => Hash::make('wali123'),
                'role'      => 'wali',
            ]);
        }
        
        $totalUsers = User::count();
        echo "✅ Created {$totalUsers} users across all roles\n";
    }
}