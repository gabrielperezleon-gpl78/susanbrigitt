<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function index(): View
    {
        $purchases = Purchase::query()
            ->with([
                'supplier',
                'exchangeRate',
                'items.product',
            ])
            ->latest()
            ->get();

        $totalPurchases = $purchases->count();

        $totalUnits = $purchases->sum(function ($purchase) {
            return $purchase->items->sum('quantity');
        });

        $totalUsd = $purchases->sum(function ($purchase) {
            return (float) ($purchase->total_usd ?? 0);
        });

        $totalBs = $purchases->sum(function ($purchase) {
            return (float) ($purchase->total_bs ?? 0);
        });

        $averageRate = $purchases
            ->filter(fn($purchase) => ! is_null($purchase->exchange_rate_value))
            ->avg('exchange_rate_value');

        return view('purchases.index', compact(
            'purchases',
            'totalPurchases',
            'totalUnits',
            'totalUsd',
            'totalBs',
            'averageRate'
        ));
    }
}
