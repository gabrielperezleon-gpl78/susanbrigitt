<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::view('/dashboard', 'dashboard.index')->name('dashboard');

Route::view('/productos', 'products.index')->name('products.index');

Route::view('/productos/nuevo', 'products.create')->name('products.create');

Route::view('/inventario', 'inventory.index')->name('inventory.index');

Route::view('/compras', 'purchases.index')->name('purchases.index');

Route::view('/compras/nueva', 'purchases.create')->name('purchases.create');

Route::view('/ventas', 'sales.index')->name('sales.index');

Route::view('/ventas/nueva', 'sales.create')->name('sales.create');

Route::view('/tasas', 'exchange-rates.index')->name('exchange-rates.index');

Route::view('/tasas/nueva', 'exchange-rates.create')->name('exchange-rates.create');
