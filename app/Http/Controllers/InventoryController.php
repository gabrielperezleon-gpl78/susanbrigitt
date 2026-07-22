<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Tone;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'search' => trim((string) $request->query('search')),
            'brand_id' => $request->integer('brand_id') ?: null,
            'tone_id' => $request->integer('tone_id') ?: null,
            'category_id' => $request->integer('category_id') ?: null,
            'stock_status' => (string) $request->query('stock_status'),
            'supplier_id' => $request->integer('supplier_id') ?: null,
        ];

        $productsQuery = Product::query()
            ->with([
                'category',
                'brand',
                'tone',
                'supplier',
            ])
            ->orderBy('name');

        if ($filters['search'] !== '') {
            $productsQuery->where(function ($query) use ($filters) {
                $query
                    ->where('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere(
                        'internal_code',
                        'like',
                        '%' . $filters['search'] . '%'
                    );
            });
        }

        if ($filters['brand_id']) {
            $productsQuery->where('brand_id', $filters['brand_id']);
        }

        if ($filters['tone_id']) {
            $productsQuery->where('tone_id', $filters['tone_id']);
        }

        if ($filters['category_id']) {
            $productsQuery->where(
                'category_id',
                $filters['category_id']
            );
        }

        if ($filters['supplier_id']) {
            $productsQuery->where(
                'supplier_id',
                $filters['supplier_id']
            );
        }

        if ($filters['stock_status'] === 'available') {
            $productsQuery->whereColumn(
                'current_stock',
                '>',
                'minimum_stock'
            );
        }

        if ($filters['stock_status'] === 'low') {
            $productsQuery
                ->where('current_stock', '>', 0)
                ->whereColumn(
                    'current_stock',
                    '<=',
                    'minimum_stock'
                );
        }

        if ($filters['stock_status'] === 'out') {
            $productsQuery->where('current_stock', '<=', 0);
        }

        $products = $productsQuery->get();

        $totalProducts = Product::count();

        $availableUnits = (int) Product::sum('current_stock');

        $outOfStockProducts = Product::query()
            ->where('current_stock', '<=', 0)
            ->count();

        $lowStockProducts = Product::query()
            ->where('current_stock', '>', 0)
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->count();

        $inventoryValue = (float) (
            Product::query()
            ->selectRaw(
                'COALESCE(SUM(current_stock * purchase_price_usd), 0) as total'
            )
            ->value('total') ?? 0
        );

        $replenishmentAlerts = Product::query()
            ->with(['brand', 'supplier'])
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->orderBy('current_stock')
            ->orderBy('name')
            ->limit(8)
            ->get();

        $brandValues = Product::query()
            ->leftJoin(
                'brands',
                'products.brand_id',
                '=',
                'brands.id'
            )
            ->selectRaw(
                "COALESCE(brands.name, 'Sin marca') as brand_name"
            )
            ->selectRaw(
                'COALESCE(SUM(products.current_stock * products.purchase_price_usd), 0) as total_value'
            )
            ->groupBy('brands.id', 'brands.name')
            ->orderByDesc('total_value')
            ->get()
            ->filter(
                fn($brandValue) =>
                (float) $brandValue->total_value > 0
            )
            ->take(6)
            ->values();

        $maxBrandValue = (float) (
            $brandValues->max('total_value') ?? 0
        );

        $soldUnitsLast30Days = (int) DB::table('sale_items')
            ->join(
                'sales',
                'sales.id',
                '=',
                'sale_items.sale_id'
            )
            ->whereDate(
                'sales.sale_date',
                '>=',
                now()->subDays(30)->toDateString()
            )
            ->sum('sale_items.quantity');

        $rotationRatio = $availableUnits > 0
            ? $soldUnitsLast30Days / $availableUnits
            : 0;

        $rotationEstimate = match (true) {
            $soldUnitsLast30Days === 0 => 'Sin datos',
            $rotationRatio >= 1 => 'Alta',
            $rotationRatio >= 0.30 => 'Media',
            default => 'Baja',
        };

        $productsWithoutMovement = Product::query()
            ->whereNotExists(function ($query) {
                $query
                    ->selectRaw('1')
                    ->from('inventory_movements')
                    ->whereColumn(
                        'inventory_movements.product_id',
                        'products.id'
                    )
                    ->where(
                        'inventory_movements.type',
                        'sale'
                    );
            })
            ->count();

        $lastUpdatedValue = Product::max('updated_at');

        $lastUpdatedAt = $lastUpdatedValue
            ? CarbonImmutable::parse($lastUpdatedValue)
            : null;

        $brands = Brand::query()
            ->orderBy('name')
            ->get();

        $tones = Tone::query()
            ->orderBy('name')
            ->get();

        $categories = Category::query()
            ->orderBy('name')
            ->get();

        $suppliers = Supplier::query()
            ->orderBy('name')
            ->get();

        return view('inventory.index', compact(
            'products',
            'totalProducts',
            'availableUnits',
            'outOfStockProducts',
            'lowStockProducts',
            'inventoryValue',
            'replenishmentAlerts',
            'brandValues',
            'maxBrandValue',
            'rotationEstimate',
            'productsWithoutMovement',
            'lastUpdatedAt',
            'brands',
            'tones',
            'categories',
            'suppliers',
            'filters'
        ));
    }
}
