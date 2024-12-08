<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeographicData extends Model
{
    use HasFactory;

    // Menentukan nama tabel (optional jika nama tabel mengikuti konvensi)
    protected $table = 'geographic_data';

    // Menentukan kolom yang dapat diisi (fillable)
    protected $fillable = [
        'name',
        'type',
        'coordinates',
        'description','photo',
    ];

    // Menentukan tipe kolom yang bertipe JSON
    protected $casts = [
        'coordinates' => 'array', // Mengubah kolom coordinates menjadi array otomatis
    ];

    // Jika perlu, Anda bisa menambahkan metode lain seperti query scope atau accessor jika diperlukan
}
