<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DokumenPrestasi extends Model
{
    use HasFactory;

    protected $table = 'dokumen_prestasi';
    protected $fillable = [
        'id_prestasi_siswa',
        'nama_file',
        'path_file',
        'uploaded_by',
        'uploaded_at'
    ];

    // Relasi ke prestasi siswa
    public function prestasi()
    {
        return $this->belongsTo(PrestasiSiswa::class, 'id_prestasi_siswa');
    }

    // Relasi ke user yang upload
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}