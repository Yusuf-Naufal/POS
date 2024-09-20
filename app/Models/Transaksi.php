<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = "transaksi";

    protected $fillable = [
        'resi',
        'tanggal_transaksi',
        'total_qty',
        'total_belanja',
        'id_outlet',
        'status',
    ];

    public function details()
    {
        return $this->hasMany(Detail_Transaksi::class, 'id_transaksi');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }
}
