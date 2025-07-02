<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TingkatPenghargaan extends Model
{
    use HasFactory;
    protected $table = 'tingkat_penghargaan';
    protected $fillable = ['tingkat'];
}