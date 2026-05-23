<?php

use App\Http\Controllers\BerandaController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RajaOngkirController;
use Illuminate\Support\Facades\Http;

Route::get('/', function () {
    // return view('welcome');
    return redirect()->route('beranda');
});

Route::get('/cek-ongkir', function () {
    return view('ongkir');
});

Route::get('/provinces', [RajaOngkirController::class, 'getProvinces']);
Route::get('/cities', [RajaOngkirController::class, 'getCities']);
Route::post('/cost', [RajaOngkirController::class, 'getCost']);

// Route::get('/list-ongkir', function () {
// $response = Http::withHeaders(['key' => 'your_api_key'])->get('https://api.rajaongkir.com/starter/province');
// dd($response->json());
// });

// Group route untuk customer
Route::middleware('is.customer')->group(function () {

    Route::post('add-to-cart/{id}', [OrderController::class, 'addToCart'])->name('order.addToCart');

    Route::get('cart', [OrderController::class, 'viewCart'])->name('order.cart');

    Route::put('cart/update/{id}', [OrderController::class, 'updateCart'])->name('order.updateCart');

    Route::delete('cart/remove/{id}', [OrderController::class, 'removeFromCart'])->name('order.removeFromCart');

    Route::get('/provinces', [OrderController::class, 'getProvinces']);
    Route::get('/cities', [OrderController::class, 'getCities']);
    Route::post('/cost', [OrderController::class, 'getCost']);
    Route::post('/save-shipping', [OrderController::class, 'saveShipping'])->name('order.saveShipping');

    Route::post('checkout', [OrderController::class, 'checkout'])->name('order.checkout');
    Route::get('history', [OrderController::class, 'history'])->name('order.history');
    
});


// Route untuk menampilkan halaman akun customer
Route::get('/customer/akun/{id}', [CustomerController::class, 'akun'])->name('customer.akun')->middleware('is.customer');
Route::put('/customer/akun/{id}/update', [CustomerController::class, 'updateAkun'])->name('customer.akun.update')->middleware('is.customer');

// Group route untuk customer
Route::middleware('is.customer')->group(function () {
// Route untuk menampilkan halaman akun customer
Route::get('/customer/akun/{id}', [CustomerController::class, 'akun']) ->name('customer.akun');
// Route untuk mengupdate data akun customer
Route::put('/customer/updateakun/{id}', [CustomerController::class, 'updateAkun']) ->name('customer.updateakun'); });

// Route untuk Customer
Route::resource('backend/customer', CustomerController::class, ['as' => 'backend'])->middleware('auth');

//API Google
Route::get('/auth/redirect', [CustomerController::class, 'redirect'])->name('auth.redirect');
Route::get('/auth/google/callback', [CustomerController::class, 'callback'])->name('auth.callback');
// Logout
Route::post('/logout', [CustomerController::class, 'logout'])->name('customer.logout');

// Frontend
Route::get('/beranda', [BerandaController::class, 'index'])->name('beranda');
Route::post('/produk/detail/{id}', [ProdukController::class, 'detail'])->name('produk.detail');
Route::get('/produk/kategori/{id}', [ProdukController::class,
'produkKategori'])->name('produk.kategori');

Route::get('backend/beranda', [BerandaController::class, 'berandaBackend'])->name('backend.beranda')->middleware('auth');

Route::get('backend/login', [LoginController::class, 'loginBackend'])->name('backend.login');
Route::post('backend/login', [LoginController::class, 'authenticateBackend'])->name('backend.login');
Route::post('backend/logout', [LoginController::class, 'logoutBackend'])->name('backend.logout');

// Route User
Route::resource('backend/user', UserController::class, ['as' => 'backend'])->middleware('auth');
Route::get('backend/laporan/formuser', [UserController::class, 'formUser'])->name('backend.laporan.formuser')->middleware('auth');
Route::post('backend/laporan/cetakuser', [UserController::class, 'cetakUser'])->name('backend.laporan.cetakuser')->middleware('auth');

// Route Kategori
Route::resource('backend/kategori', KategoriController::class, ['as' => 'backend'])->middleware('auth');

// Route Produk
Route::resource('backend/produk', ProdukController::class, ['as' => 'backend'])->middleware('auth');
Route::post('foto-produk/store', [ProdukController::class, 'storeFoto'])->name('backend.foto_produk.store')->middleware('auth');
Route::delete('foto-produk/{id}', [ProdukController::class, 'destroyFoto'])->name('backend.foto_produk.destroy')->middleware('auth');
Route::get('backend/laporan/formproduk', [ProdukController::class, 'formProduk'])->name('backend.laporan.formproduk')->middleware('auth');
Route::post('backend/laporan/cetakproduk', [ProdukController::class, 'cetakProduk'])->name('backend.laporan.cetakproduk')->middleware('auth');
