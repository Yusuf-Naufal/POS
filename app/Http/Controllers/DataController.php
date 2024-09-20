<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function fetchData(Request $request)
    {
        $filterValue = $request->input('filter');

        // Fetch the latest orders with related details
        if ($filterValue === 'today') {
            $orders = Order::whereDate('created_at', today())->with('detailOrders.produk')->get();
        } elseif ($filterValue === 'week') {
            $orders = Order::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->with('detailOrders.produk')->get();
        } elseif ($filterValue === 'month') {
            $orders = Order::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->with('detailOrders.produk')->get();
        } else {
            $orders = Order::with('detailOrders.produk')->get();
        }

        // Format the data to return all the fields
        $data = $orders->map(function($order) {
            return [
                'resi' => $order->resi,
                'nama_pemesan' => $order->nama_pemesan,
                'no_telp' => $order->no_telp,
                'nama_outlet' => $order->outlet->nama_outlet,
                'tanggal' => $order->tanggal,
                'total_qty' => $order->total_qty,
                'total_belanja' => $order->total_belanja,
                'jam_mengambil' => $order->jam_mengambil,
                'pembayaran' => $order->pembayaran,
                'status' => $order->status,
                'catatan' => $order->catatan
            ];
        });

        // Return the data as JSON
        return response()->json($data);
    }
}
