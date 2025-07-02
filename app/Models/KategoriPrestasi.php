<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KategoriPrestasi extends Model
{
    use HasFactory;
    protected $table = 'kategori_prestasi';
    protected $fillable = ['nama_kategori'];
}