<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KenaikanKelas extends Model
{
    use HasFactory;

    protected $table = 'kenaikan_kelas';

    protected $fillable = [
        'id_siswa',
        'kelas_asal',
        'kelas_tujuan',
        'tahun_ajaran_id',
        'status',
        'kriteria_kelulusan',
        'tanggal_kenaikan',
        'keterangan',
        'created_by'
    ];

    protected $casts = [
        'kriteria_kelulusan' => 'array',
        'tanggal_kenaikan' => 'date',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function kelasAsal()
    {
        return $this->belongsTo(Kelas::class, 'kelas_asal');
    }

    public function kelasTujuan()
    {
        return $this->belongsTo(Kelas::class, 'kelas_tujuan');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByTahunAjaran($query, $tahunAjaranId)
    {
        return $query->where('tahun_ajaran_id', $tahunAjaranId);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeNaik($query)
    {
        return $query->where('status', 'naik');
    }

    public function scopeTidakNaik($query)
    {
        return $query->where('status', 'tidak_naik');
    }

    public function isNaik()
    {
        return $this->status === 'naik';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isTidakNaik()
    {
        return $this->status === 'tidak_naik';
    }
}
