<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
            ->orderByDesc('sale_date')
            ->orderByDesc('id')
            ->get();

        $totalSales = $sales->count();

        $totalUnits = $sales->sum(
            fn($sale) => $sale->items->sum('quantity')
        );

        $totalUsd = $sales->sum(
            fn($sale) => (float) ($sale->total_usd ?? 0)
        );

        $totalBs = $sales->sum(
            fn($sale) => (float) ($sale->total_bs ?? 0)
        );

        $totalProfitUsd = $sales->sum(
            fn($sale) =>
            (float) ($sale->estimated_profit_usd ?? 0)
        );

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

        $exchangeRates = $this->availableExchangeRates();

        $rateChoices = $this->buildRateChoices(
            $exchangeRates
        );

        return view('sales.create', compact(
            'products',
            'rateChoices'
        ));
    }

    public function store(
        Request $request
    ): RedirectResponse {
        $request->merge([
            'unit_price_usd' => $this->normalizeDecimal(
                $request->input('unit_price_usd')
            ),
        ]);

        $validated = $this->validateSale($request);

        DB::transaction(function () use ($validated) {
            $rateSelection = $this->resolveRateSelection(
                $validated
            );

            $product = Product::query()
                ->whereKey($validated['product_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $quantity = (int) $validated['quantity'];

            if (
                (int) $product->current_stock
                < $quantity
            ) {
                throw ValidationException::withMessages([
                    'quantity' =>
                    'No hay stock suficiente para registrar esta venta.',
                ]);
            }

            $unitPriceUsd = round(
                (float) $validated['unit_price_usd'],
                2
            );

            $unitCostUsd = round(
                (float) $product->purchase_price_usd,
                2
            );

            $unitProfitUsd = round(
                $unitPriceUsd - $unitCostUsd,
                2
            );

            $totalUsd = round(
                $quantity * $unitPriceUsd,
                2
            );

            $totalProfitUsd = round(
                $quantity * $unitProfitUsd,
                2
            );

            $exchangeRateValue = round(
                $rateSelection['value'],
                4
            );

            $totalBs = round(
                $totalUsd * $exchangeRateValue,
                2
            );

            $sale = Sale::create([
                'exchange_rate_id' =>
                $rateSelection['exchange_rate']->id,
                'sale_date' =>
                $validated['sale_date'],
                'customer_name' =>
                $validated['customer_name'] ?? null,
                'total_usd' =>
                $totalUsd,
                'exchange_rate_value' =>
                $exchangeRateValue,
                'total_bs' =>
                $totalBs,
                'estimated_profit_usd' =>
                $totalProfitUsd,
                'rate_source' =>
                $rateSelection['source'],
                'payment_method' =>
                $validated['payment_method'],
                'notes' =>
                $validated['notes'] ?? null,
            ]);

            SaleItem::create([
                'sale_id' =>
                $sale->id,
                'product_id' =>
                $product->id,
                'quantity' =>
                $quantity,
                'unit_price_usd' =>
                $unitPriceUsd,
                'unit_cost_usd' =>
                $unitCostUsd,
                'unit_profit_usd' =>
                $unitProfitUsd,
                'total_usd' =>
                $totalUsd,
                'total_profit_usd' =>
                $totalProfitUsd,
            ]);

            $product->current_stock =
                (int) $product->current_stock
                - $quantity;

            $product->save();

            InventoryMovement::create([
                'product_id' =>
                $product->id,
                'movementable_type' =>
                Sale::class,
                'movementable_id' =>
                $sale->id,
                'type' =>
                'sale',
                'quantity' =>
                $quantity,
                'stock_after_movement' =>
                $product->current_stock,
                'movement_date' =>
                $validated['sale_date'],
                'notes' =>
                'Salida por venta registrada.',
            ]);
        });

        return redirect()
            ->route('sales.index')
            ->with(
                'success',
                'Venta registrada correctamente.'
            );
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
                    ->orWhere(
                        'id',
                        $saleItem->product_id
                    );
            })
            ->orderBy('name')
            ->get();

        $exchangeRates = $this->availableExchangeRates(
            $sale->exchange_rate_id
        );

        $rateChoices = $this->buildRateChoices(
            $exchangeRates,
            $sale
        );

        return view('sales.edit', compact(
            'sale',
            'saleItem',
            'products',
            'rateChoices'
        ));
    }

    public function update(
        Request $request,
        Sale $sale
    ): RedirectResponse {
        $request->merge([
            'unit_price_usd' => $this->normalizeDecimal(
                $request->input('unit_price_usd')
            ),
        ]);

        $validated = $this->validateSale($request);

        DB::transaction(function () use (
            $validated,
            $sale
        ) {
            $lockedSale = Sale::query()
                ->whereKey($sale->id)
                ->lockForUpdate()
                ->firstOrFail();

            $rateSelection = $this->resolveRateSelection(
                $validated,
                $lockedSale
            );

            $saleItem = SaleItem::query()
                ->where(
                    'sale_id',
                    $lockedSale->id
                )
                ->lockForUpdate()
                ->firstOrFail();

            $oldProductId =
                (int) $saleItem->product_id;

            $newProductId =
                (int) $validated['product_id'];

            $oldQuantity =
                (int) $saleItem->quantity;

            $newQuantity =
                (int) $validated['quantity'];

            $unitPriceUsd = round(
                (float) $validated['unit_price_usd'],
                2
            );

            if (
                $oldProductId === $newProductId
            ) {
                $product = Product::query()
                    ->whereKey($oldProductId)
                    ->lockForUpdate()
                    ->firstOrFail();

                $correctedStock =
                    (int) $product->current_stock
                    + $oldQuantity
                    - $newQuantity;

                if ($correctedStock < 0) {
                    throw ValidationException::withMessages([
                        'quantity' =>
                        'No hay stock suficiente para aplicar la nueva cantidad de la venta.',
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

                $product->current_stock =
                    $correctedStock;

                $product->save();

                $movementStock =
                    $correctedStock;
            } else {
                $lockedProducts = Product::query()
                    ->whereIn('id', [
                        $oldProductId,
                        $newProductId,
                    ])
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $oldProduct =
                    $lockedProducts->get($oldProductId);

                $newProduct =
                    $lockedProducts->get($newProductId);

                if (
                    ! $oldProduct ||
                    ! $newProduct
                ) {
                    throw ValidationException::withMessages([
                        'product_id' =>
                        'No fue posible localizar los productos de la corrección.',
                    ]);
                }

                if (
                    (int) $newProduct->current_stock
                    < $newQuantity
                ) {
                    throw ValidationException::withMessages([
                        'quantity' =>
                        'El nuevo producto seleccionado no tiene stock suficiente.',
                    ]);
                }

                $oldProduct->current_stock =
                    (int) $oldProduct->current_stock
                    + $oldQuantity;

                $oldProduct->save();

                $newProduct->current_stock =
                    (int) $newProduct->current_stock
                    - $newQuantity;

                $newProduct->save();

                $unitCostUsd = round(
                    (float) $newProduct->purchase_price_usd,
                    2
                );

                $movementStock =
                    $newProduct->current_stock;
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
                $rateSelection['value'],
                4
            );

            $totalBs = round(
                $totalUsd * $exchangeRateValue,
                2
            );

            $lockedSale->update([
                'exchange_rate_id' =>
                $rateSelection['exchange_rate']->id,
                'sale_date' =>
                $validated['sale_date'],
                'customer_name' =>
                $validated['customer_name'] ?? null,
                'total_usd' =>
                $totalUsd,
                'exchange_rate_value' =>
                $exchangeRateValue,
                'total_bs' =>
                $totalBs,
                'estimated_profit_usd' =>
                $totalProfitUsd,
                'rate_source' =>
                $rateSelection['source'],
                'payment_method' =>
                $validated['payment_method'],
                'notes' =>
                $validated['notes'] ?? null,
            ]);

            $saleItem->update([
                'product_id' =>
                $newProductId,
                'quantity' =>
                $newQuantity,
                'unit_price_usd' =>
                $unitPriceUsd,
                'unit_cost_usd' =>
                $unitCostUsd,
                'unit_profit_usd' =>
                $unitProfitUsd,
                'total_usd' =>
                $totalUsd,
                'total_profit_usd' =>
                $totalProfitUsd,
            ]);

            InventoryMovement::query()
                ->updateOrCreate(
                    [
                        'movementable_type' =>
                        Sale::class,
                        'movementable_id' =>
                        $lockedSale->id,
                        'type' =>
                        'sale',
                    ],
                    [
                        'product_id' =>
                        $newProductId,
                        'quantity' =>
                        $newQuantity,
                        'stock_after_movement' =>
                        $movementStock,
                        'movement_date' =>
                        $validated['sale_date'],
                        'notes' =>
                        'Salida por venta corregida.',
                    ]
                );
        });

        return redirect()
            ->route('sales.index')
            ->with(
                'success',
                'Venta actualizada correctamente.'
            );
    }

    private function validateSale(
        Request $request
    ): array {
        return $request->validate([
            'product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],
            'sale_date' => [
                'required',
                'date_format:Y-m-d',
            ],
            'customer_name' => [
                'nullable',
                'string',
                'max:180',
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
            'unit_price_usd' => [
                'required',
                'numeric',
                'min:0.01',
            ],
            'exchange_rate_choice' => [
                'required',
                'string',
                'regex:/^\d+\|(bcv|binance|manual)$/',
            ],
            'payment_method' => [
                'required',
                'in:pago_movil,transferencia_bs,efectivo_usd,binance,zelle,mixto',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ], [
            'exchange_rate_choice.required' =>
            'Debes seleccionar una tasa registrada para la venta.',

            'exchange_rate_choice.regex' =>
            'La tasa seleccionada no tiene un formato válido.',
        ]);
    }

    private function availableExchangeRates(
        ?int $includeExchangeRateId = null
    ): Collection {
        return ExchangeRate::query()
            ->where(function ($query) use (
                $includeExchangeRateId
            ) {
                $query->where(
                    'status',
                    'active'
                );

                if ($includeExchangeRateId) {
                    $query->orWhere(
                        'id',
                        $includeExchangeRateId
                    );
                }
            })
            ->orderByDesc('rate_date')
            ->orderByDesc('rate_time')
            ->orderByDesc('id')
            ->get();
    }

    private function buildRateChoices(
        Collection $exchangeRates,
        ?Sale $currentSale = null
    ): Collection {
        $sourceDefinitions = [
            'binance' => [
                'field' => 'binance_rate',
                'label' => 'Binance',
            ],
            'bcv' => [
                'field' => 'bcv_rate',
                'label' => 'BCV',
            ],
            'manual' => [
                'field' => 'manual_rate',
                'label' => 'Manual',
            ],
        ];

        $choices = collect();

        foreach ($exchangeRates as $exchangeRate) {
            foreach (
                $sourceDefinitions
                as $source => $definition
            ) {
                $storedValue =
                    $exchangeRate->{$definition['field']};

                $isCurrentSaleChoice =
                    $currentSale
                    && (int) $currentSale->exchange_rate_id
                    === (int) $exchangeRate->id
                    && $currentSale->rate_source
                    === $source;

                if ($isCurrentSaleChoice) {
                    $storedValue =
                        $currentSale->exchange_rate_value;
                }

                if (
                    $storedValue === null ||
                    $storedValue === '' ||
                    ! is_numeric($storedValue) ||
                    (float) $storedValue <= 0
                ) {
                    continue;
                }

                $choices->push([
                    'key' =>
                    $exchangeRate->id . '|' . $source,

                    'exchange_rate_id' =>
                    $exchangeRate->id,

                    'source' =>
                    $source,

                    'source_label' =>
                    $definition['label'],

                    'rate_date' =>
                    $exchangeRate->rate_date
                        ?->format('Y-m-d'),

                    'rate_date_label' =>
                    $exchangeRate->rate_date
                        ?->format('d/m/Y'),

                    'rate_time' =>
                    $exchangeRate->rate_time
                        ?->format('H:i')
                        ?? '--:--',

                    'value' =>
                    round((float) $storedValue, 4),

                    'status' =>
                    $exchangeRate->status,

                    'is_current_sale_choice' =>
                    $isCurrentSaleChoice,
                ]);
            }
        }

        return $choices->values();
    }

    private function resolveRateSelection(
        array $validated,
        ?Sale $currentSale = null
    ): array {
        [$exchangeRateId, $source] = explode(
            '|',
            $validated['exchange_rate_choice'],
            2
        );

        $exchangeRate = ExchangeRate::query()
            ->whereKey((int) $exchangeRateId)
            ->lockForUpdate()
            ->first();

        if (! $exchangeRate) {
            throw ValidationException::withMessages([
                'exchange_rate_choice' =>
                'La tasa seleccionada ya no existe.',
            ]);
        }

        $validSources = [
            'bcv',
            'binance',
            'manual',
        ];

        if (! in_array(
            $source,
            $validSources,
            true
        )) {
            throw ValidationException::withMessages([
                'exchange_rate_choice' =>
                'La fuente de la tasa seleccionada no es válida.',
            ]);
        }

        $isCurrentSaleChoice =
            $currentSale
            && (int) $currentSale->exchange_rate_id
            === (int) $exchangeRate->id
            && $currentSale->rate_source
            === $source;

        if (
            $exchangeRate->status !== 'active'
            && ! $isCurrentSaleChoice
        ) {
            throw ValidationException::withMessages([
                'exchange_rate_choice' =>
                'La tasa seleccionada está inactiva.',
            ]);
        }

        $exchangeRateDate =
            $exchangeRate->rate_date
            ?->format('Y-m-d');

        if (
            $exchangeRateDate
            !== $validated['sale_date']
        ) {
            throw ValidationException::withMessages([
                'exchange_rate_choice' =>
                'La tasa seleccionada no corresponde a la fecha de la venta.',
            ]);
        }

        if ($isCurrentSaleChoice) {
            $value =
                $currentSale->exchange_rate_value;
        } else {
            $value = match ($source) {
                'bcv' =>
                $exchangeRate->bcv_rate,

                'binance' =>
                $exchangeRate->binance_rate,

                'manual' =>
                $exchangeRate->manual_rate,
            };
        }

        if (
            $value === null ||
            $value === '' ||
            ! is_numeric($value) ||
            (float) $value <= 0
        ) {
            throw ValidationException::withMessages([
                'exchange_rate_choice' =>
                'La fuente seleccionada no tiene una tasa válida.',
            ]);
        }

        return [
            'exchange_rate' => $exchangeRate,
            'source' => $source,
            'value' => (float) $value,
        ];
    }

    private function normalizeDecimal(
        mixed $value
    ): ?string {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

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

        if (
            $lastComma !== false &&
            $lastDot !== false
        ) {
            if ($lastComma > $lastDot) {
                $value = str_replace(
                    '.',
                    '',
                    $value
                );

                $value = str_replace(
                    ',',
                    '.',
                    $value
                );
            } else {
                $value = str_replace(
                    ',',
                    '',
                    $value
                );
            }
        } elseif ($lastComma !== false) {
            $value = str_replace(
                ',',
                '.',
                $value
            );
        }

        return $value;
    }
}
