<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Produk;
use App\Models\Detail_Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function LaporanMaster() 
    {
        $user = auth()->user();
        $outlet = $user->outlet;

        $today = Carbon::today();

        // Get the top 3 favorite products for each category (food and drink) for today
        $topProducts = DB::table('detail_transaksi')
            ->join('transaksi', 'detail_transaksi.id_transaksi', '=', 'transaksi.id')
            ->join('produks', 'detail_transaksi.id_produk', '=', 'produks.id')
            ->join('kategoris', 'produks.id_kategori', '=', 'kategoris.id')
            ->select('produks.id', 'produks.nama_produk', 'kategoris.nama_kategori as kategori', DB::raw('SUM(detail_transaksi.qty) as total_qty'))
            ->where('transaksi.id_outlet', $outlet->id)
            ->groupBy('produks.id', 'produks.nama_produk', 'kategori')
            ->orderBy('total_qty', 'desc')
            ->get();

        $produk = Produk::where('id_outlet',$outlet->id)
        ->orderBy('nama_produk', 'asc')
        ->get();

        // Separate products into food and drink
        $foodProducts = $topProducts->filter(function ($product) {
            return $product->kategori == 'Makanan';
        })->take(3);

        $drinkProducts = $topProducts->filter(function ($product) {
            return $product->kategori == 'Minuman';
        })->take(3);

        // Calculate profit and earnings
        $earnings = DB::table('detail_transaksi')
            ->join('transaksi', 'detail_transaksi.id_transaksi', '=', 'transaksi.id')
            ->join('produks', 'detail_transaksi.id_produk', '=', 'produks.id')
            ->where('transaksi.id_outlet', $outlet->id)
            ->select(DB::raw('SUM(detail_transaksi.qty * produks.harga_jual) as total_earnings'))
            ->value('total_earnings');

        $cost = DB::table('detail_transaksi')
            ->join('transaksi', 'detail_transaksi.id_transaksi', '=', 'transaksi.id')
            ->join('produks', 'detail_transaksi.id_produk', '=', 'produks.id')
            ->where('transaksi.id_outlet', $outlet->id)
            ->select(DB::raw('SUM(detail_transaksi.qty * produks.harga_modal) as total_cost'))
            ->value('total_cost');

        $profit = $earnings - $cost;

        return view('pemilik.laporan', [
            'Outlet' => $outlet,
            'foodProducts' => $foodProducts,
            'drinkProducts' => $drinkProducts,
            'earnings' => $earnings,
            'profit' => $profit,
            'produk' => $produk
        ]);
    }

    public function getProductStats(Request $request)
    {
        $productId = $request->input('product');
        $period = $request->input('period');

        // Define start and end dates based on the selected period
        switch ($period) {
            case 'minggu':
                $startDate = now()->startOfWeek();
                $endDate = now()->endOfWeek();
                break;
            case 'bulan':
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                break;
            case 'hari': // For today
                $startDate = now()->startOfDay();
                $endDate = now()->endOfDay();
                break;
            default: // Fallback if no valid period
                $startDate = now()->startOfDay();
                $endDate = now()->endOfDay();
                break;
        }

        // Generate all dates in the range
        $dateRange = [];
        $date = $startDate->copy();
        while ($date->lte($endDate)) {
            $dateRange[] = $date->format('Y-m-d');
            $date->addDay();
        }

        // Group by date and calculate total quantity sold
        $data = Detail_Transaksi::join('transaksi', 'detail_transaksi.id_transaksi', '=', 'transaksi.id')
            ->where('detail_transaksi.id_produk', $productId)
            ->whereBetween('transaksi.tanggal_transaksi', [$startDate, $endDate])
            ->selectRaw('DATE(transaksi.tanggal_transaksi) as date, SUM(detail_transaksi.qty) as total_qty')
            ->groupBy('date')
            ->get()
            ->keyBy('date'); // Use date as key for easy access

        // Prepare final data with all dates, filling missing dates with zero
        $result = [];
        foreach ($dateRange as $date) {
            $totalQty = $data->has($date) ? $data->get($date)->total_qty : 0; // Default to 0 if no data
            $result[] = [
                'date' => $date,
                'total_qty' => $totalQty
            ];
        }

        return response()->json($result);
    }

    public function getStatistics(Request $request)
    {
        $periode = $request->get('periode', 'hari');
        $startDate = null;
        $endDate = null;

        switch ($periode) {
            case 'minggu':
                $startDate = now()->startOfWeek();
                $endDate = now()->endOfWeek();
                break;
            case 'bulan':
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                break;
            case 'tahun':
                $startDate = now()->startOfYear();
                $endDate = now()->endOfYear();
                break;
            default:
                $startDate = now()->startOfDay();
                $endDate = now()->endOfDay();
                break;
        }

        // Generate date range
        $dateRange = [];
        
        if ($periode === 'tahun') {
            // For 'tahun', generate month labels (e.g., January, February)
            $date = $startDate->copy();
            while ($date->lte($endDate)) {
                $dateRange[] = $date->format('F'); // Format month as full name
                $date->addMonth();
            }

            // Adjust the query for grouping by month
            $query = DB::table('transaksi')
                ->select(DB::raw('DATE_FORMAT(tanggal_transaksi, "%M") as label, SUM(total_belanja) as total_penghasilan, COUNT(id) as total_transaksi'))
                ->groupBy('label')
                ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
                ->where('id_outlet', auth()->user()->id_outlet);
        } else {
            // For other periods (hari, minggu, bulan), use daily labels
            $date = $startDate->copy();
            while ($date->lte($endDate)) {
                $dateRange[] = $date->format('Y-m-d');
                $date->addDay();
            }

            $query = DB::table('transaksi')
                ->select(DB::raw('DATE(tanggal_transaksi) as label, SUM(total_belanja) as total_penghasilan, COUNT(id) as total_transaksi'))
                ->groupBy('label')
                ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
                ->where('id_outlet', auth()->user()->id_outlet);
        }

        $data = $query->get()->keyBy('label');

        // Fill missing dates or months with zero values
        $result = array_map(function ($date) use ($data) {
            return $data->get($date, (object) [
                'label' => $date,
                'total_penghasilan' => 0,
                'total_transaksi' => 0
            ]);
        }, $dateRange);

        return response()->json($result);
    }

    public function fetchEarnings(Request $request)
    {
        $user = auth()->user();
        $outlet = $user->outlet;

        // Fetch earnings grouped by date
        $earnings = DB::table('detail_transaksi')
            ->join('transaksi', 'detail_transaksi.id_transaksi', '=', 'transaksi.id')
            ->join('produks', 'detail_transaksi.id_produk', '=', 'produks.id')
            ->where('transaksi.id_outlet', $outlet->id)
            ->select(
                DB::raw('DATE(transaksi.tanggal_transaksi) as date'),
                DB::raw('SUM(detail_transaksi.qty * produks.harga_jual) as total_earnings')
            )
            ->groupBy('date')
            ->get();

        // Map earnings data to FullCalendar's event format
        $events = $earnings->map(function ($earning) {
            return [
                'title' => $earning->total_earnings, // Send raw value
                'start' => $earning->date,
                'allDay' => true
            ];
        });

        return response()->json($events);
    }
}
