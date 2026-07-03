<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;

Route::view('/', 'welcome')->name('home');

Route::view('/dashboard', 'dashboard.index')->name('dashboard');

Route::get('/productos', [ProductController::class, 'index'])->name('products.index');

Route::view('/productos/nuevo', 'products.create')->name('products.create');

Route::get('/inventario', [InventoryController::class, 'index'])->name('inventory.index');

Route::view('/compras', 'purchases.index')->name('purchases.index');

Route::view('/compras/nueva', 'purchases.create')->name('purchases.create');

Route::view('/ventas', 'sales.index')->name('sales.index');

Route::view('/ventas/nueva', 'sales.create')->name('sales.create');

Route::view('/tasas', 'exchange-rates.index')->name('exchange-rates.index');

Route::view('/tasas/nueva', 'exchange-rates.create')->name('exchange-rates.create');

Route::view('/reportes', 'reports.index')->name('reports.index');