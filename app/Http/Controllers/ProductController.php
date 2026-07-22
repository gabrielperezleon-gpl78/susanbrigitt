<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\UnitMeasure;
use App\Models\Supplier;
use App\Models\Tone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::with(['category', 'brand', 'tone', 'unitMeasure', 'supplier'])
            ->latest()
            ->get();

        $totalProducts = Product::count();

        $availableUnits = Product::sum('current_stock');

        $outOfStockProducts = Product::where('current_stock', '<=', 0)->count();

        $inventoryValue = Product::query()
            ->selectRaw('SUM(current_stock * purchase_price_usd) as total')
            ->value('total') ?? 0;

        return view('products.index', [
            'products' => $products,
            'totalProducts' => $totalProducts,
            'availableUnits' => $availableUnits,
            'outOfStockProducts' => $outOfStockProducts,
            'inventoryValue' => $inventoryValue,
        ]);
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
            'internal_code' => ['nullable', 'string', 'max:80', 'unique:products,internal_code'],
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

        Product::create([
            'category_id' => $validated['category_id'] ?? null,
            'brand_id' => $validated['brand_id'] ?? null,
            'tone_id' => $validated['tone_id'] ?? null,
            'unit_measure_id' => $validated['unit_measure_id'] ?? null,
            'supplier_id' => $validated['supplier_id'] ?? null,
            'internal_code' => $validated['internal_code'] ?? null,
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

        return redirect()
            ->route('products.index')
            ->with('success', 'Producto registrado correctamente.');
    }

    private function generateUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 2;

        while (Product::where('slug', $slug)->exists()) {
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
