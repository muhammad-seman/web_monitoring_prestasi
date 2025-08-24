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

    // Relasi ke notifikasi
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadNotifications()
    {
        return $this->notifications()->where('is_read', false);
    }

    // Relasi untuk prestasi yang dibuat oleh user ini (guru/admin)
    public function createdPrestasi()
    {
        return $this->hasMany(PrestasiSiswa::class, 'created_by');
    }

    // Relasi untuk prestasi yang divalidasi oleh user ini
    public function validatedPrestasi()
    {
        return $this->hasMany(PrestasiSiswa::class, 'validated_by');
    }

    // Relasi untuk kelas yang diwali oleh guru ini
    public function waliKelas()
    {
        return $this->hasMany(Kelas::class, 'id_wali_kelas');
    }
}
