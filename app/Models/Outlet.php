<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    use HasFactory;

    protected $table = "outlet";

    protected $fillable = [
        'nama_outlet',
        'pemilik',
        'no_telp',
        'email',
        'pin',
        'alamat',
        'instagram',
        'tiktok',
        'facebook',
        'status',
        'foto',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'id_outlet');
    }

    public function produks()
    {
        return $this->hasMany(Produk::class, 'id_outlet');
    }

    
}
