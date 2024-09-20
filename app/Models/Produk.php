<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_produk',
        'sku',
        'id_kategori',
        'id_unit',
        'id_outlet',
        'stok_awal',
        'stok_minimum',
        'harga_jual',
        'harga_modal',
        'status',
        'foto',
        'catatan',
    ];

    public function kategoris()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }


    public function units()
    {
        return $this->belongsTo(Unit::class, 'id_unit');
    }

    public function outlets()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }

}
