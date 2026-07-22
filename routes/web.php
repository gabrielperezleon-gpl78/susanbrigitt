<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ExchangeRateController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CatalogController;

Route::view('/', 'welcome')->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/productos', [ProductController::class, 'index'])->name('products.index');

Route::get('/productos/nuevo', [ProductController::class, 'create'])->name('products.create');

Route::post('/productos', [ProductController::class, 'store'])->name('products.store');

Route::get('/inventario', [InventoryController::class, 'index'])->name('inventory.index');

Route::get('/compras', [PurchaseController::class, 'index'])->name('purchases.index');

Route::get('/compras/nueva', [PurchaseController::class, 'create'])->name('purchases.create');

Route::post('/compras', [PurchaseController::class, 'store'])->name('purchases.store');

Route::get('/ventas', [SaleController::class, 'index'])->name('sales.index');

Route::get('/ventas/nueva', [SaleController::class, 'create'])->name('sales.create');

Route::post('/ventas', [SaleController::class, 'store'])->name('sales.store');

Route::get('/tasas', [ExchangeRateController::class, 'index'])->name('exchange-rates.index');

Route::get('/tasas/nueva', [ExchangeRateController::class, 'create'])->name('exchange-rates.create');

Route::post('/tasas', [ExchangeRateController::class, 'store'])->name('exchange-rates.store');

Route::get('/catalogos', [CatalogController::class, 'index'])->name('catalogs.index');

Route::post('/catalogos/proveedores', [CatalogController::class, 'storeSupplier'])->name('catalogs.suppliers.store');

Route::post('/catalogos/marcas', [CatalogController::class, 'storeBrand'])->name('catalogs.brands.store');

Route::post('/catalogos/unidades', [CatalogController::class, 'storeUnitMeasure'])->name('catalogs.unit-measures.store');

Route::view('/reportes', 'reports.index')->name('reports.index');
