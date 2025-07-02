<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SiswaEkskul extends Model
{
    use HasFactory;

    protected $table = 'siswa_ekskul';
    protected $fillable = ['id_siswa', 'id_ekskul', 'jabatan', 'periode'];

    // Relasi ke siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    // Relasi ke ekstrakurikuler
    public function ekskul()
    {
        return $this->belongsTo(Ekstrakurikuler::class, 'id_ekskul');
    }
}