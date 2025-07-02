<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';
    protected $fillable = ['nama_kelas', 'id_wali_kelas', 'tahun_ajaran'];

    // Relasi ke wali kelas (user)
    public function wali()
    {
        return $this->belongsTo(User::class, 'id_wali_kelas');
    }

    // Relasi ke siswa-siswa di kelas
    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'id_kelas');
    }
}