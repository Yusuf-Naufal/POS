<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'resi', 
        'nama_pemesan', 
        'no_telp', 
        'id_outlet', 
        'tanggal', 
        'total_qty', 
        'total_belanja', 
        'jam_mengambil', 
        'pembayaran', 
        'status', 
        'catatan'
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }

    public function detailOrders()
    {
        return $this->hasMany(Detail_Order::class, 'order_id');
    }
}
