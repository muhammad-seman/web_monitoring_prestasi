<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PrestasiSiswa extends Model
{
    use HasFactory;

    protected $table = 'prestasi_siswa';
    protected $fillable = [
        'id_siswa',
        'id_kategori_prestasi',
        'id_tingkat_penghargaan',
        'id_ekskul',
        'nama_prestasi',
        'penyelenggara',
        'tanggal_prestasi',
        'keterangan',
        'dokumen_url',
        'surat_tugas_url',
        'status',
        'rata_rata_nilai',
        'created_by',
        'validated_by',
        'validated_at',
        'alasan_tolak',
    ];

    // Relasi ke siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    // Relasi ke kategori prestasi
    public function kategoriPrestasi()
    {
        return $this->belongsTo(KategoriPrestasi::class, 'id_kategori_prestasi');
    }

    // Relasi ke tingkat penghargaan
    public function tingkatPenghargaan()
    {
        return $this->belongsTo(TingkatPenghargaan::class, 'id_tingkat_penghargaan');
    }

    // Relasi ke ekskul
    public function ekskul()
    {
        return $this->belongsTo(Ekstrakurikuler::class, 'id_ekskul');
    }

    // Relasi ke user creator & validator
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    // Alias untuk backward compatibility
    public function kategori()
    {
        return $this->kategoriPrestasi();
    }

    public function tingkat()
    {
        return $this->tingkatPenghargaan();
    }
}