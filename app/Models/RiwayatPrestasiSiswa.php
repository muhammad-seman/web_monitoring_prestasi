<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RiwayatPrestasiSiswa extends Model
{
    use HasFactory;

    protected $table = 'riwayat_prestasi_siswa';
    protected $fillable = [
        'id_prestasi_siswa',
        'status',
        'keterangan',
        'tanggal_perubahan',
        'changed_by',
    ];

    // Relasi ke prestasi
    public function prestasi()
    {
        return $this->belongsTo(PrestasiSiswa::class, 'id_prestasi_siswa');
    }

    // Relasi ke user (yang mengubah)
    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}