<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_unit',
    ];

    public function produks()
    {
        return $this->hasMany(Produk::class, 'id_unit');
    }
}
