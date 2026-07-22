<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function create(): View
    {
        $suppliers = Supplier::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $products = Product::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $latestExchangeRate = ExchangeRate::query()
            ->where('status', 'active')
            ->orderByDesc('rate_date')
            ->orderByDesc('id')
            ->first();

        return view('purchases.create', compact(
            'suppliers',
            'products',
            'latestExchangeRate'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'unit_cost_usd' => $this->normalizeDecimal($request->input('unit_cost_usd')),
            'exchange_rate_value' => $this->normalizeDecimal($request->input('exchange_rate_value')),
        ]);

        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'product_id' => ['required', 'exists:products,id'],
            'purchase_date' => ['required', 'date'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_cost_usd' => ['required', 'numeric', 'min:0.01'],
            'exchange_rate_id' => ['nullable', 'exists:exchange_rates,id'],
            'exchange_rate_value' => ['required', 'numeric', 'min:0.01'],
            'rate_source' => ['required', 'string', 'max:50'],
            'payment_method' => ['required', 'string', 'max:80'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::query()
                ->lockForUpdate()
                ->findOrFail($validated['product_id']);

            $quantity = (int) $validated['quantity'];
            $unitCostUsd = round((float) $validated['unit_cost_usd'], 2);
            $exchangeRateValue = round((float) $validated['exchange_rate_value'], 2);

            $totalUsd = round($quantity * $unitCostUsd, 2);
            $totalBs = round($totalUsd * $exchangeRateValue, 2);

            $purchase = Purchase::create([
                'supplier_id' => $validated['supplier_id'],
                'exchange_rate_id' => $validated['exchange_rate_id'] ?? null,
                'purchase_date' => $validated['purchase_date'],
                'total_usd' => $totalUsd,
                'exchange_rate_value' => $exchangeRateValue,
                'total_bs' => $totalBs,
                'rate_source' => $validated['rate_source'],
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
            ]);

            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_cost_usd' => $unitCostUsd,
                'total_usd' => $totalUsd,
            ]);

            $product->current_stock = (int) $product->current_stock + $quantity;
            $product->purchase_price_usd = $unitCostUsd;

            if (! is_null($product->sale_price_usd) && $product->sale_price_usd > 0) {
                $product->unit_profit_usd = round((float) $product->sale_price_usd - $unitCostUsd, 2);
                $product->profit_margin = round(($product->unit_profit_usd / (float) $product->sale_price_usd) * 100, 2);
            }

            $product->save();

            InventoryMovement::create([
                'product_id' => $product->id,
                'movementable_type' => Purchase::class,
                'movementable_id' => $purchase->id,
                'type' => 'purchase',
                'quantity' => $quantity,
                'stock_after_movement' => $product->current_stock,
                'movement_date' => $validated['purchase_date'],
                'notes' => 'Entrada por compra registrada.',
            ]);
        });

        return redirect()
            ->route('purchases.index')
            ->with('success', 'Compra registrada correctamente.');
    }

    private function normalizeDecimal(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        if ($value === '') {
            return null;
        }

        $value = str_replace(['$', 'Bs.', 'Bs', ' '], '', $value);

        $lastComma = strrpos($value, ',');
        $lastDot = strrpos($value, '.');

        if ($lastComma !== false && $lastDot !== false) {
            if ($lastComma > $lastDot) {
                $value = str_replace('.', '', $value);
                $value = str_replace(',', '.', $value);
            } else {
                $value = str_replace(',', '', $value);
            }
        } elseif ($lastComma !== false) {
            $value = str_replace(',', '.', $value);
        }

        return $value;
    }
}
