<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalProducts = Product::count();

        $availableUnits = Product::sum('current_stock');

        $lowStockProducts = Product::query()
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->count();

        $outOfStockProducts = Product::query()
            ->where('current_stock', '<=', 0)
            ->count();

        $inventoryValue = Product::query()
            ->selectRaw('SUM(current_stock * purchase_price_usd) as total')
            ->value('total') ?? 0;

        $totalPurchasesUsd = Purchase::sum('total_usd');
        $totalPurchasesBs = Purchase::sum('total_bs');

        $totalSalesUsd = Sale::sum('total_usd');
        $totalSalesBs = Sale::sum('total_bs');
        $estimatedProfitUsd = Sale::sum('estimated_profit_usd');

        $todaySalesUsd = Sale::query()
            ->whereDate('sale_date', now()->toDateString())
            ->sum('total_usd');

        $todaySalesCount = Sale::query()
            ->whereDate('sale_date', now()->toDateString())
            ->count();

        $latestExchangeRate = ExchangeRate::query()
            ->where('status', 'active')
            ->orderByDesc('rate_date')
            ->orderByDesc('id')
            ->first();

        $recentProducts = Product::query()
            ->with(['brand', 'tone'])
            ->latest()
            ->limit(5)
            ->get();

        $recentSales = Sale::query()
            ->with(['items.product'])
            ->latest('sale_date')
            ->latest('id')
            ->limit(5)
            ->get();

        $lowStockList = Product::query()
            ->with(['brand', 'tone'])
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->orderBy('current_stock')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'totalProducts',
            'availableUnits',
            'lowStockProducts',
            'outOfStockProducts',
            'inventoryValue',
            'totalPurchasesUsd',
            'totalPurchasesBs',
            'totalSalesUsd',
            'totalSalesBs',
            'estimatedProfitUsd',
            'todaySalesUsd',
            'todaySalesCount',
            'latestExchangeRate',
            'recentProducts',
            'recentSales',
            'lowStockList'
        ));
    }
}
