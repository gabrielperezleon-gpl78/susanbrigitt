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

        $availableUnits = Product::sum(
            'current_stock'
        );

        $lowStockProducts = Product::query()
            ->whereColumn(
                'current_stock',
                '<=',
                'minimum_stock'
            )
            ->count();

        $outOfStockProducts = Product::query()
            ->where(
                'current_stock',
                '<=',
                0
            )
            ->count();

        $inventoryValue = Product::query()
            ->selectRaw(
                'SUM(
                    current_stock *
                    purchase_price_usd
                ) as total'
            )
            ->value('total') ?? 0;

        /*
 * Solamente las compras confirmadas participan
 * en los indicadores financieros.
 */
        $totalPurchasesUsd = Purchase::confirmed()
            ->sum('total_usd');

        $totalPurchasesBs = Purchase::confirmed()
            ->sum('total_bs');

        /*
         * Solamente las ventas confirmadas participan
         * en los indicadores financieros.
         */
        $totalSalesUsd = Sale::confirmed()
            ->sum('total_usd');

        $totalSalesBs = Sale::confirmed()
            ->sum('total_bs');

        $estimatedProfitUsd = Sale::confirmed()
            ->sum('estimated_profit_usd');

        $todaySalesUsd = Sale::confirmed()
            ->whereDate(
                'sale_date',
                now()->toDateString()
            )
            ->sum('total_usd');

        $todaySalesCount = Sale::confirmed()
            ->whereDate(
                'sale_date',
                now()->toDateString()
            )
            ->count();

        $latestExchangeRate = ExchangeRate::query()
            ->where('status', 'active')
            ->orderByDesc('rate_date')
            ->orderByDesc('rate_time')
            ->orderByDesc('id')
            ->first();

        $recentProducts = Product::query()
            ->with([
                'brand',
                'tone',
            ])
            ->latest()
            ->limit(5)
            ->get();

        $recentSales = Sale::confirmed()
            ->with([
                'items.product',
            ])
            ->latest('sale_date')
            ->latest('id')
            ->limit(5)
            ->get();

        $lowStockList = Product::query()
            ->with([
                'brand',
                'tone',
            ])
            ->whereColumn(
                'current_stock',
                '<=',
                'minimum_stock'
            )
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
