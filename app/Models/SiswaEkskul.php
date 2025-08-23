<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SiswaEkskul extends Model
{
    use HasFactory;

    protected $table = 'siswa_ekskul';
    protected $fillable = [
        'id_siswa', 
        'id_ekskul', 
        'jabatan', 
        'tahun_ajaran',
        'semester',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_keaktifan',
        'keterangan'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

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

    public function scopeByTahunAjaran($query, $tahunAjaran)
    {
        return $query->where('tahun_ajaran', $tahunAjaran);
    }

    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    public function scopeAktif($query)
    {
        return $query->where('status_keaktifan', 'aktif');
    }

    public function scopeNonAktif($query)
    {
        return $query->where('status_keaktifan', 'non_aktif');
    }

    public function scopeGraduated($query)
    {
        return $query->where('status_keaktifan', 'graduated');
    }

    public function isAktif()
    {
        return $this->status_keaktifan === 'aktif';
    }

    public function getPeriodeLengkapAttribute()
    {
        return $this->tahun_ajaran . ' - ' . ucfirst($this->semester);
    }
}