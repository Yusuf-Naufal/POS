<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Models\Detail_Transaksi;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function KasirDashboard()
    {
        $user = auth()->user();

        // Dapatkan outlets yang terkait dengan pengguna ini
        $outlets = $user->outlet;


        // Ambil produk yang terkait dengan outlet
        $produks = Produk::with(['detailTransaksi', 'kategoris']) // Eager load kategoris
            ->leftJoin('detail_transaksi', 'produks.id', '=', 'detail_transaksi.id_produk')
            ->select('produks.id', 'produks.nama_produk', 'produks.harga_jual', 'produks.id_kategori', 'produks.foto', DB::raw('COALESCE(SUM(detail_transaksi.qty), 0) as total_sold'))
            ->where('produks.id_outlet', $outlets->id)
            ->groupBy('produks.id', 'produks.nama_produk', 'produks.harga_jual', 'produks.id_kategori', 'produks.foto') // Include foto in GROUP BY
            ->orderBy('total_sold', 'desc')
            ->orderBy('produks.nama_produk', 'asc')
            ->get();


        // Group products by category
        $groupedProduks = $produks->groupBy('id_kategori');

        return view('user.dashboard', [
            'Outlet' => $outlets,
            'User' => $user,
            'Produk' => $produks,
            'groupedProduks' => $groupedProduks,
        ]);
    }

    public function MasterDashboard()
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
            ->whereDate('transaksi.tanggal_transaksi', $today)
            ->groupBy('produks.id', 'produks.nama_produk', 'kategori')
            ->orderBy('total_qty', 'desc')
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
            ->whereDate('transaksi.tanggal_transaksi', $today)
            ->select(DB::raw('SUM(detail_transaksi.qty * produks.harga_jual) as total_earnings'))
            ->value('total_earnings');

        $cost = DB::table('detail_transaksi')
            ->join('transaksi', 'detail_transaksi.id_transaksi', '=', 'transaksi.id')
            ->join('produks', 'detail_transaksi.id_produk', '=', 'produks.id')
            ->where('transaksi.id_outlet', $outlet->id)
            ->whereDate('transaksi.tanggal_transaksi', $today)
            ->select(DB::raw('SUM(detail_transaksi.qty * produks.harga_modal) as total_cost'))
            ->value('total_cost');

        $profit = $earnings - $cost;

        return view('pemilik.dashboard', [
            'Outlet' => $outlet,
            'foodProducts' => $foodProducts,
            'drinkProducts' => $drinkProducts,
            'earnings' => $earnings,
            'profit' => $profit,
        ]);
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







}
