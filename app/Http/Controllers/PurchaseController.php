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
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function index(): View
    {
        $purchases = Purchase::query()
            ->with([
                'supplier',
                'exchangeRate',
                'items.product.unitMeasure',
            ])
            ->latest('purchase_date')
            ->latest('id')
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
            ->with('unitMeasure')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $latestExchangeRate = $this->latestExchangeRate();

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

        $validated = $this->validatePurchase($request);

        DB::transaction(function () use ($validated) {
            $product = Product::query()
                ->lockForUpdate()
                ->findOrFail($validated['product_id']);

            $quantity = (int) $validated['quantity'];
            $unitCostUsd = round((float) $validated['unit_cost_usd'], 2);
            $exchangeRateValue = round((float) $validated['exchange_rate_value'], 4);

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

            $this->applyProductCost($product, $unitCostUsd);

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

    public function edit(Purchase $purchase): View
    {
        $purchase->load([
            'supplier',
            'exchangeRate',
            'items.product.unitMeasure',
        ]);

        $purchaseItem = $purchase->items->first();

        abort_if(! $purchaseItem, 404, 'La compra no tiene productos asociados.');

        $suppliers = Supplier::query()
            ->where(function ($query) use ($purchase) {
                $query
                    ->where('is_active', true)
                    ->orWhere('id', $purchase->supplier_id);
            })
            ->orderBy('name')
            ->get();

        $products = Product::query()
            ->with('unitMeasure')
            ->where(function ($query) use ($purchaseItem) {
                $query
                    ->where('status', 'active')
                    ->orWhere('id', $purchaseItem->product_id);
            })
            ->orderBy('name')
            ->get();

        $latestExchangeRate = $this->latestExchangeRate();

        return view('purchases.edit', compact(
            'purchase',
            'purchaseItem',
            'suppliers',
            'products',
            'latestExchangeRate'
        ));
    }

    public function update(Request $request, Purchase $purchase): RedirectResponse
    {
        $request->merge([
            'unit_cost_usd' => $this->normalizeDecimal($request->input('unit_cost_usd')),
            'exchange_rate_value' => $this->normalizeDecimal($request->input('exchange_rate_value')),
        ]);

        $validated = $this->validatePurchase($request);

        DB::transaction(function () use ($validated, $purchase) {
            $lockedPurchase = Purchase::query()
                ->whereKey($purchase->id)
                ->lockForUpdate()
                ->firstOrFail();

            $purchaseItem = PurchaseItem::query()
                ->where('purchase_id', $lockedPurchase->id)
                ->lockForUpdate()
                ->firstOrFail();

            $oldProductId = (int) $purchaseItem->product_id;
            $newProductId = (int) $validated['product_id'];

            $oldQuantity = (int) $purchaseItem->quantity;
            $newQuantity = (int) $validated['quantity'];

            $unitCostUsd = round((float) $validated['unit_cost_usd'], 2);
            $exchangeRateValue = round((float) $validated['exchange_rate_value'], 4);

            $totalUsd = round($newQuantity * $unitCostUsd, 2);
            $totalBs = round($totalUsd * $exchangeRateValue, 2);

            if ($oldProductId === $newProductId) {
                $product = Product::query()
                    ->whereKey($oldProductId)
                    ->lockForUpdate()
                    ->firstOrFail();

                $correctedStock = (int) $product->current_stock
                    - $oldQuantity
                    + $newQuantity;

                if ($correctedStock < 0) {
                    throw ValidationException::withMessages([
                        'quantity' => 'No es posible reducir la compra a esa cantidad porque parte de las unidades ya fue vendida.',
                    ]);
                }

                $product->current_stock = $correctedStock;

                $this->applyProductCost($product, $unitCostUsd);

                $product->save();

                $movementStock = $correctedStock;
            } else {
                $lockedProducts = Product::query()
                    ->whereIn('id', [$oldProductId, $newProductId])
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $oldProduct = $lockedProducts->get($oldProductId);
                $newProduct = $lockedProducts->get($newProductId);

                if (! $oldProduct || ! $newProduct) {
                    throw ValidationException::withMessages([
                        'product_id' => 'No fue posible localizar los productos asociados a la corrección.',
                    ]);
                }

                $correctedOldStock = (int) $oldProduct->current_stock - $oldQuantity;

                if ($correctedOldStock < 0) {
                    throw ValidationException::withMessages([
                        'product_id' => 'No se puede cambiar el producto porque las unidades de la compra original ya fueron utilizadas o vendidas.',
                    ]);
                }

                $oldProduct->current_stock = $correctedOldStock;
                $oldProduct->save();

                $newProduct->current_stock = (int) $newProduct->current_stock + $newQuantity;

                $this->applyProductCost($newProduct, $unitCostUsd);

                $newProduct->save();

                $movementStock = $newProduct->current_stock;
            }

            $lockedPurchase->update([
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

            $purchaseItem->update([
                'product_id' => $newProductId,
                'quantity' => $newQuantity,
                'unit_cost_usd' => $unitCostUsd,
                'total_usd' => $totalUsd,
            ]);

            InventoryMovement::query()->updateOrCreate(
                [
                    'movementable_type' => Purchase::class,
                    'movementable_id' => $lockedPurchase->id,
                    'type' => 'purchase',
                ],
                [
                    'product_id' => $newProductId,
                    'quantity' => $newQuantity,
                    'stock_after_movement' => $movementStock,
                    'movement_date' => $validated['purchase_date'],
                    'notes' => 'Entrada por compra corregida.',
                ]
            );
        });

        return redirect()
            ->route('purchases.index')
            ->with('success', 'Compra actualizada correctamente.');
    }

    private function validatePurchase(Request $request): array
    {
        return $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'product_id' => ['required', 'exists:products,id'],
            'purchase_date' => ['required', 'date'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_cost_usd' => ['required', 'numeric', 'min:0.01'],
            'exchange_rate_id' => ['nullable', 'exists:exchange_rates,id'],
            'exchange_rate_value' => ['required', 'numeric', 'min:0.01'],
            'rate_source' => ['required', 'in:bcv,binance,manual'],
            'payment_method' => [
                'required',
                'in:pago_movil,transferencia_bs,efectivo_usd,binance,zelle,mixto',
            ],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    private function latestExchangeRate(): ?ExchangeRate
    {
        return ExchangeRate::query()
            ->where('status', 'active')
            ->orderByDesc('rate_date')
            ->orderByDesc('rate_time')
            ->orderByDesc('id')
            ->first();
    }

    private function applyProductCost(Product $product, float $unitCostUsd): void
    {
        $product->purchase_price_usd = $unitCostUsd;

        if (! is_null($product->sale_price_usd) && (float) $product->sale_price_usd > 0) {
            $product->unit_profit_usd = round(
                (float) $product->sale_price_usd - $unitCostUsd,
                2
            );

            $product->profit_margin = round(
                ($product->unit_profit_usd / (float) $product->sale_price_usd) * 100,
                2
            );
        }
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
