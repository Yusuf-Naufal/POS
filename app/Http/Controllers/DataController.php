<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function fetchData(Request $request)
    {
        $filterValue = $request->input('filter', 'all');
        $perPage = $request->input('per_page', 10);

        // Build the query based on the filter
        $ordersQuery = Order::with('detailOrders.produk');

        if ($filterValue === 'today') {
            $ordersQuery->whereDate('created_at', today());
        } elseif ($filterValue === 'week') {
            $ordersQuery->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($filterValue === 'month') {
            $ordersQuery->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
        }

        // Paginate the results
        $orders = $ordersQuery->paginate($perPage);

        // Format the data to return all the fields
        $data = [
            'data' => $orders->map(function($order) {
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
                    'catatan' => $order->catatan,
                ];
            }),
            'total' => $orders->total(),
            'current_page' => $orders->currentPage(),
            'last_page' => $orders->lastPage(),
        ];

        // Return the data as JSON
        return response()->json($data);
    }
}
