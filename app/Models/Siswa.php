<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';
    protected $fillable = [
        'nisn',
        'nama',
        'jenis_kelamin',
        'tanggal_lahir',
        'id_kelas',
        'alamat',
        'tahun_masuk',
        'no_hp',
        'wali_id',
    ];

    // Relasi ke kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }

    // Relasi ke user (login siswa)
    public function user()
    {
        return $this->hasOne(User::class, 'siswa_id');
    }

    // Relasi ke wali murid (orang tua)
    public function wali()
    {
        return $this->belongsTo(User::class, 'wali_id');
    }

    // Relasi ke ekstrakurikuler (melalui siswa_ekskul)
    public function ekstrakurikuler()
    {
        return $this->belongsToMany(Ekstrakurikuler::class, 'siswa_ekskul', 'id_siswa', 'id_ekskul');
    }

    // Relasi ke prestasi
    public function prestasi()
    {
        return $this->hasMany(PrestasiSiswa::class, 'id_siswa');
    }
}