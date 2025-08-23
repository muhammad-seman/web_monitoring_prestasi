<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KategoriPrestasi extends Model
{
    use HasFactory;
    protected $table = 'kategori_prestasi';
    protected $fillable = [
        'nama_kategori',
        'jenis_prestasi',
        'tingkat_kompetisi',
        'bidang_prestasi',
        'deskripsi',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function prestasi()
    {
        return $this->hasMany(PrestasiSiswa::class, 'id_kategori_prestasi');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis_prestasi', $jenis);
    }

    public function scopeByTingkat($query, $tingkat)
    {
        return $query->where('tingkat_kompetisi', $tingkat);
    }
}