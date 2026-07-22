<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(): View
    {
        $sales = Sale::query()
            ->with(['items.product', 'exchangeRate'])
            ->latest('sale_date')
            ->latest('id')
            ->get();

        $totalSales = $sales->count();

        $totalUnits = $sales->sum(function ($sale) {
            return $sale->items->sum('quantity');
        });

        $totalUsd = $sales->sum(function ($sale) {
            return (float) ($sale->total_usd ?? 0);
        });

        $totalBs = $sales->sum(function ($sale) {
            return (float) ($sale->total_bs ?? 0);
        });

        $totalProfitUsd = $sales->sum(function ($sale) {
            return (float) ($sale->estimated_profit_usd ?? 0);
        });

        return view('sales.index', compact(
            'sales',
            'totalSales',
            'totalUnits',
            'totalUsd',
            'totalBs',
            'totalProfitUsd'
        ));
    }

    public function create(): View
    {
        $products = Product::query()
            ->where('status', 'active')
            ->where('current_stock', '>', 0)
            ->orderBy('name')
            ->get();

        $latestExchangeRate = ExchangeRate::query()
            ->where('status', 'active')
            ->orderByDesc('rate_date')
            ->orderByDesc('id')
            ->first();

        return view('sales.create', compact(
            'products',
            'latestExchangeRate'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'sale_date' => ['required', 'date'],
            'customer_name' => ['nullable', 'string', 'max:180'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price_usd' => ['required', 'numeric', 'min:0.01'],
            'exchange_rate_id' => ['nullable', 'exists:exchange_rates,id'],
            'exchange_rate_value' => ['required', 'numeric', 'min:0.01'],
            'rate_source' => ['required', 'in:bcv,binance,manual'],
            'payment_method' => ['required', 'in:pago_movil,transferencia_bs,efectivo_usd,binance,zelle,mixto'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::query()
                ->lockForUpdate()
                ->findOrFail($validated['product_id']);

            $quantity = (int) $validated['quantity'];

            if ((int) $product->current_stock < $quantity) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'quantity' => 'No hay stock suficiente para registrar esta venta.',
                ]);
            }

            $unitPriceUsd = round((float) $validated['unit_price_usd'], 2);
            $unitCostUsd = round((float) $product->purchase_price_usd, 2);
            $unitProfitUsd = round($unitPriceUsd - $unitCostUsd, 2);

            $totalUsd = round($quantity * $unitPriceUsd, 2);
            $totalProfitUsd = round($quantity * $unitProfitUsd, 2);

            $exchangeRateValue = round((float) $validated['exchange_rate_value'], 4);
            $totalBs = round($totalUsd * $exchangeRateValue, 2);

            $sale = Sale::create([
                'exchange_rate_id' => $validated['exchange_rate_id'] ?? null,
                'sale_date' => $validated['sale_date'],
                'customer_name' => $validated['customer_name'] ?? null,
                'total_usd' => $totalUsd,
                'exchange_rate_value' => $exchangeRateValue,
                'total_bs' => $totalBs,
                'estimated_profit_usd' => $totalProfitUsd,
                'rate_source' => $validated['rate_source'],
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
            ]);

            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price_usd' => $unitPriceUsd,
                'unit_cost_usd' => $unitCostUsd,
                'unit_profit_usd' => $unitProfitUsd,
                'total_usd' => $totalUsd,
                'total_profit_usd' => $totalProfitUsd,
            ]);

            $product->current_stock = (int) $product->current_stock - $quantity;
            $product->save();

            InventoryMovement::create([
                'product_id' => $product->id,
                'movementable_type' => Sale::class,
                'movementable_id' => $sale->id,
                'type' => 'sale',
                'quantity' => $quantity,
                'stock_after_movement' => $product->current_stock,
                'movement_date' => $validated['sale_date'],
                'notes' => 'Salida por venta registrada.',
            ]);
        });

        return redirect()
            ->route('sales.index')
            ->with('success', 'Venta registrada correctamente.');
    }
}
