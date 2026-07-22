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
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(): View
    {
        $sales = Sale::query()
            ->with([
                'items.product.unitMeasure',
                'exchangeRate',
            ])
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
            ->with('unitMeasure')
            ->where('status', 'active')
            ->where('current_stock', '>', 0)
            ->orderBy('name')
            ->get();

        $latestExchangeRate = $this->latestExchangeRate();

        return view('sales.create', compact(
            'products',
            'latestExchangeRate'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'unit_price_usd' => $this->normalizeDecimal(
                $request->input('unit_price_usd')
            ),
            'exchange_rate_value' => $this->normalizeDecimal(
                $request->input('exchange_rate_value')
            ),
        ]);

        $validated = $this->validateSale($request);

        DB::transaction(function () use ($validated) {
            $product = Product::query()
                ->lockForUpdate()
                ->findOrFail($validated['product_id']);

            $quantity = (int) $validated['quantity'];

            if ((int) $product->current_stock < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'No hay stock suficiente para registrar esta venta.',
                ]);
            }

            $unitPriceUsd = round((float) $validated['unit_price_usd'], 2);
            $unitCostUsd = round((float) $product->purchase_price_usd, 2);
            $unitProfitUsd = round($unitPriceUsd - $unitCostUsd, 2);

            $totalUsd = round($quantity * $unitPriceUsd, 2);
            $totalProfitUsd = round($quantity * $unitProfitUsd, 2);

            $exchangeRateValue = round(
                (float) $validated['exchange_rate_value'],
                4
            );

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

    public function edit(Sale $sale): View
    {
        $sale->load([
            'items.product.unitMeasure',
            'exchangeRate',
        ]);

        $saleItem = $sale->items->first();

        abort_if(
            ! $saleItem,
            404,
            'La venta no tiene productos asociados.'
        );

        $products = Product::query()
            ->with('unitMeasure')
            ->where(function ($query) use ($saleItem) {
                $query
                    ->where(function ($activeQuery) {
                        $activeQuery
                            ->where('status', 'active')
                            ->where('current_stock', '>', 0);
                    })
                    ->orWhere('id', $saleItem->product_id);
            })
            ->orderBy('name')
            ->get();

        $latestExchangeRate = $this->latestExchangeRate();

        return view('sales.edit', compact(
            'sale',
            'saleItem',
            'products',
            'latestExchangeRate'
        ));
    }

    public function update(Request $request, Sale $sale): RedirectResponse
    {
        $request->merge([
            'unit_price_usd' => $this->normalizeDecimal(
                $request->input('unit_price_usd')
            ),
            'exchange_rate_value' => $this->normalizeDecimal(
                $request->input('exchange_rate_value')
            ),
        ]);

        $validated = $this->validateSale($request);

        DB::transaction(function () use ($validated, $sale) {
            $lockedSale = Sale::query()
                ->whereKey($sale->id)
                ->lockForUpdate()
                ->firstOrFail();

            $saleItem = SaleItem::query()
                ->where('sale_id', $lockedSale->id)
                ->lockForUpdate()
                ->firstOrFail();

            $oldProductId = (int) $saleItem->product_id;
            $newProductId = (int) $validated['product_id'];

            $oldQuantity = (int) $saleItem->quantity;
            $newQuantity = (int) $validated['quantity'];

            $unitPriceUsd = round(
                (float) $validated['unit_price_usd'],
                2
            );

            if ($oldProductId === $newProductId) {
                $product = Product::query()
                    ->whereKey($oldProductId)
                    ->lockForUpdate()
                    ->firstOrFail();

                $correctedStock = (int) $product->current_stock
                    + $oldQuantity
                    - $newQuantity;

                if ($correctedStock < 0) {
                    throw ValidationException::withMessages([
                        'quantity' => 'No hay stock suficiente para aplicar la nueva cantidad de la venta.',
                    ]);
                }

                $unitCostUsd = round(
                    (float) $saleItem->unit_cost_usd,
                    2
                );

                if ($unitCostUsd <= 0) {
                    $unitCostUsd = round(
                        (float) $product->purchase_price_usd,
                        2
                    );
                }

                $product->current_stock = $correctedStock;
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
                        'product_id' => 'No fue posible localizar los productos de la corrección.',
                    ]);
                }

                if ((int) $newProduct->current_stock < $newQuantity) {
                    throw ValidationException::withMessages([
                        'quantity' => 'El nuevo producto seleccionado no tiene stock suficiente.',
                    ]);
                }

                $oldProduct->current_stock =
                    (int) $oldProduct->current_stock + $oldQuantity;

                $oldProduct->save();

                $newProduct->current_stock =
                    (int) $newProduct->current_stock - $newQuantity;

                $newProduct->save();

                $unitCostUsd = round(
                    (float) $newProduct->purchase_price_usd,
                    2
                );

                $movementStock = $newProduct->current_stock;
            }

            $unitProfitUsd = round(
                $unitPriceUsd - $unitCostUsd,
                2
            );

            $totalUsd = round(
                $newQuantity * $unitPriceUsd,
                2
            );

            $totalProfitUsd = round(
                $newQuantity * $unitProfitUsd,
                2
            );

            $exchangeRateValue = round(
                (float) $validated['exchange_rate_value'],
                4
            );

            $totalBs = round(
                $totalUsd * $exchangeRateValue,
                2
            );

            $lockedSale->update([
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

            $saleItem->update([
                'product_id' => $newProductId,
                'quantity' => $newQuantity,
                'unit_price_usd' => $unitPriceUsd,
                'unit_cost_usd' => $unitCostUsd,
                'unit_profit_usd' => $unitProfitUsd,
                'total_usd' => $totalUsd,
                'total_profit_usd' => $totalProfitUsd,
            ]);

            InventoryMovement::query()->updateOrCreate(
                [
                    'movementable_type' => Sale::class,
                    'movementable_id' => $lockedSale->id,
                    'type' => 'sale',
                ],
                [
                    'product_id' => $newProductId,
                    'quantity' => $newQuantity,
                    'stock_after_movement' => $movementStock,
                    'movement_date' => $validated['sale_date'],
                    'notes' => 'Salida por venta corregida.',
                ]
            );
        });

        return redirect()
            ->route('sales.index')
            ->with('success', 'Venta actualizada correctamente.');
    }

    private function validateSale(Request $request): array
    {
        return $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'sale_date' => ['required', 'date'],
            'customer_name' => ['nullable', 'string', 'max:180'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price_usd' => ['required', 'numeric', 'min:0.01'],
            'exchange_rate_id' => [
                'nullable',
                'exists:exchange_rates,id',
            ],
            'exchange_rate_value' => [
                'required',
                'numeric',
                'min:0.01',
            ],
            'rate_source' => [
                'required',
                'in:bcv,binance,manual',
            ],
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

    private function normalizeDecimal(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        if ($value === '') {
            return null;
        }

        $value = str_replace(
            ['$', 'Bs.', 'Bs', ' '],
            '',
            $value
        );

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
