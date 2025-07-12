<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Mass assignable attributes
    protected $fillable = [
        'nama',
        'username',
        'email',
        'password',
        'role',
        'status',
        'siswa_id',
    ];
    /**
     * Relasi ke activity logs
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Relasi ke siswa (jika user adalah siswa)
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    // Relasi ke anak-anak (jika user adalah wali/orang tua)
    public function anak()
    {
        return $this->hasMany(Siswa::class, 'wali_id');
    }

    // Tambahkan relasi lain jika perlu (siswa, wali, dll)
}
