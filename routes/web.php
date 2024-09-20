<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransaksiController;

Route::get('/', function () {
    return view('login');
});

// ================================== KASIR ===================================== //
// FITUR LOGIN
Route::get('/login', [AuthController::class, 'loginView'])->name('loginForm');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'registerView'])->name('registerForm');
Route::post('/register', [AuthController::class, 'register'])->name('register');

//FITUR TRANSAKSI
Route::get('/menu', [TransaksiController::class, 'menuView'])->name('users.menu');
Route::post('/verify-pin/{outlet}', [TransaksiController::class, 'verifyPin'])->name('verify-pin');
Route::get('/kasir', [DashboardController::class, 'KasirDashboard'])->name('users.dashboard');
Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi');

//FITUR STOK
Route::get('/stok', [TransaksiController::class, 'stokView'])->name('users.stok');
Route::patch('/products/{id}/status', [ProdukController::class, 'updateStatus'])->name('products.updateStatus');



//FITUR ORDERAN
Route::get('/daftar-order', [OrderController::class, 'DaftarOrder'])->name('daftar-order.index');
Route::get('/daftar-process', [OrderController::class, 'DaftarProcess'])->name('daftar-process.index');
Route::get('/riwayat-order', [OrderController::class, 'RiwayatOrder'])->name('riwayat-order.index');
Route::get('/api/data', [DataController::class, 'fetchData']);
Route::post('/orders/{id}/update', [OrderController::class, 'updateOrderStatus'])->name('orders.update');
Route::get('/filter-orders', [OrderController::class, 'filterOrders']);
Route::post('/update-transaction', [TransaksiController::class, 'updateTransaction'])->name('updateTransaction');








// ===================================== PELANGGAN ========================================= //
Route::get('/daftar-outlet', [OrderController::class, 'index'])->name('daftar-outlet.index');
Route::get('/order/{id}', [OrderController::class, 'showOrderForm'])->name('order.form')->middleware('time.access');
Route::post('/pesanan', [OrderController::class, 'store'])->name('pesanan');
Route::get('/struk-order/{id}', [OrderController::class, 'show'])->name('order.details');
Route::get('/order/{id}/pdf', [OrderController::class, 'exportPdf'])->name('order.pdf');
Route::get('/cek-order', [OrderController::class, 'cekOrder'])->name('cek.order');
Route::delete('/order/{id}', [OrderController::class, 'destroy'])->name('order.destroy');


Route::get('/error', function () {
    return view('error');
})->name('error.page');





// ============================= PEMILIK ==================================//

// MANAJEMEN DASHBOARD PEMILIK
Route::get('/master', [DashboardController::class, 'MasterDashboard'])->name('master.dashboard');
Route::get('/get-statistics', [DashboardController::class, 'getStatistics']);

// MANAJEMEN KARYAWAN
Route::get('/karyawan', [AuthController::class, 'indexMaster'])->name('master.users.index');
Route::get('/karyawan/create', [AuthController::class, 'createKaryawan'])->name('master.users.create');
Route::post('/karyawan', [AuthController::class, 'storeKaryawan'])->name('master.users.store');
Route::get('/karyawan/{id}/edit', [AuthController::class, 'editKaryawan'])->name('master.users.edit');
Route::put('/karyawan/{id}', [AuthController::class, 'updateKaryawan'])->name('master.users.update');
Route::patch('/karyawan/{id}/deactivate', [AuthController::class, 'deactivateKaryawan'])->name('master.users.deactivate'); 
Route::patch('/karyawan/{id}/activate', [AuthController::class, 'activateKaryawan'])->name('master.users.activate');
Route::delete('/karyawan/{id}', [AuthController::class, 'destroy'])->name('users.destroy');

// MANAJEMEN TRANSAKSI
Route::get('/outlet-transaksi', [TransaksiController::class, 'indexOutlet'])->name('master.transaksi.index');
Route::get('/transaksi/{resi}/edit', [TransaksiController::class, 'edit'])->name('transaksi.edit');
Route::put('/transaksi/{id}', [TransaksiController::class, 'update'])->name('transaksi.update');





// -------------------------------- ADMIN --------------------------------- // 
Route::get('/admin', function () {
    return view('admin.dashboard');
})->name('dashboard.admin');

// MANAJEMEN USER
Route::get('/users', [AuthController::class, 'indexAdmin'])->name('admin.users.index');
Route::get('/users/create', [AuthController::class, 'createPemilik'])->name('admin.users.create');
Route::post('/users', [AuthController::class, 'storePemilik'])->name('admin.users.store');
Route::patch('/users/{id}/deactivate', [AuthController::class, 'deactivatePemilik'])->name('admin.users.deactivate'); 
Route::patch('/users/{id}/activate', [AuthController::class, 'activatePemilik'])->name('admin.users.activate');
Route::get('/users/{id}/edit', [AuthController::class, 'editPemilik'])->name('admin.users.edit');
Route::put('/users/{id}', [AuthController::class, 'updatePemilik'])->name('admin.users.update');

// MANAJEMEN OUTLET 
Route::get('/outlets', [OutletController::class, 'index'])->name('outlets.index');
Route::get('/outlets/create', [OutletController::class, 'create'])->name('outlets.create');
Route::post('/outlets', [OutletController::class, 'store'])->name('outlets.store');
Route::get('/outlets/{id}/edit', [OutletController::class, 'edit'])->name('outlets.edit');
Route::put('/outlets/{id}', [OutletController::class, 'update'])->name('outlets.update');
Route::get('/outlets/autocomplete', [OutletController::class, 'autocomplete']);
Route::delete('/outlets/{id}', [OutletController::class, 'destroy'])->name('outlets.destroy');
Route::patch('/outlets/{id}/deactivate', [OutletController::class, 'deactivate'])->name('outlets.deactivate'); 
Route::patch('/outlets/{id}/activate', [OutletController::class, 'activate'])->name('outlets.activate');


// MANAJEMEN PRODUK OUTLET PEMILIK
Route::prefix('outlets/{outlet}')->group(function () {
    Route::get('/products', [ProdukController::class, 'showProducts'])->name('outlets.products');
    Route::get('/products/create', [ProdukController::class, 'createProducts'])->name('outlets.products.create');
    Route::post('/products', [ProdukController::class, 'storeProducts'])->name('outlets.products.store');
    Route::get('/products/{id}/edit', [ProdukController::class, 'editProducts'])->name('outlets.products.edit');
    Route::put('/products/{id}', [ProdukController::class, 'updateProducts'])->name('outlets.products.update');
    Route::delete('/products/{id}', [ProdukController::class, 'destroyProducts'])->name('outlets.products.destroy');
    Route::patch('/products/{id}/deactivate', [ProdukController::class, 'deactivateProducts'])->name('outlets.products.deactivate');
    Route::patch('/products/{id}/activate', [ProdukController::class, 'activateProducts'])->name('outlets.products.activate');
});

// MANAJEMEN SEMUA PRODUK OUTLET
Route::get('/produks', [ProdukController::class, 'index'])->name('produks.index');
Route::get('/produks/create', [ProdukController::class, 'createAdmin'])->name('produks.create');
Route::post('/produks', [ProdukController::class, 'store'])->name('produks.store');
Route::post('/produks/kategori', [ProdukController::class, 'storeKategori'])->name('produks-kategori.store');
Route::post('/produks/unit', [ProdukController::class, 'storeUnit'])->name('produks-unit.store');
Route::get('/produks/{id}/edit', [ProdukController::class, 'edit'])->name('produks.edit');
Route::put('/produks/{id}', [ProdukController::class, 'update'])->name('produks.update');
Route::delete('/produks/{id}', [ProdukController::class, 'destroy'])->name('produks.destroy');
Route::patch('/produks/{id}/deactivate', [ProdukController::class, 'deactivate'])->name('produks.deactivate'); 
Route::patch('/produks/{id}/activate', [ProdukController::class, 'activate'])->name('produks.activate');

// MANAJEMEN TRANSAKSI
Route::get('/data-transaksi', [TransaksiController::class, 'index'])->name('data-transaksi.index');
Route::get('/detail-transaksi/{resi}', [TransaksiController::class, 'show'])->name('detail-transaksi');
Route::delete('/transaksi/{id}', [TransaksiController::class, 'destroy'])->name('transaksi.destroy');

// MANAJEMEN KATEGORI PRODUK
Route::get('/kategoris', [KategoriController::class, 'index'])->name('kategoris.index');
Route::post('/kategoris', [KategoriController::class, 'store'])->name('kategoris.store');
Route::delete('/kategoris/{id}', [KategoriController::class, 'destroy'])->name('kategoris.destroy');
Route::put('/kategoris/{id}', [KategoriController::class, 'update'])->name('kategoris.update');

// MANAJEMEN UNIT PRODUK
Route::get('/units', [UnitController::class, 'index'])->name('units.index');
Route::post('/units', [UnitController::class, 'store'])->name('units.store');
Route::delete('/units/{id}', [UnitController::class, 'destroy'])->name('units.destroy');
Route::put('/units/{id}', [UnitController::class, 'update'])->name('units.update');


