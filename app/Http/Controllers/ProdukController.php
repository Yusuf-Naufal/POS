<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Outlet;
use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProdukController extends Controller
{
    public function index()
    {
        $perPage = request()->get('per_page', 10);

        // Use query builder to paginate the results
        $produk = Produk::paginate($perPage);

        return view('admin.produk.produk', [
             'Produk' => $produk,
        ]);
    }

    public function createAdmin(): View
    {
        $kategori = Kategori::all();
        $unit = Unit::all();
        $outlet = Outlet::all();
        return view('admin.produk.add-produk', [
            'Kategori' => $kategori,
            'Unit' => $unit,
            'Outlet' => $outlet,
        ]);
    }

    // public function store(Request $request)
    // {
    //     $validation = Validator::make($request->all(), [
    //         'nama_produk' => 'required|string|max:255',
    //         'sku' => 'required|string|max:255|unique:produks,sku',
    //         'id_kategori' => 'required|exists:kategoris,id',
    //         'id_unit' => 'required|exists:units,id',
    //         'harga_jual' => 'required|numeric|min:0',
    //         'harga_modal' => 'required|numeric|min:0',
    //         'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif',
    //     ]);

    //     if ($validation->fails()) {
    //         return back()->withErrors($validation);
    //     }

    //     $foto = time() . '.' . $request->foto->extension();
    //     $request->foto->storeAs('public/assets/produk', $foto);

    //     $produk = new Produk();
    //     $produk->nama_produk = $request->nama_produk;
    //     $produk->sku = $request->sku;
    //     $produk->id_kategori = $request->id_kategori;
    //     $produk->id_unit = $request->id_unit;
    //     $produk->stok_awal = $request->stok_awal;
    //     $produk->stok_minimum = $request->stok_minimum;
    //     $produk->harga_jual = $request->harga_jual;
    //     $produk->harga_modal = $request->harga_modal;
    //     $produk->catatan = $request->catatan;
    //     $produk->foto = 'produk/' . $foto;
    //     $produk->status = $request->has('status') ? 'Aktif' : 'Habis';
        
    //     $produk->save();

    //     // If 'Tersedia di POS' is checked and outlets are selected
    //     if ($request->has('id_outlet') && is_array($request->id_outlet)) {
    //         $cabangData = [];

    //         foreach ($request->id_outlet as $outletId) {
    //             $cabangData[] = [
    //                 'id_produk' => $produk->id,
    //                 'id_outlet' => $outletId,
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ];
    //         }

    //         // Insert multiple records at once
    //         Cabang::insert($cabangData);
    //     }
        
    //     return redirect()->route('produks.index')->with('success', 'Produk added successfully.');

    //     // dd($request->all());
    // }

    
    public function storeKategori(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        Kategori::create([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function storeUnit(Request $request)
    {
        $request->validate([
            'nama_unit' => 'required|string|max:255',
        ]);

        Unit::create([
            'nama_unit' => $request->nama_unit,
        ]);

        return redirect()->back()->with('success', 'Unit berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $produk = Produk::findOrFail($id);
        $unit = Unit::all();
        $outlet = Outlet::all();
        $kategori = Kategori::all();
        
        return view('admin.produk.edit-produk', [
            'produk' => $produk,
            'Unit' => $unit,
            'Outlet' => $outlet,
            'Kategori' => $kategori,
        ]);
    }

    public function update(Request $request, $id)
    {
        // Validasi permintaan
        $validation = Validator::make($request->all(), [
            'nama_produk' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:produks,sku,' . $id,
            'id_kategori' => 'required|exists:kategoris,id',
            'id_unit' => 'required|exists:units,id',
            'harga_jual' => 'required|numeric|min:0',
            'harga_modal' => 'required|numeric|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        if ($validation->fails()) {
            return back()->withErrors($validation)->withInput();
        }

        $produk = Produk::findOrFail($id);

        // Jika ada foto baru yang diunggah
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($produk->foto && file_exists(public_path('assets/' . $produk->foto))) {
                // Menghapus file yang ada di path public
                unlink(public_path('assets/' . $produk->foto));
            }

            // Simpan foto baru
            $foto = time() . '.' . $request->foto->extension();
            $request->foto->move(public_path('assets/produk'), $foto); // Simpan ke folder public/assets/produk
            $produk->foto = 'produk/' . $foto; // Simpan jalur foto ke database
        }


        // Perbarui atribut produk
        $produk->nama_produk = $request->nama_produk;
        $produk->sku = $request->sku;
        $produk->id_kategori = $request->id_kategori;
        $produk->id_unit = $request->id_unit;
        $produk->stok_awal = $request->stok_awal;
        $produk->stok_minimum = $request->stok_minimum;
        $produk->harga_jual = $request->harga_jual;
        $produk->harga_modal = $request->harga_modal;
        $produk->catatan = $request->catatan;
        $produk->status = $request->status;
        $produk->save();

        return redirect()->route('produks.index')->with('success', 'Produk updated successfully!');
        // return $request;
    }

    public function destroy($id)
    {
        // Find the product by ID
        $produk = Produk::findOrFail($id);

        // If the product has a photo, delete it from storage
        if ($produk->foto && Storage::exists('public/assets/' . $produk->foto)) {
            Storage::delete('public/assets/' . $produk->foto);
        }

        // Delete the product
        $produk->delete();

        // Redirect to the product list for the specified outlet
        if (auth()->user()->role === 'Admin') {
            return redirect()->route('produks.index')->with('success', 'Produk deleted successfully!');
        }else{
            return redirect()->route('master.produk.index')->with('success', 'Produk deleted successfully!');
        }
    }

    public function deactivate($id)
    {
        $produk = Produk::find($id);

        if ($produk) {
            $produk->status = 'Habis'; // Change status to 'Inactive' or however you represent deactivation
            $produk->save();
            return redirect()->route('produks.index')->with('success', 'Produk status updated to Inactive.');
        }

        return redirect()->route('produks.index')->with('error', 'Produk not found.');
    }
    public function activate($id)
    {
        $produk = Produk::find($id);

        if ($produk) {
            $produk->status = 'Aktif'; // Change status back to 'Active'
            $produk->save();
            return redirect()->route('produks.index')->with('success', 'Produk status updated to Active.');
        }

        return redirect()->route('produks.index')->with('error', 'Produk not found.');
    }

    protected function generateSKU($length = 13)
    {
        // Characters to use in SKU
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $sku = '';
        $maxLength = min($length, 13); // Ensure length does not exceed 13 characters

        for ($i = 0; $i < $maxLength; $i++) {
            $sku .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $sku;
    }

    public function checkSku(Request $request)
    {
        $request->validate([
            'sku' => 'required|string|max:255', // Validate SKU input
        ]);

        $sku = $request->sku;
        $exists = Produk::where('sku', $sku)->exists(); // Check if SKU exists

        return response()->json(['exists' => $exists]);
    }


    // ===================================================  PRODUCT TERSENDIRI ========================================================== // 
    public function showProducts($id)
    {
        // Retrieve the outlet by id
        $outlet = Outlet::findOrFail($id);

        $perPage = request()->get('per_page', 10);

        // Retrieve only products that belong to this outlet
        $produks = Produk::where('id_outlet', $outlet->id)
        ->paginate($perPage);

        // Pass the outlet and its products to the view
        return view('admin.produk.produk-outlet', compact('outlet', 'produks'));
    }

    public function createProducts($outletId)
    {
        $outlet = Outlet::findOrFail($outletId);
        $kategoris = Kategori::all();
        $units = Unit::all();

        
        // Pass the outlet to the view so you can associate the product with it
        return view('admin.produk.add-produk-outlet', compact('outlet', 'kategoris', 'units'));
    }

    public function editProducts($outletId, $id)
    {
        $produk = Produk::findOrFail($id);
        $unit = Unit::all();
        $kategori = Kategori::all();
        $outlet = Outlet::findOrFail($outletId); // Use the outlet ID from the route parameter
        
        return view('admin.produk.edit-produk-outlet', [
            'produk' => $produk,
            'Unit' => $unit,
            'Outlet' => $outlet,
            'Kategori' => $kategori,
        ]);
    }

    public function storeProducts(Request $request){
        $validation = Validator::make($request->all(), [
            'nama_produk' => 'required|string|max:255',
            'id_kategori' => 'required|exists:kategoris,id',
            'id_unit' => 'required|exists:units,id',
            'id_outlet' => 'required|exists:outlet,id',
            'harga_jual' => 'required|numeric|min:0',
            'harga_modal' => 'required|numeric|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        if ($validation->fails()) {
            return back()->withErrors($validation);
        }

        $foto = null; // Inisialisasi foto dengan null
        if ($request->hasFile('foto')) {
            // Buat nama file unik
            $foto = time() . '.' . $request->foto->extension();

            // Pindahkan file ke folder public/assets/produk
            $request->foto->move(public_path('assets/produk'), $foto);
        }

        $sku = $request->sku ? $request->sku : $this->generateSKU();

        $produk = new Produk();
        $produk->nama_produk = $request->nama_produk;
        $produk->sku = $sku;
        $produk->id_kategori = $request->id_kategori;
        $produk->id_unit = $request->id_unit;
        $produk->id_outlet = $request->id_outlet;
        $produk->stok_awal = $request->stok_awal;
        $produk->stok_minimum = $request->stok_minimum;
        $produk->harga_jual = $request->harga_jual;
        $produk->harga_modal = $request->harga_modal;
        $produk->catatan = $request->catatan;
        $produk->foto = $foto ? 'produk/' . $foto : null;
        $produk->status = $request->status;
        
        $produk->save();

        return redirect()->route('outlets.products', $produk->id_outlet)->with('success', 'Produk added successfully.');
    }

    public function updateProducts(Request $request, $outletId, $id)
    {
        // Validasi permintaan
        $validation = Validator::make($request->all(), [
            'nama_produk' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:produks,sku,' . $id,
            'id_kategori' => 'required|exists:kategoris,id',
            'id_unit' => 'required|exists:units,id',
            'harga_jual' => 'required|numeric|min:0',
            'harga_modal' => 'required|numeric|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        if ($validation->fails()) {
            return back()->withErrors($validation)->withInput();
        }

        $produk = Produk::findOrFail($id);

        // Jika ada foto baru yang diunggah
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($produk->foto && file_exists(public_path('assets/' . $produk->foto))) {
                // Menghapus file yang ada di path public
                unlink(public_path('assets/' . $produk->foto));
            }

            // Simpan foto baru
            $foto = time() . '.' . $request->foto->extension();
            $request->foto->move(public_path('assets/produk'), $foto); // Simpan ke folder public/assets/produk
            $produk->foto = 'produk/' . $foto; // Simpan jalur foto ke database
        }


        // Perbarui atribut produk
        $produk->nama_produk = $request->nama_produk;
        $produk->sku = $request->sku;
        $produk->id_kategori = $request->id_kategori;
        $produk->id_unit = $request->id_unit;
        $produk->stok_awal = $request->stok_awal;
        $produk->stok_minimum = $request->stok_minimum;
        $produk->harga_jual = $request->harga_jual;
        $produk->harga_modal = $request->harga_modal;
        $produk->catatan = $request->catatan;
        $produk->status = $request->status;
        $produk->save();

        // Redirect to the products list for the specified outlet
        return redirect()->route('outlets.products', ['outlet' => $outletId])->with('success', 'Produk updated successfully!');
    }

    public function destroyProducts($outletId, $id)
    {
        // Find the product by ID
        $produk = Produk::findOrFail($id);

        if ($produk->foto && Storage::exists('public/assets/' . $produk->foto)) {
            Storage::delete('public/assets/' . $produk->foto);
        }

        // Delete the product
        $produk->delete();

        // Redirect to the product list for the specified outlet
        return redirect()->route('outlets.products', ['outlet' => $outletId])
                        ->with('success', 'Produk deleted successfully!');
    }

    public function deactivateProducts($outletId, $id)
    {
        $produk = Produk::find($id);

        if ($produk) {
            $produk->status = 'Habis'; // Change status to 'Inactive' or however you represent deactivation
            $produk->save();
            return redirect()->route('outlets.products', $outletId)->with('success', 'Produk status updated to Inactive.');
        }

        return redirect()->route('outlets.products', $outletId)->with('error', 'Produk not found.');
    }

    public function activateProducts($outletId, $id)
    {
        $produk = Produk::find($id);

        if ($produk) {
            $produk->status = 'Aktif'; // Change status back to 'Active'
            $produk->save();
            return redirect()->route('outlets.products', $outletId)->with('success', 'Produk status updated to Active.');
        }

        return redirect()->route('outlets.products', $outletId)->with('error', 'Produk not found.');
    }

    public function updateStatus(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        // Update the product status
        $produk->status = $request->status;
        $produk->save();

        // Return a JSON response
        return response()->json(['success' => true]);
    }

    // ================================================= MASTER ====================================================== //

    public function produkMaster()
    {
        // Retrieve the id_outlet of the authenticated user
        $outletId = auth()->user()->id_outlet;

        $perPage = request()->get('per_page', 10);

        // Fetch products belonging to the user's outlet and paginate the results
        $produk = Produk::where('id_outlet', $outletId)
                        ->orderBy('created_at', 'desc')
                        ->paginate($perPage);  // Adjust the number to your preferred items per page

        // Pass the paginated products to the view
        return view('pemilik.produk.produk', compact('produk'));
    }


    public function createMaster()
    {
        $kategoris = Kategori::all();
        $units = Unit::all();

        // Pass the outlet to the view so you can associate the product with it
        return view('pemilik.produk.add-produk', compact( 'kategoris', 'units'));
    }

    public function storeMaster(Request $request){
        $validation = Validator::make($request->all(), [
            'nama_produk' => 'required|string|max:255',
            'id_kategori' => 'required|exists:kategoris,id',
            'id_unit' => 'required|exists:units,id',
            'id_outlet' => 'required|exists:outlet,id',
            'harga_jual' => 'required|numeric|min:0',
            'harga_modal' => 'required|numeric|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        if ($validation->fails()) {
            return back()->withErrors($validation);
        }

        $foto = null; // Inisialisasi foto dengan null
        if ($request->hasFile('foto')) {
            // Buat nama file unik
            $foto = time() . '.' . $request->foto->extension();

            // Pindahkan file ke folder public/assets/produk
            $request->foto->move(public_path('assets/produk'), $foto);
        }

        $sku = $request->sku ? $request->sku : $this->generateSKU();

        $produk = new Produk();
        $produk->nama_produk = $request->nama_produk;
        $produk->sku = $sku;
        $produk->id_kategori = $request->id_kategori;
        $produk->id_unit = $request->id_unit;
        $produk->id_outlet = $request->id_outlet;
        $produk->stok_awal = $request->stok_awal;
        $produk->stok_minimum = $request->stok_minimum;
        $produk->harga_jual = $request->harga_jual;
        $produk->harga_modal = $request->harga_modal;
        $produk->catatan = $request->catatan;
        $produk->foto = $foto ? 'produk/' . $foto : null;
        $produk->status = $request->status;
        
        $produk->save();

        return redirect()->route('master.produk.index', $produk->id_outlet)->with('success', 'Produk Berhasil Ditambah!!');
    }

    public function editMaster($id)
    {
        // Get the authenticated user's id_outlet
        $userOutletId = auth()->user()->id_outlet;

        // Find the product by ID and ensure it belongs to the user's outlet
        $produk = Produk::where('id_outlet', $userOutletId)->findOrFail($id);

        // Retrieve units and categories relevant to the user's outlet
        $unit = Unit::all();
        $kategori = Kategori::all();

        return view('pemilik.produk.edit-produk', [
            'produk' => $produk,
            'Unit' => $unit,
            'Kategori' => $kategori,
        ]);
    }

    public function updateMaster(Request $request, $id)
    {
        // Validasi permintaan
        $validation = Validator::make($request->all(), [
            'nama_produk' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:produks,sku,' . $id,
            'id_kategori' => 'required|exists:kategoris,id',
            'id_unit' => 'required|exists:units,id',
            'harga_jual' => 'required|numeric|min:0',
            'harga_modal' => 'required|numeric|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        if ($validation->fails()) {
            return back()->withErrors($validation)->withInput();
        }

        $produk = Produk::findOrFail($id);

        // Jika ada foto baru yang diunggah
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($produk->foto && file_exists(public_path('assets/' . $produk->foto))) {
                // Menghapus file yang ada di path public
                unlink(public_path('assets/' . $produk->foto));
            }

            // Simpan foto baru
            $foto = time() . '.' . $request->foto->extension();
            $request->foto->move(public_path('assets/produk'), $foto); // Simpan ke folder public/assets/produk
            $produk->foto = 'produk/' . $foto; // Simpan jalur foto ke database
        }


        // Perbarui atribut produk
        $produk->nama_produk = $request->nama_produk;
        $produk->sku = $request->sku;
        $produk->id_kategori = $request->id_kategori;
        $produk->id_unit = $request->id_unit;
        $produk->stok_awal = $request->stok_awal;
        $produk->stok_minimum = $request->stok_minimum;
        $produk->harga_jual = $request->harga_jual;
        $produk->harga_modal = $request->harga_modal;
        $produk->catatan = $request->catatan;
        $produk->status = $request->status;
        $produk->save();

        // Redirect to the products list for the specified outlet
        return redirect()->route('master.produk.index')->with('success', 'Produk Berhasil Diupdate!!');
    }

    public function deactivateMaster($id)
    {
        $produk = Produk::find($id);

        if ($produk) {
            $produk->status = 'Habis'; // Change status to 'Inactive' or however you represent deactivation
            $produk->save();
            return redirect()->route('master.produk.index')->with('success', 'Produk Dinon-aktifkan');
        }

        return redirect()->route('master.produk.index')->with('error', 'Produk not found.');
    }

    public function activateMaster($id)
    {
        $produk = Produk::find($id);

        if ($produk) {
            $produk->status = 'Aktif'; // Change status back to 'Active'
            $produk->save();
            return redirect()->route('master.produk.index')->with('success', 'Produk Diaktifkan');
        }

        return redirect()->route('master.produk.index')->with('error', 'Produk not found.');
    }

    public function produkFavorit(Request $request)
    {
        $user = auth()->user();
        $outlet = $user->outlet;

        // Get all categories for the dropdown
        $kategoris = Kategori::all();

        // Get the selected category from the request
        $selectedKategori = $request->input('kategori');

        // Get the top products for each category (food and drink) for today
        $topProducts = DB::table('detail_transaksi')
            ->join('transaksi', 'detail_transaksi.id_transaksi', '=', 'transaksi.id')
            ->join('produks', 'detail_transaksi.id_produk', '=', 'produks.id')
            ->join('kategoris', 'produks.id_kategori', '=', 'kategoris.id')
            ->select('produks.id', 'produks.nama_produk', 'kategoris.nama_kategori as kategori', DB::raw('SUM(detail_transaksi.qty) as total_qty'))
            ->where('transaksi.id_outlet', $outlet->id)
            ->groupBy('produks.id', 'produks.nama_produk', 'kategoris.nama_kategori')
            ->orderBy('total_qty', 'desc')
            ->get();

        // Separate products into food and drink without limiting the number
        $foodProducts = $topProducts->filter(function ($product) {
            return $product->kategori === 'Makanan'; // Adjust according to your category names
        })->take(5);

        $drinkProducts = $topProducts->filter(function ($product) {
            return $product->kategori === 'Minuman'; // Adjust according to your category names
        })->take(5);

        $terjual = Produk::with(['units', 'kategoris'])
            ->leftJoin('detail_transaksi', 'produks.id', '=', 'detail_transaksi.id_produk')
            ->leftJoin('transaksi', function ($join) use ($outlet) {
                $join->on('detail_transaksi.id_transaksi', '=', 'transaksi.id')
                     ->where('transaksi.id_outlet', '=', $outlet->id);
            })
            ->select('produks.id', 'produks.nama_produk', 'produks.harga_jual', DB::raw('COALESCE(SUM(detail_transaksi.qty), 0) as total_qty'))
            ->groupBy('produks.id', 'produks.nama_produk', 'produks.harga_jual')
            ->orderBy('total_qty', 'desc')
            ->get();

        // Filter by selected category if applicable
        if ($selectedKategori) {
            $terjual = $terjual->where('id_kategori', $selectedKategori);
        }

        return view('pemilik.produk.favorit', [
            'Outlet' => $outlet,
            'foodProducts' => $foodProducts,
            'drinkProducts' => $drinkProducts,
            'terjual' => $terjual,
            'kategoris' => $kategoris, // Pass categories to the view
            'selectedKategori' => $selectedKategori, // Pass selected category to the view
        ]);
    }



}
