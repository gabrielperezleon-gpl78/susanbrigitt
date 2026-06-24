<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::with(['category', 'brand', 'tone', 'supplier'])
            ->latest()
            ->get();

        $totalProducts = Product::count();

        $availableUnits = Product::sum('current_stock');

        $outOfStockProducts = Product::where('current_stock', '<=', 0)->count();

        $inventoryValue = Product::query()
            ->selectRaw('SUM(current_stock * purchase_price_usd) as total')
            ->value('total') ?? 0;

        return view('products.index', [
            'products' => $products,
            'totalProducts' => $totalProducts,
            'availableUnits' => $availableUnits,
            'outOfStockProducts' => $outOfStockProducts,
            'inventoryValue' => $inventoryValue,
        ]);
    }
}
