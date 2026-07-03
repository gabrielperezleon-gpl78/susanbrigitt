<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View
    {
        $products = Product::with(['category', 'brand', 'tone', 'supplier'])
            ->orderBy('name')
            ->get();

        return view('inventory.index', [
            'products' => $products,
            'totalProducts' => Product::count(),
            'availableUnits' => Product::sum('current_stock'),
            'outOfStockProducts' => Product::where('current_stock', '<=', 0)->count(),
            'lowStockProducts' => Product::whereColumn('current_stock', '<=', 'minimum_stock')
                ->where('current_stock', '>', 0)
                ->count(),
            'inventoryValue' => Product::selectRaw('SUM(current_stock * purchase_price_usd) as total')->value('total') ?? 0,
        ]);
    }
}
