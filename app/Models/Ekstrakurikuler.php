<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ekstrakurikuler extends Model
{
    use HasFactory;

    protected $table = 'ekstrakurikuler';
    protected $fillable = ['nama', 'pembina', 'keterangan'];

    // Relasi ke siswa_ekskul
    public function anggota()
    {
        return $this->hasMany(SiswaEkskul::class, 'id_ekskul');
    }

    // Relasi ke siswa (melalui siswa_ekskul)
    public function siswa()
    {
        return $this->belongsToMany(Siswa::class, 'siswa_ekskul', 'id_ekskul', 'id_siswa');
    }

    // Relasi ke prestasi siswa
    public function prestasi()
    {
        return $this->hasMany(PrestasiSiswa::class, 'id_ekskul');
    }
}