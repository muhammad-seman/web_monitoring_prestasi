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
        'name',
        'username',
        'email',
        'password',
        'role',
        'status',
    ];

    // Attributes to hide
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Type casting
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relasi ke tabel employees (jika user punya satu data pegawai)
     */
    // public function employee()
    // {
    //     return $this->hasOne(Employee::class);
    // }

    /**
     * Relasi ke activity logs
     */
    // public function activityLogs()
    // {
    //     return $this->hasMany(ActivityLog::class);
    // }

}
