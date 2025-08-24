<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ekstrakurikuler;

class EkstrakurikulerSeeder extends Seeder
{
    public function run()
    {
        echo "🎯 Creating Extracurricular Activities...\n";
        
        $ekskulData = [
            // OLAHRAGA
            [
                'nama' => 'Basket',
                'pembina' => 'Drs. Agung Wijaya, S.Pd',
                'keterangan' => 'Ekstrakurikuler basket untuk melatih kerjasama tim dan keterampilan olahraga. Jadwal: Senin, Rabu, Jumat 15:30-17:00 di Lapangan Basket Sekolah'
            ],
            [
                'nama' => 'Futsal',
                'pembina' => 'Ahmad Rahman, S.Pd',
                'keterangan' => 'Ekstrakurikuler futsal untuk mengembangkan bakat sepak bola. Jadwal: Selasa, Kamis 15:30-17:00 di Lapangan Futsal Sekolah'
            ],
            [
                'nama' => 'Bulu Tangkis',
                'pembina' => 'Siti Nurhasanah, S.Pd',
                'keterangan' => 'Ekstrakurikuler bulu tangkis untuk melatih refleks dan ketangkasan. Jadwal: Rabu, Sabtu 15:30-17:00 di GOR Bulu Tangkis'
            ],
            [
                'nama' => 'Voli',
                'pembina' => 'Budi Santoso, S.Pd',
                'keterangan' => 'Ekstrakurikuler bola voli untuk membangun koordinasi dan kerjasama. Jadwal: Senin, Kamis 15:30-17:00 di Lapangan Voli Sekolah'
            ],
            [
                'nama' => 'Taekwondo',
                'pembina' => 'Master Kim Jong Su',
                'keterangan' => 'Ekstrakurikuler taekwondo untuk melatih disiplin dan bela diri. Jadwal: Selasa, Jumat 16:00-17:30 di Dojo Taekwondo'
            ],

            // SENI DAN BUDAYA
            [
                'nama' => 'Paduan Suara',
                'pembina' => 'Dra. Melati Kusuma, M.Pd',
                'keterangan' => 'Ekstrakurikuler paduan suara untuk mengembangkan bakat musik vokal. Jadwal: Rabu, Sabtu 14:00-16:00 di Aula Sekolah'
            ],
            [
                'nama' => 'Tari Tradisional',
                'pembina' => 'Dewi Sartika, S.Sn',
                'keterangan' => 'Ekstrakurikuler tari tradisional Indonesia. Jadwal: Senin, Kamis 14:30-16:30 di Studio Tari'
            ],
            [
                'nama' => 'Band Sekolah',
                'pembina' => 'Rizky Pratama, S.Sn',
                'keterangan' => 'Ekstrakurikuler band untuk mengembangkan bakat musik modern. Jadwal: Selasa, Jumat 15:00-17:00 di Studio Musik'
            ],
            [
                'nama' => 'Teater',
                'pembina' => 'Indira Kencana, S.Pd',
                'keterangan' => 'Ekstrakurikuler teater untuk mengasah kemampuan akting dan public speaking. Jadwal: Rabu, Sabtu 15:00-17:00 di Ruang Teater'
            ],

            // AKADEMIK DAN SAINS
            [
                'nama' => 'Olimpiade Sains',
                'pembina' => 'Dr. Bambang Sutrisno, M.Pd',
                'keterangan' => 'Ekstrakurikuler untuk persiapan olimpiade sains nasional. Jadwal: Senin, Rabu, Jumat 13:00-15:00 di Laboratorium IPA'
            ],
            [
                'nama' => 'English Club',
                'pembina' => 'Sarah Johnson, M.A',
                'keterangan' => 'Ekstrakurikuler bahasa Inggris untuk meningkatkan kemampuan berbahasa. Jadwal: Selasa, Kamis 14:00-16:00 di Language Laboratory'
            ],
            [
                'nama' => 'Robotika',
                'pembina' => 'Ir. Fajar Sidiq, M.T',
                'keterangan' => 'Ekstrakurikuler robotika dan pemrograman. Jadwal: Rabu, Sabtu 15:00-18:00 di Laboratorium Komputer'
            ],

            // ORGANISASI DAN KEPEMIMPINAN
            [
                'nama' => 'OSIS',
                'pembina' => 'Drs. Muhammad Yusuf, M.Pd',
                'keterangan' => 'Organisasi Siswa Intra Sekolah untuk mengembangkan jiwa kepemimpinan. Jadwal: Setiap hari sesuai kebutuhan di Ruang OSIS'
            ],
            [
                'nama' => 'Pramuka',
                'pembina' => 'Arif Wijaya, S.Pd',
                'keterangan' => 'Ekstrakurikuler pramuka untuk membentuk karakter dan kemandirian. Jadwal: Sabtu 07:00-12:00 di Lapangan Upacara'
            ],
            [
                'nama' => 'Pecinta Alam',
                'pembina' => 'Bayu Nugroho, S.Pd',
                'keterangan' => 'Ekstrakurikuler pencinta alam untuk melatih survival dan cinta lingkungan. Jadwal: Minggu (kegiatan lapangan) di Alam terbuka'
            ],

            // KEAGAMAAN DAN KEROHANIAN
            [
                'nama' => 'Rohis (Rohani Islam)',
                'pembina' => 'Ustadz Abdul Rahman, Lc',
                'keterangan' => 'Ekstrakurikuler rohani Islam untuk memperdalam ilmu agama. Jadwal: Jumat 12:00-13:00 di Mushola Sekolah'
            ],
            [
                'nama' => 'Persekutuan Doa',
                'pembina' => 'Pendeta Maria Magdalena, S.Th',
                'keterangan' => 'Ekstrakurikuler persekutuan untuk siswa Kristen. Jadwal: Kamis 12:00-13:00 di Ruang Ibadah'
            ],

            // MEDIA DAN KOMUNIKASI
            [
                'nama' => 'Jurnalistik',
                'pembina' => 'Putri Maharani, S.Kom',
                'keterangan' => 'Ekstrakurikuler jurnalistik untuk mengasah kemampuan menulis dan media. Jadwal: Selasa, Kamis 15:00-17:00 di Ruang Redaksi'
            ],
            [
                'nama' => 'Photography Club',
                'pembina' => 'Ryan Aditya, S.Sn',
                'keterangan' => 'Ekstrakurikuler fotografi untuk mengembangkan kreativitas visual. Jadwal: Sabtu 09:00-12:00 di Studio Photo'
            ]
        ];

        foreach ($ekskulData as $ekskul) {
            Ekstrakurikuler::create($ekskul);
            echo "   ✓ 🎯 Created ekstrakurikuler: {$ekskul['nama']}\n";
        }
        
        echo "🎯 Extracurricular Activities seeding completed!\n\n";
    }
}