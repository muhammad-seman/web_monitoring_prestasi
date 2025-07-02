<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\RiwayatPrestasiSiswa;

class RiwayatPrestasiSiswaSeeder extends Seeder
{
    public function run()
    {
        RiwayatPrestasiSiswa::insert([
            [
                'id_prestasi_siswa' => 1,
                'status'            => 'draft',
                'keterangan'        => 'Input awal oleh siswa.',
                'tanggal_perubahan' => now()->subDays(7),
                'changed_by'        => 3,
                'created_at'        => now()->subDays(7),
                'updated_at'        => now()->subDays(7),
            ],
            [
                'id_prestasi_siswa' => 1,
                'status'            => 'menunggu_validasi',
                'keterangan'        => 'Diajukan untuk validasi.',
                'tanggal_perubahan' => now()->subDays(6),
                'changed_by'        => 3,
                'created_at'        => now()->subDays(6),
                'updated_at'        => now()->subDays(6),
            ],
            [
                'id_prestasi_siswa' => 1,
                'status'            => 'diterima',
                'keterangan'        => 'Disetujui oleh wali kelas.',
                'tanggal_perubahan' => now()->subDays(5),
                'changed_by'        => 2,
                'created_at'        => now()->subDays(5),
                'updated_at'        => now()->subDays(5),
            ],
        ]);
    }
}