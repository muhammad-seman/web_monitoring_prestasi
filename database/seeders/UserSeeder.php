<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Admin dummy
         User::create([
            'name' => 'Admin Dummy',
            'username' => 'admin', // <-- username harus diisi!
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), // Default: password
            'role' => 'admin',
            'status' => 'active',
        ]);

    }
}
