<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuan_Outlet extends Model
{
    use HasFactory;

    // Nama tabel jika berbeda dari default
    protected $table = 'pengajuan_outlet';

    // Mass assignable attributes
    protected $fillable = [
        'nama_outlet',
        'id_pemilik',
        'no_telp',
        'pin',
        'email',
        'alamat',
        'instagram',
        'facebook',
        'tiktok',
        'status',
        'foto',
    ];

    // Relasi dengan model User
    public function pemilik()
    {
        return $this->belongsTo(User::class, 'id_pemilik');
    }
}
