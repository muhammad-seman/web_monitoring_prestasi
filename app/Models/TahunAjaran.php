<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajaran';

    protected $fillable = [
        'nama_tahun_ajaran',
        'tanggal_mulai',
        'tanggal_selesai', 
        'semester_aktif',
        'is_active',
        'keterangan'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean',
    ];

    public function prestasi()
    {
        return $this->hasMany(PrestasiSiswa::class, 'id_tahun_ajaran');
    }

    public function kenaikanKelas()
    {
        return $this->hasMany(KenaikanKelas::class, 'tahun_ajaran_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_active', true)->first();
    }

    public static function getActiveTahunAjaran()
    {
        return static::where('is_active', true)->first();
    }

    public function isActive()
    {
        return $this->is_active;
    }

    public function getFormatTahunAttribute()
    {
        return $this->nama_tahun_ajaran . ' (' . ucfirst($this->semester_aktif) . ')';
    }
}
