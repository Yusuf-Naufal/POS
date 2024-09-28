<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Outlet;
use App\Models\Produk;
use App\Models\Detail_Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index()
    {
        $outlet = Outlet::all();
        return view('pelanggan.menu-outlet', [
            'Outlet' => $outlet,
        ]);
    }

    public function showOrderForm($id)
    {
        // Fetch the outlet by ID
        $outlet = Outlet::findOrFail($id);

        // Fetch the products directly associated with the selected outlet and order by sales
        $produks = Produk::with('detailTransaksi')  // Assuming 'detailTransaksi' is the relationship name
                    ->leftJoin('detail_transaksi', 'produks.id', '=', 'detail_transaksi.id_produk')
                    ->select('produks.*', DB::raw('COALESCE(SUM(detail_transaksi.qty), 0) as total_sold'))
                    ->where('produks.id_outlet', $outlet->id)
                    ->groupBy('produks.id')
                    ->orderBy('total_sold', 'desc') 
                    ->orderBy('produks.nama_produk', 'asc') 
                    ->get();

        // Group products by category
        $groupedProduks = $produks->groupBy('id_kategori');

        // Return the view with the outlet and grouped products
        return view('pelanggan.order', compact('outlet', 'groupedProduks'));
    }



    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'catatan' => 'nullable|string',
            'id_outlet' => 'required|integer',
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|integer',
            'items.*.price' => 'required|integer',
            'items.*.subtotal' => 'required|integer',
            'jam_mengambil' => 'required|date_format:H:i',
            'nama_pemesan' => 'required|string',
            'no_telp' => 'required|string',
            'pembayaran' => 'required|string',
            'resi' => 'nullable|string',
            'tanggal' => 'required|date_format:Y-m-d',
            'total_belanja' => 'required|integer',
            'total_qty' => 'required|integer',
        ]);

        if ($validator->fails()) {
            dd($validator->errors());
            return response()->json(['errors' => $validator->errors()], 422);
        }
        // MEMBUAT RESI OTOMATIS
        
        // Mulai transaksi database
        DB::beginTransaction();

        try {
            Log::info('Saving order:', $request->all());
            
            $resiNumber = $this->generateResi();

            // Simpan data order
            $order = Order::create([
                'resi' => $resiNumber,
                'nama_pemesan' => $request->nama_pemesan,
                'no_telp' => $request->no_telp,
                'id_outlet' => $request->id_outlet,
                'tanggal' => $request->tanggal,
                'total_qty' => $request->total_qty,
                'total_belanja' => $request->total_belanja,
                'jam_mengambil' => $request->jam_mengambil,
                'pembayaran' => $request->pembayaran,
                'status' => 'Pending',
                'catatan' => $request->catatan,
            ]);

            // Simpan data detail order
            foreach ($request->items as $item) {
                Detail_Order::create([
                    'order_id' => $order->id,
                    'id_produk' => $item['product_id'],
                    'qty' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            // Commit transaksi jika semua berhasil
            DB::commit();

            $this->sendWA($order, $resiNumber);

            // Setelah menyimpan order
            return response()->json(['message' => 'Order berhasil disimpan', 'order_id' => $order->id]);


        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();
            Log::error('Failed to save order:', ['exception' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal menyimpan order'], 500);
        }
    }

    public function sendWA($order, $resiNumber)
    {
        $waNama = $order->nama_pemesan;
        $waOrder = $order->no_telp;
        $waOutlet = $order->outlet->nama_outlet;
        // $waTotal = $order->total_belanja;
        $token = 'euEVyX1WLFFji@5ipREk';
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $waOrder,
                'message' => "✨ *Terima Kasih, $waNama!* ✨\n\n" .
                            "Pesanan Anda telah kami terima. Berikut adalah nomor resi orderan Anda: *$resiNumber* 📦\n\n" .
                            "📝 *Pesanan Anda akan segera kami proses!*\n\n" .
                            "🔍 Untuk cek status order, silakan klik tautan berikut:\n" .
                            "👉 http://192.168.1.9:8000/cek-order?resi=$resiNumber\n\n" .
                            "📅 Terima kasih telah berbelanja di *$waOutlet*! 😊",
                'delay' => '10',
                'countryCode' => '62',
            ),
            CURLOPT_HTTPHEADER => array( 
                'Authorization: ' . $token
            ),
        ));

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            Log::error('WhatsApp send error:', ['error' => $error_msg]);
        }
        curl_close($curl);

        // Optionally log the response or handle it further
        Log::info('WhatsApp response:', ['response' => $response]);
    }

    public function show($id)
    {
        // Mengambil data order berdasarkan ID
        $order = Order::with('detailOrders.produk')->findOrFail($id);

        // Menampilkan view dengan data order
        return view('pelanggan.struk-order', compact('order'));
    }

    public function exportPdf($id)
    {
        $order = Order::with('outlet', 'detailOrders.produk')->find($id);

        if (!$order) {
            abort(404, 'Order not found');
        }

        // Load view dengan data order
        $pdf = Pdf::loadView('pelanggan.pdf', compact('order'));

        // Menghasilkan file PDF
        return $pdf->download('order-'.$order->resi.'.pdf');
    }

    protected function generateResi()
    {
        function getRandomLetters($length)
        {
            $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            return substr(str_shuffle($letters), 0, $length);
        }

        function getRandomDigits($length)
        {
            $digits = '0123456789';
            return substr(str_shuffle($digits), 0, $length);
        }

        $now = new \DateTime();
        $year = $now->format('y'); // Last 2 digits of the year
        $month = $now->format('m'); // Month with leading zero

        $resiNumber = getRandomLetters(2) . $month . $year . getRandomLetters(2) . getRandomDigits(5);

        return strtoupper($resiNumber);
    }

    public function DaftarOrder()
    {
        $user = auth()->user();

        // Dapatkan outlets yang terkait dengan pengguna ini
        $outlets = $user->outlet;

        $pendingOrders = Order::where('id_outlet', $outlets->id)
                ->where('status', 'Pending')
                ->with(['detailOrders.produk'])
                ->orderBy('created_at', 'asc')
                ->get();

        // Retrieve all orders with product details
        $allOrders = Order::with(['detailOrders.produk'])->get();

        return view('user.daftar-order', [
            'Outlet' => $outlets,
            'User' => $user,
            'PendingOrders' => $pendingOrders,
            'AllOrders' => $allOrders,
        ]);
    }

    public function DaftarProcess()
    {
        $user = auth()->user();

        // Dapatkan outlets yang terkait dengan pengguna ini
        $outlets = $user->outlet;

        // Retrieve pending orders for the specific outlet with product details
        $pendingOrders = Order::where('id_outlet', $outlets->id)
            ->where('status', 'Process')
            ->with(['detailOrders.produk'])
            ->get();

        // Retrieve all orders with product details
        $allOrders = Order::with(['detailOrders.produk'])->get();

        return view('user.daftar-order-process', [
            'Outlet' => $outlets,
            'User' => $user,
            'PendingOrders' => $pendingOrders,
            'AllOrders' => $allOrders,
        ]);
    }

    public function RiwayatOrder()
    {
        $user = auth()->user();

        // Dapatkan outlets yang terkait dengan pengguna ini
        $outlets = $user->outlet;

        $perPage = request()->get('per_page', 10);

        // Ambil semua pesanan dari outlet yang terkait dengan status "Denied" atau "Success", dan hanya untuk hari ini
        $allOrders = Order::where('id_outlet', $outlets->id)
            ->whereIn('status', ['Denied', 'Success'])
            ->whereDate('created_at', now()->toDateString()) // Filter order hanya untuk hari ini
            ->with(['outlet','detailOrders.produk'])
            ->paginate($perPage);

        return view('user.riwayat-order', [
            'Outlet' => $outlets,
            'User' => $user,
            'AllOrders' => $allOrders,
        ]);
    }

    public function filterOrders(Request $request)
    {
        // Ambil filter dari query string
        $filter = $request->query('filter');
        
        // Inisialisasi query dasar
        $orders = Order::query();

        $orders->whereIn('status', ['Success', 'Denied']);

        // Periksa jenis filter dan lakukan filter berdasarkan tanggal
        switch ($filter) {
            case 'today':
                // Membandingkan dengan tanggal sekarang (hanya bagian tanggal)
                $orders->whereDate('tanggal', Carbon::now());
                break;
            case 'week':
                // Filter berdasarkan minggu ini
                $orders->whereBetween('tanggal', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'month':
                // Filter berdasarkan bulan ini
                $orders->whereMonth('tanggal', Carbon::now()->month);
                break;
            case 'specific_date':
                // Filter berdasarkan tanggal yang diterima dari request
                $specificDate = $request->query('date'); // Dapatkan tanggal dari query parameter
                if ($specificDate) {
                    $orders->whereDate('tanggal', $specificDate);
                }
                break;
            default:
                // Jika tidak ada filter, ambil semua order
                $orders->whereDate('tanggal', Carbon::now()); // Atau sesuai dengan kebutuhan default
                break;
        }

        // Ambil order yang telah difilter beserta detailnya
        $filteredOrders = $orders->with(['detailOrders.produk'])->get();

        // Kembalikan sebagai response JSON
        return response()->json($filteredOrders);
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        $order->status = $request->status;
        $order->save();

        return response()->json(['success' => true]);
    }

    public function cekOrder(Request $request)
    {
        $resi = $request->query('resi');

        // Cari order berdasarkan resi
        $order = Order::where('resi', $resi)->with('detailOrders.produk')->first();

        if (!$order) {
            // Jika resi tidak ditemukan, arahkan kembali dengan pesan error
            return redirect()->back()->with('error', 'Order dengan resi tersebut tidak ditemukan.');
        }

        // Arahkan ke halaman detail order jika resi ditemukan
        return view('pelanggan.cek-order', ['order' => $order]);
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Hapus detail_order terlebih dahulu
            Detail_Order::where('order_id', $id)->delete();

            // Hapus order-nya
            Order::where('id', $id)->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Order berhasil dibatalkan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal membatalkan order']);
        }
    }

    public function destroyOldDeniedOrders()
    {
        try {
            DB::beginTransaction();

            // Find orders with status 'Denied' that are older than 3 days
            $orders = Order::with('detailOrders')
                ->where('status', 'Denied')
                ->where('created_at', '<', now()->subDays(3))
                ->get();

            foreach ($orders as $order) {
                // Delete associated detail orders
                $order->detailOrders()->delete();

                // Delete the order itself
                $order->delete();
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Old denied orders successfully deleted']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to delete old denied orders']);
        }
    }







}
