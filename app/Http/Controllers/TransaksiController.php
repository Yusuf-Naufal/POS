<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Order;
use App\Models\Outlet;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Models\Detail_Transaksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransaksiController extends Controller
{
    // =============================== ADMIN =================================== //
    public function index()
    {

        $perPage = request()->get('per_page', 10);

        $transaksi = Transaksi::paginate($perPage);
        $outlet = Outlet::all();
        return view('admin.transaksi.transaksi', [
             'Transaksi' => $transaksi,
             'Outlet' => $outlet,

        ]);
    }

    public function show($resi)
    {
        $transaksi = Transaksi::where('resi', $resi)->firstOrFail();

        // Retrieve the transaction details, such as detail_transaksi
        $detailTransaksi = Detail_Transaksi::with('produk')
            ->join('produks', 'detail_transaksi.id_produk', '=', 'produks.id')
            ->where('detail_transaksi.id_transaksi', $transaksi->id)
            ->orderBy('produks.nama_produk', 'asc') 
            ->select('detail_transaksi.*') 
            ->get();



        // $detailTransaksi = Detail_Transaksi::where('id_transaksi', $transaksi->id)->get();
        
        if (auth()->user()->role === 'Admin') {
            return view('admin.transaksi.detail-transaksi', compact('transaksi', 'detailTransaksi'));
        } else {
            return view('pemilik.transaksi.detail-transaksi', compact('transaksi', 'detailTransaksi'));
        }
    }

    public function destroy($id)
    {
        // Find the transaction by ID
        $transaksi = Transaksi::findOrFail($id);

        // Delete the transaction and related details if needed
        $transaksi->delete();

        if (auth()->user()->role === 'Admin') {
            return redirect()->route('admin.transaksi.index')->with('success', 'Transaction Berhasil Dihapus!');
        }else {
            // Redirect with a success message
            return redirect()->route('master.transaksi.index')->with('success', 'Transaction Berhasil Dihapus!');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'produk' => 'required|array',
                'produk.*.id' => 'required|exists:produks,id',
                'produk.*.qty' => 'required|integer|min:1',
                'tanggal_transaksi' => 'required|date',
                'catatan' => 'nullable|string'
            ]);

            DB::transaction(function () use ($validatedData, $id) {
                Detail_Transaksi::where('id_transaksi', $id)->delete();

                foreach ($validatedData['produk'] as $item) {
                    $product = Produk::find($item['id']);
                    if (!$product) {
                        throw new \Exception("Product with ID {$item['id']} not found.");
                    }

                    $price = $product->harga_jual;
                    $subtotal = $price * $item['qty'];

                    Detail_Transaksi::create([
                        'id_transaksi' => $id,
                        'id_produk' => $item['id'],
                        'qty' => $item['qty'],
                        'subtotal' => $subtotal
                    ]);
                }

                $totalQty = Detail_Transaksi::where('id_transaksi', $id)->sum('qty');
                $totalBelanja = Detail_Transaksi::where('id_transaksi', $id)
                    ->join('produks', 'detail_transaksi.id_produk', '=', 'produks.id')
                    ->sum(DB::raw('qty * harga_jual'));

                $transaksi = Transaksi::findOrFail($id);
                $transaksi->catatan = $validatedData['catatan']; 
                $transaksi->tanggal_transaksi = $validatedData['tanggal_transaksi'];
                $transaksi->total_qty = $totalQty;
                $transaksi->total_belanja = $totalBelanja;
                $transaksi->save();
            });

            return response()->json(['message' => 'Transaksi Berhasil Diupdate!!']);
        } catch (\Exception $e) {
            Log::error('Error updating transaksi for ID ' . $id . ': ' . $e->getMessage());
            return response()->json(['message' => 'Error updating transaksi'], 500);
        }
    }


    public function editAdmin($resi)
    {
        // 1. Fetch the transaction and its details based on the provided 'resi'
        $transaksi = Transaksi::with('details.produk')->where('resi', $resi)->firstOrFail();
        $OutletId = $transaksi->id_outlet;

        // 2. Get the IDs of products already in the transaction
        $produkIdsInTransaksi = $transaksi->details->pluck('produk.id');

        // 3. Fetch all products that are not part of the transaction for the specific outlet
        $produksYangBelumAda = Produk::where('id_outlet', $OutletId)
            ->whereNotIn('id', $produkIdsInTransaksi)
            ->get();

        // 4. Fetch all products for the specific outlet, without filtering
        $semuaProduks = Produk::where('id_outlet', $OutletId)->get();

        // Return view with all the data
        return view('admin.transaksi.edit-transaksi', [
            'transaksi' => $transaksi,
            'detailTransaksi' => $transaksi->details,
            'semuaProduks' => $semuaProduks,
            'produksYangBelumAda' => $produksYangBelumAda,
        ]);
    }

    // ================================= KASIR ================================== //
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'resi' => 'required|string|max:13',
                'tanggal_transaksi' => 'required|date',
                'total_qty' => 'required|integer',
                'total_belanja' => 'required|numeric',
                'id_outlet' => 'required|integer',
                'status' => 'required|string',
                'orderItems' => 'required|array',
                'orderItems.*.productId' => 'required|integer',
                'orderItems.*.qty' => 'required|integer',
                'orderItems.*.subtotal' => 'required|numeric',
            ]);

            DB::transaction(function() use ($validated) {
                // Insert into transaksi
                $transaksi = Transaksi::create([
                    'resi' => $validated['resi'],
                    'tanggal_transaksi' => $validated['tanggal_transaksi'],
                    'total_qty' => $validated['total_qty'],
                    'total_belanja' => $validated['total_belanja'],
                    'id_outlet' => $validated['id_outlet'],
                    'status' => $validated['status'],
                ]);

                // Insert into detail_transaksi
                foreach ($validated['orderItems'] as $item) {
                    Detail_Transaksi::create([
                        'id_transaksi' => $transaksi->id,
                        'id_produk' => $item['productId'],
                        'qty' => $item['qty'],
                        'subtotal' => $item['subtotal'],
                    ]);
                }
            });

            return response()->json(['message' => 'Transaction saved successfully']);
        } catch (Exception $e) {
            // Log the exception and return an error response
            Log::error('Transaction error:', ['exception' => $e]);
            return response()->json(['error' => 'Failed to save transaction'], 500);
        }
    }

    // public function dashboardView()
    // {
    //     $user = auth()->user();

    //     // Dapatkan outlets yang terkait dengan pengguna ini
    //     $outlets = $user->outlet;


    //     // Ambil produk yang terkait dengan outlet
    //     $produks = Produk::with('detailTransaksi')  // Assuming 'detailTransaksi' is the relationship name
    //                 ->leftJoin('detail_transaksi', 'produks.id', '=', 'detail_transaksi.id_produk')
    //                 ->select('produks.*', DB::raw('COALESCE(SUM(detail_transaksi.qty), 0) as total_sold'))
    //                 ->where('produks.id_outlet', $outlets->id)
    //                 ->groupBy('produks.id')
    //                 ->orderBy('total_sold', 'desc') 
    //                 ->orderBy('produks.nama_produk', 'asc') 
    //                 ->get();

    //     // Group products by category
    //     $groupedProduks = $produks->groupBy('id_kategori');

    //     return view('user.dashboard', [
    //         'Outlet' => $outlets,
    //         'User' => $user,
    //         'Produk' => $produks,
    //         'groupedProduks' => $groupedProduks,
    //     ]);
    // }


    public function menuView()
    {
        // Retrieve the authenticated user
        $user = auth()->user();
        
        // Get the outlets associated with this user
        $outlets = $user->outlet;
        return view('user.menu', [
             'Outlet' => $outlets,
             'User' => $user,
        ]);
    }

    public function stokView()
    {
        $user = auth()->user();

        // Dapatkan outlets yang terkait dengan pengguna ini
        $outlets = $user->outlet;


        // Ambil produk yang terkait dengan outlet
        $produks = Produk::where('id_outlet', $outlets->id)->get();

        return view('user.stok', [
            'Outlet' => $outlets,
            'User' => $user,
            'Produk' => $produks,
        ]);
    }

    public function verifyPin(Request $request, $id)
    {
        // Validasi input PIN
        $request->validate([
            'pin' => 'required|array|min:4|max:4',
            'pin.*' => 'numeric|min:0|max:9'
        ]);

        // Gabungkan PIN menjadi satu string
        $pin = implode('', $request->pin);

        // Ambil outlet berdasarkan ID
        $outlet = Outlet::findOrFail($id);

        // Verifikasi PIN
        if ($outlet->pin === $pin) {
            // Ambil pengguna yang terautentikasi
            $user = auth()->user();

            // Cek status user
            if ($user->status === 'Bekerja') {
                // Jika status user "Bekerja", arahkan ke halaman dashboard
                return redirect()->route('users.dashboard');
            } else {
                // Jika status bukan "Bekerja", tampilkan pesan kesalahan
                return back()->withErrors(['status' => 'Anda tidak sedang dalam status bekerja.'])->withInput();
            }
        } else {
            // PIN salah, kembalikan ke form dengan pesan error
            return back()->withErrors(['pin' => 'PIN yang Anda masukkan salah.'])->withInput();
        }
    }


    // TRANSAKSI DARI ORDER
    public function updateTransaction(Request $request)
    {
        $validated = $request->validate([
            'orderId' => 'required|integer',
            'totalAmount' => 'required|numeric',
            'payment' => 'required|numeric',
            'change' => 'required|numeric',
            'details' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            // Update order status
            $order = Order::findOrFail($validated['orderId']);
            $order->status = 'Success'; // or other status as required
            $order->save();

            // Create new transaction
            $transaction = new Transaksi([
                'resi' => $order->resi,
                'tanggal_transaksi' => now(),
                'total_qty' => $order->total_qty,
                'total_belanja' => $validated['totalAmount'],
                'id_outlet' => $order->id_outlet,
                'status' => 'Selesai', // or other status as required
            ]);
            $transaction->save();

            // Add detail transactions
            foreach ($validated['details'] as $detail) {
                Detail_Transaksi::create([
                    'id_transaksi' => $transaction->id,
                    'id_produk' => $detail['id_produk'], // Ensure this key matches the key from the request
                    'qty' => $detail['qty'],
                    'subtotal' => $detail['subtotal'],
                ]);
            }

            DB::commit();

            // Return success response
            return response()->json(['success' => true, 'message' => 'Transaction updated successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            // Return error response
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ================================ PEMILIK =================================//

    public function indexOutlet()
    {
        $OutletId = auth()->user()->id_outlet;

        $perPage = request()->get('per_page', 10);

        $transaksi = Transaksi::where('id_outlet', $OutletId)
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);
        
        return view('pemilik.transaksi.transaksi', [
             'Transaksi' => $transaksi,
        ]);
    }

    public function edit($resi)
    {
        $OutletId = auth()->user()->id_outlet;

        // Fetch the transaction and its details
        $transaksi = Transaksi::with('details.produk')->where('resi', $resi)->where('id_outlet', $OutletId)->firstOrFail();

        // Ambil ID produk yang sudah ada di dalam transaksi
        $produkIdsInTransaksi = $transaksi->details->pluck('produk.id');

        // Ambil semua produk yang belum ada dalam transaksi
        $produksYangBelumAda = Produk::where('id_outlet', $OutletId)
            ->whereNotIn('id', $produkIdsInTransaksi)
            ->get();

        // Ambil semua produk tanpa filter
        $semuaProduks = Produk::where('id_outlet', $OutletId)->get();

        
        
        return view('pemilik.transaksi.edit-transaksi', [
            'transaksi' => $transaksi,
            'detailTransaksi' => $transaksi->details,
            'semuaProduks' => $semuaProduks,
            'produksYangBelumAda' => $produksYangBelumAda,
        ]);
        
    }
}
