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
use Illuminate\Support\Collection;
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

        $totalUnits = $purchases->sum(
            fn(Purchase $purchase) =>
            $purchase->items->sum('quantity')
        );

        $totalUsd = $purchases->sum(
            fn(Purchase $purchase) =>
            (float) ($purchase->total_usd ?? 0)
        );

        $totalBs = $purchases->sum(
            fn(Purchase $purchase) =>
            (float) ($purchase->total_bs ?? 0)
        );

        $averageRate = $purchases
            ->filter(
                fn(Purchase $purchase) =>
                $purchase->exchange_rate_value !== null
            )
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

        $exchangeRates = $this->availableExchangeRates();

        $rateChoices = $this->buildRateChoices(
            $exchangeRates
        );

        return view('purchases.create', compact(
            'suppliers',
            'products',
            'rateChoices'
        ));
    }

    public function store(
        Request $request
    ): RedirectResponse {
        $request->merge([
            'unit_cost_usd' => $this->normalizeDecimal(
                $request->input('unit_cost_usd')
            ),
        ]);

        $validated = $this->validatePurchase(
            $request
        );

        DB::transaction(function () use ($validated) {
            $rateSelection = $this->resolveRateSelection(
                $validated
            );

            $product = Product::query()
                ->whereKey($validated['product_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $quantity = (int) $validated['quantity'];

            $unitCostUsd = round(
                (float) $validated['unit_cost_usd'],
                2
            );

            $exchangeRateValue = round(
                $rateSelection['value'],
                4
            );

            $totalUsd = round(
                $quantity * $unitCostUsd,
                2
            );

            $totalBs = round(
                $totalUsd * $exchangeRateValue,
                2
            );

            $purchase = Purchase::create([
                'supplier_id' =>
                $validated['supplier_id'],

                'exchange_rate_id' =>
                $rateSelection['exchange_rate']->id,

                'purchase_date' =>
                $validated['purchase_date'],

                'total_usd' =>
                $totalUsd,

                'exchange_rate_value' =>
                $exchangeRateValue,

                'total_bs' =>
                $totalBs,

                'rate_source' =>
                $rateSelection['source'],

                'payment_method' =>
                $validated['payment_method'],

                'notes' =>
                $validated['notes'] ?? null,
            ]);

            PurchaseItem::create([
                'purchase_id' =>
                $purchase->id,

                'product_id' =>
                $product->id,

                'quantity' =>
                $quantity,

                'unit_cost_usd' =>
                $unitCostUsd,

                'total_usd' =>
                $totalUsd,
            ]);

            $product->current_stock =
                (int) $product->current_stock
                + $quantity;

            $this->applyProductCost(
                $product,
                $unitCostUsd
            );

            $product->save();

            InventoryMovement::create([
                'product_id' =>
                $product->id,

                'movementable_type' =>
                Purchase::class,

                'movementable_id' =>
                $purchase->id,

                'type' =>
                'purchase',

                'quantity' =>
                $quantity,

                'stock_after_movement' =>
                $product->current_stock,

                'movement_date' =>
                $validated['purchase_date'],

                'notes' =>
                'Entrada por compra registrada.',
            ]);
        });

        return redirect()
            ->route('purchases.index')
            ->with(
                'success',
                'Compra registrada correctamente.'
            );
    }

    public function edit(
        Purchase $purchase
    ): View {
        $purchase->load([
            'supplier',
            'exchangeRate',
            'items.product.unitMeasure',
        ]);

        $purchaseItem = $purchase->items->first();

        abort_if(
            ! $purchaseItem,
            404,
            'La compra no tiene productos asociados.'
        );

        $suppliers = Supplier::query()
            ->where(function ($query) use ($purchase) {
                $query
                    ->where('is_active', true)
                    ->orWhere(
                        'id',
                        $purchase->supplier_id
                    );
            })
            ->orderBy('name')
            ->get();

        $products = Product::query()
            ->with('unitMeasure')
            ->where(function ($query) use (
                $purchaseItem
            ) {
                $query
                    ->where('status', 'active')
                    ->orWhere(
                        'id',
                        $purchaseItem->product_id
                    );
            })
            ->orderBy('name')
            ->get();

        $exchangeRates = $this->availableExchangeRates(
            $purchase->exchange_rate_id
        );

        $rateChoices = $this->buildRateChoices(
            $exchangeRates,
            $purchase
        );

        return view('purchases.edit', compact(
            'purchase',
            'purchaseItem',
            'suppliers',
            'products',
            'rateChoices'
        ));
    }

    public function update(
        Request $request,
        Purchase $purchase
    ): RedirectResponse {
        $request->merge([
            'unit_cost_usd' => $this->normalizeDecimal(
                $request->input('unit_cost_usd')
            ),
        ]);

        $validated = $this->validatePurchase(
            $request
        );

        DB::transaction(function () use (
            $validated,
            $purchase
        ) {
            $lockedPurchase = Purchase::query()
                ->whereKey($purchase->id)
                ->lockForUpdate()
                ->firstOrFail();

            $rateSelection = $this->resolveRateSelection(
                $validated,
                $lockedPurchase
            );

            $purchaseItem = PurchaseItem::query()
                ->where(
                    'purchase_id',
                    $lockedPurchase->id
                )
                ->lockForUpdate()
                ->firstOrFail();

            $oldProductId =
                (int) $purchaseItem->product_id;

            $newProductId =
                (int) $validated['product_id'];

            $oldQuantity =
                (int) $purchaseItem->quantity;

            $newQuantity =
                (int) $validated['quantity'];

            $unitCostUsd = round(
                (float) $validated['unit_cost_usd'],
                2
            );

            $exchangeRateValue = round(
                $rateSelection['value'],
                4
            );

            $totalUsd = round(
                $newQuantity * $unitCostUsd,
                2
            );

            $totalBs = round(
                $totalUsd * $exchangeRateValue,
                2
            );

            if ($oldProductId === $newProductId) {
                $product = Product::query()
                    ->whereKey($oldProductId)
                    ->lockForUpdate()
                    ->firstOrFail();

                $correctedStock =
                    (int) $product->current_stock
                    - $oldQuantity
                    + $newQuantity;

                if ($correctedStock < 0) {
                    throw ValidationException::withMessages([
                        'quantity' =>
                        'No es posible reducir la compra a esa cantidad porque parte de las unidades ya fue vendida.',
                    ]);
                }

                $product->current_stock =
                    $correctedStock;

                $this->applyProductCost(
                    $product,
                    $unitCostUsd
                );

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

                $oldProduct = $lockedProducts->get(
                    $oldProductId
                );

                $newProduct = $lockedProducts->get(
                    $newProductId
                );

                if (
                    ! $oldProduct
                    || ! $newProduct
                ) {
                    throw ValidationException::withMessages([
                        'product_id' =>
                        'No fue posible localizar los productos asociados a la corrección.',
                    ]);
                }

                $correctedOldStock =
                    (int) $oldProduct->current_stock
                    - $oldQuantity;

                if ($correctedOldStock < 0) {
                    throw ValidationException::withMessages([
                        'product_id' =>
                        'No se puede cambiar el producto porque las unidades de la compra original ya fueron utilizadas o vendidas.',
                    ]);
                }

                $oldProduct->current_stock =
                    $correctedOldStock;

                $oldProduct->save();

                $newProduct->current_stock =
                    (int) $newProduct->current_stock
                    + $newQuantity;

                $this->applyProductCost(
                    $newProduct,
                    $unitCostUsd
                );

                $newProduct->save();

                $movementStock =
                    $newProduct->current_stock;
            }

            $lockedPurchase->update([
                'supplier_id' =>
                $validated['supplier_id'],

                'exchange_rate_id' =>
                $rateSelection['exchange_rate']->id,

                'purchase_date' =>
                $validated['purchase_date'],

                'total_usd' =>
                $totalUsd,

                'exchange_rate_value' =>
                $exchangeRateValue,

                'total_bs' =>
                $totalBs,

                'rate_source' =>
                $rateSelection['source'],

                'payment_method' =>
                $validated['payment_method'],

                'notes' =>
                $validated['notes'] ?? null,
            ]);

            $purchaseItem->update([
                'product_id' =>
                $newProductId,

                'quantity' =>
                $newQuantity,

                'unit_cost_usd' =>
                $unitCostUsd,

                'total_usd' =>
                $totalUsd,
            ]);

            InventoryMovement::query()
                ->updateOrCreate(
                    [
                        'movementable_type' =>
                        Purchase::class,

                        'movementable_id' =>
                        $lockedPurchase->id,

                        'type' =>
                        'purchase',
                    ],
                    [
                        'product_id' =>
                        $newProductId,

                        'quantity' =>
                        $newQuantity,

                        'stock_after_movement' =>
                        $movementStock,

                        'movement_date' =>
                        $validated['purchase_date'],

                        'notes' =>
                        'Entrada por compra corregida.',
                    ]
                );
        });

        return redirect()
            ->route('purchases.index')
            ->with(
                'success',
                'Compra actualizada correctamente.'
            );
    }

    private function validatePurchase(
        Request $request
    ): array {
        return $request->validate([
            'supplier_id' => [
                'required',
                'integer',
                'exists:suppliers,id',
            ],

            'product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],

            'purchase_date' => [
                'required',
                'date_format:Y-m-d',
            ],

            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'unit_cost_usd' => [
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
            'Debes seleccionar una tasa registrada para la compra.',

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
        ?Purchase $currentPurchase = null
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

                $isCurrentPurchaseChoice =
                    $currentPurchase
                    && (int) $currentPurchase
                        ->exchange_rate_id
                    === (int) $exchangeRate->id
                    && $currentPurchase->rate_source
                    === $source;

                if ($isCurrentPurchaseChoice) {
                    $storedValue =
                        $currentPurchase
                        ->exchange_rate_value;
                }

                if (
                    $storedValue === null
                    || $storedValue === ''
                    || ! is_numeric($storedValue)
                    || (float) $storedValue <= 0
                ) {
                    continue;
                }

                $choices->push([
                    'key' =>
                    $exchangeRate->id
                        . '|'
                        . $source,

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
                    round(
                        (float) $storedValue,
                        4
                    ),

                    'status' =>
                    $exchangeRate->status,

                    'is_current_purchase_choice' =>
                    $isCurrentPurchaseChoice,
                ]);
            }
        }

        return $choices->values();
    }

    private function resolveRateSelection(
        array $validated,
        ?Purchase $currentPurchase = null
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

        $isCurrentPurchaseChoice =
            $currentPurchase
            && (int) $currentPurchase->exchange_rate_id
            === (int) $exchangeRate->id
            && $currentPurchase->rate_source
            === $source;

        if (
            $exchangeRate->status !== 'active'
            && ! $isCurrentPurchaseChoice
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
            !== $validated['purchase_date']
        ) {
            throw ValidationException::withMessages([
                'exchange_rate_choice' =>
                'La tasa seleccionada no corresponde a la fecha de la compra.',
            ]);
        }

        if ($isCurrentPurchaseChoice) {
            $value =
                $currentPurchase
                ->exchange_rate_value;
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
            $value === null
            || $value === ''
            || ! is_numeric($value)
            || (float) $value <= 0
        ) {
            throw ValidationException::withMessages([
                'exchange_rate_choice' =>
                'La fuente seleccionada no tiene una tasa válida.',
            ]);
        }

        return [
            'exchange_rate' =>
            $exchangeRate,

            'source' =>
            $source,

            'value' =>
            (float) $value,
        ];
    }

    private function applyProductCost(
        Product $product,
        float $unitCostUsd
    ): void {
        $product->purchase_price_usd =
            $unitCostUsd;

        if (
            $product->sale_price_usd !== null
            && (float) $product->sale_price_usd > 0
        ) {
            $product->unit_profit_usd = round(
                (float) $product->sale_price_usd
                    - $unitCostUsd,
                2
            );

            $product->profit_margin = round(
                (
                    $product->unit_profit_usd
                    / (float) $product->sale_price_usd
                ) * 100,
                2
            );
        }
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
            $lastComma !== false
            && $lastDot !== false
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
