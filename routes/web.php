<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PurchaseController;

Route::view('/', 'welcome')->name('home');

Route::view('/dashboard', 'dashboard.index')->name('dashboard');

Route::get('/productos', [ProductController::class, 'index'])->name('products.index');

Route::view('/productos/nuevo', 'products.create')->name('products.create');

Route::get('/inventario', [InventoryController::class, 'index'])->name('inventory.index');

Route::get('/compras', [PurchaseController::class, 'index'])->name('purchases.index');

Route::get('/compras/nueva', [PurchaseController::class, 'create'])->name('purchases.create');

Route::post('/compras', [PurchaseController::class, 'store'])->name('purchases.store');

Route::view('/ventas', 'sales.index')->name('sales.index');

Route::view('/ventas/nueva', 'sales.create')->name('sales.create');

Route::view('/tasas', 'exchange-rates.index')->name('exchange-rates.index');

Route::view('/tasas/nueva', 'exchange-rates.create')->name('exchange-rates.create');

Route::view('/reportes', 'reports.index')->name('reports.index');