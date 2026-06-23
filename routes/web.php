<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::view('/dashboard', 'dashboard.index')->name('dashboard');

Route::view('/productos', 'products.index')->name('products.index');

Route::view('/productos/nuevo', 'products.create')->name('products.create');