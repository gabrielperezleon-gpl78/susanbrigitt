<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Tone;
use App\Models\UnitMeasure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim(
            $request->string('search')->toString()
        );

        $brandId = $request->integer('brand_id') ?: null;
        $toneId = $request->integer('tone_id') ?: null;
        $categoryId = $request->integer('category_id') ?: null;

        $stockStatus = $request
            ->string('stock_status')
            ->toString();

        $products = Product::query()
            ->with([
                'category',
                'brand',
                'tone',
                'unitMeasure',
                'supplier',
            ])
            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->where(function ($subquery) use ($search) {
                        $subquery
                            ->where(
                                'name',
                                'like',
                                '%' . $search . '%'
                            )
                            ->orWhere(
                                'internal_code',
                                'like',
                                '%' . $search . '%'
                            )
                            ->orWhere(
                                'barcode',
                                'like',
                                '%' . $search . '%'
                            );
                    });
                }
            )
            ->when(
                $brandId,
                fn($query) =>
                $query->where('brand_id', $brandId)
            )
            ->when(
                $toneId,
                fn($query) =>
                $query->where('tone_id', $toneId)
            )
            ->when(
                $categoryId,
                fn($query) =>
                $query->where('category_id', $categoryId)
            )
            ->when(
                $stockStatus === 'available',
                fn($query) =>
                $query->whereColumn(
                    'current_stock',
                    '>',
                    'minimum_stock'
                )
            )
            ->when(
                $stockStatus === 'low',
                fn($query) =>
                $query
                    ->where('current_stock', '>', 0)
                    ->whereColumn(
                        'current_stock',
                        '<=',
                        'minimum_stock'
                    )
            )
            ->when(
                $stockStatus === 'out',
                fn($query) =>
                $query->where('current_stock', '<=', 0)
            )
            ->latest()
            ->get();

        $activeProductsQuery = Product::query()
            ->where('status', 'active');

        $totalProducts = (clone $activeProductsQuery)
            ->count();

        $availableUnits = (clone $activeProductsQuery)
            ->sum('current_stock');

        $outOfStockProducts = (clone $activeProductsQuery)
            ->where('current_stock', '<=', 0)
            ->count();

        $inventoryValue = (float) (
            (clone $activeProductsQuery)
            ->selectRaw(
                'COALESCE(
                    SUM(current_stock * purchase_price_usd),
                    0
                ) as total'
            )
            ->value('total')
        );

        $brands = Brand::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $tones = Tone::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('products.index', compact(
            'products',
            'totalProducts',
            'availableUnits',
            'outOfStockProducts',
            'inventoryValue',
            'brands',
            'tones',
            'categories'
        ));
    }

    public function create(): View
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $brands = Brand::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $tones = Tone::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $unitMeasures = UnitMeasure::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $suppliers = Supplier::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('products.create', compact(
            'categories',
            'brands',
            'tones',
            'unitMeasures',
            'suppliers'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'purchase_price_usd' => $this->normalizeDecimal($request->input('purchase_price_usd')),
            'sale_price_usd' => $this->normalizeDecimal($request->input('sale_price_usd')),
        ]);

        $validated = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'tone_id' => ['nullable', 'exists:tones,id'],
            'unit_measure_id' => ['nullable', 'exists:unit_measures,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'internal_code' => ['required', 'string', 'max:80', 'unique:products,internal_code'],
            'name' => ['required', 'string', 'max:180'],
            'barcode' => ['nullable', 'string', 'max:120', 'unique:products,barcode'],
            'description' => ['nullable', 'string', 'max:1500'],
            'purchase_price_usd' => ['required', 'numeric', 'min:0'],
            'sale_price_usd' => ['required', 'numeric', 'min:0'],
            'initial_stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'entry_date' => ['nullable', 'date'],
            'status' => ['required', 'in:active,inactive'],
            'internal_notes' => ['nullable', 'string', 'max:1500'],
        ]);

        $purchasePriceUsd = round((float) $validated['purchase_price_usd'], 2);
        $salePriceUsd = round((float) $validated['sale_price_usd'], 2);
        $unitProfitUsd = round($salePriceUsd - $purchasePriceUsd, 2);

        $profitMargin = $salePriceUsd > 0
            ? round(($unitProfitUsd / $salePriceUsd) * 100, 2)
            : 0;

        $slug = $this->generateUniqueSlug($validated['name']);

        DB::transaction(function () use ($validated, $purchasePriceUsd, $salePriceUsd, $unitProfitUsd, $profitMargin, $slug) {
            $product = Product::create([
                'category_id' => $validated['category_id'] ?? null,
                'brand_id' => $validated['brand_id'] ?? null,
                'tone_id' => $validated['tone_id'] ?? null,
                'unit_measure_id' => $validated['unit_measure_id'] ?? null,
                'supplier_id' => $validated['supplier_id'] ?? null,
                'internal_code' => $validated['internal_code'],
                'name' => $validated['name'],
                'slug' => $slug,
                'barcode' => $validated['barcode'] ?? null,
                'description' => $validated['description'] ?? null,
                'image_path' => null,
                'purchase_price_usd' => $purchasePriceUsd,
                'sale_price_usd' => $salePriceUsd,
                'unit_profit_usd' => $unitProfitUsd,
                'profit_margin' => $profitMargin,
                'initial_stock' => (int) $validated['initial_stock'],
                'current_stock' => (int) $validated['initial_stock'],
                'minimum_stock' => (int) $validated['minimum_stock'],
                'entry_date' => $validated['entry_date'] ?? now()->toDateString(),
                'status' => $validated['status'],
                'internal_notes' => $validated['internal_notes'] ?? null,
            ]);

            if ($product->initial_stock > 0) {
                InventoryMovement::create([
                    'product_id' => $product->id,
                    'type' => 'initial',
                    'quantity' => $product->initial_stock,
                    'stock_after_movement' => $product->initial_stock,
                    'movement_date' => $product->entry_date,
                    'notes' => 'Existencias al registrar el producto.',
                ]);
            }
        });

        return redirect()
            ->route('products.index')
            ->with('success', 'Producto registrado correctamente.');
    }

    public function show(Product $product): View
    {
        $product->load([
            'category',
            'brand',
            'tone',
            'unitMeasure',
            'supplier',
        ]);

        $recentAdjustments = InventoryMovement::query()
            ->where('product_id', $product->id)
            ->whereIn('type', ['adjustment_in', 'adjustment_out'])
            ->latest()
            ->limit(10)
            ->get();

        return view('products.show', compact('product', 'recentAdjustments'));
    }

    public function edit(Product $product): View
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $brands = Brand::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $tones = Tone::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $unitMeasures = UnitMeasure::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $suppliers = Supplier::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('products.edit', compact(
            'product',
            'categories',
            'brands',
            'tones',
            'unitMeasures',
            'suppliers'
        ));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->merge([
            'purchase_price_usd' => $this->normalizeDecimal($request->input('purchase_price_usd')),
            'sale_price_usd' => $this->normalizeDecimal($request->input('sale_price_usd')),
        ]);

        $validated = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'tone_id' => ['nullable', 'exists:tones,id'],
            'unit_measure_id' => ['nullable', 'exists:unit_measures,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'internal_code' => ['required', 'string', 'max:80', 'unique:products,internal_code,' . $product->id],
            'name' => ['required', 'string', 'max:180'],
            'barcode' => ['nullable', 'string', 'max:120', 'unique:products,barcode,' . $product->id],
            'description' => ['nullable', 'string', 'max:1500'],
            'purchase_price_usd' => ['required', 'numeric', 'min:0'],
            'sale_price_usd' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
            'internal_notes' => ['nullable', 'string', 'max:1500'],
        ]);

        $purchasePriceUsd = round((float) $validated['purchase_price_usd'], 2);
        $salePriceUsd = round((float) $validated['sale_price_usd'], 2);
        $unitProfitUsd = round($salePriceUsd - $purchasePriceUsd, 2);

        $profitMargin = $salePriceUsd > 0
            ? round(($unitProfitUsd / $salePriceUsd) * 100, 2)
            : 0;

        $product->update([
            'category_id' => $validated['category_id'] ?? null,
            'brand_id' => $validated['brand_id'] ?? null,
            'tone_id' => $validated['tone_id'] ?? null,
            'unit_measure_id' => $validated['unit_measure_id'] ?? null,
            'supplier_id' => $validated['supplier_id'] ?? null,
            'internal_code' => $validated['internal_code'],
            'name' => $validated['name'],
            'slug' => $this->generateUniqueSlug($validated['name'], $product->id),
            'barcode' => $validated['barcode'] ?? null,
            'description' => $validated['description'] ?? null,
            'purchase_price_usd' => $purchasePriceUsd,
            'sale_price_usd' => $salePriceUsd,
            'unit_profit_usd' => $unitProfitUsd,
            'profit_margin' => $profitMargin,
            'minimum_stock' => (int) $validated['minimum_stock'],
            'status' => $validated['status'],
            'internal_notes' => $validated['internal_notes'] ?? null,
        ]);

        return redirect()
            ->route('products.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function adjustStock(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'counted_stock' => ['required', 'integer', 'min:0'],
            'expected_stock' => ['required', 'integer', 'min:0'],
            'reason' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        DB::transaction(function () use ($product, $validated, $request) {
            $lockedProduct = Product::query()
                ->whereKey($product->id)
                ->lockForUpdate()
                ->firstOrFail();

            $previousStock = (int) $lockedProduct->current_stock;
            $countedStock = (int) $validated['counted_stock'];

            if ($previousStock !== (int) $validated['expected_stock']) {
                throw ValidationException::withMessages([
                    'counted_stock' => 'Las existencias cambiaron desde que abriste esta ficha. Actualiza la página y verifica el conteo antes de ajustar.',
                ]);
            }

            $difference = $countedStock - $previousStock;

            if ($difference === 0) {
                throw ValidationException::withMessages([
                    'counted_stock' => 'La cantidad contada coincide con el stock actual; no hay diferencia que ajustar.',
                ]);
            }

            $lockedProduct->current_stock = $countedStock;
            $lockedProduct->save();

            InventoryMovement::create([
                'product_id' => $lockedProduct->id,
                'type' => $difference > 0 ? 'adjustment_in' : 'adjustment_out',
                'quantity' => abs($difference),
                'stock_after_movement' => $countedStock,
                'movement_date' => now()->toDateString(),
                'notes' => sprintf(
                    'Conteo anterior: %d. Conteo físico: %d. Usuario: %s (#%d). Motivo: %s',
                    $previousStock,
                    $countedStock,
                    $request->user()->name,
                    $request->user()->id,
                    trim($validated['reason'])
                ),
            ]);
        });

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Inventario ajustado y movimiento registrado.');
    }

    private function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 2;

        while (
            Product::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn($query) => $query->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
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
