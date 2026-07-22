<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ExchangeRate;
use App\Models\Product;
use Carbon\CarbonImmutable;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReportsController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'period' => [
                'nullable',
                Rule::in([
                    'this_month',
                    'previous_month',
                    'last_3_months',
                    'this_year',
                    'custom',
                ]),
            ],
            'date_from' => [
                'nullable',
                'date_format:Y-m-d',
            ],
            'date_to' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:date_from',
            ],
            'category_id' => [
                'nullable',
                'integer',
                'exists:categories,id',
            ],
        ]);

        $period = $validated['period'] ?? 'this_month';

        $categoryId = isset($validated['category_id'])
            ? (int) $validated['category_id']
            : null;

        [$period, $dateFrom, $dateTo] = $this->resolveDateRange(
            $period,
            $validated
        );

        /*
        |--------------------------------------------------------------------------
        | Ventas y ganancias
        |--------------------------------------------------------------------------
        */

        $salesQuery = $this->salesItemsQuery(
            $dateFrom,
            $dateTo,
            $categoryId
        );

        $salesTotal = (float) (
            (clone $salesQuery)->sum('sale_items.total_usd') ?? 0
        );

        $profitTotal = (float) (
            (clone $salesQuery)->sum('sale_items.total_profit_usd') ?? 0
        );

        $costOfGoodsSold = (float) (
            (clone $salesQuery)
            ->selectRaw(
                'COALESCE(
                        SUM(
                            sale_items.unit_cost_usd *
                            sale_items.quantity
                        ),
                        0
                    ) AS total'
            )
            ->value('total') ?? 0
        );

        $salesBs = (float) (
            (clone $salesQuery)
            ->selectRaw(
                'COALESCE(
                        SUM(
                            sale_items.total_usd *
                            sales.exchange_rate_value
                        ),
                        0
                    ) AS total'
            )
            ->value('total') ?? 0
        );

        $profitBs = (float) (
            (clone $salesQuery)
            ->selectRaw(
                'COALESCE(
                        SUM(
                            sale_items.total_profit_usd *
                            sales.exchange_rate_value
                        ),
                        0
                    ) AS total'
            )
            ->value('total') ?? 0
        );

        $salesCount = (int) (
            (clone $salesQuery)
            ->distinct()
            ->count('sales.id')
        );

        $unitsSold = (int) (
            (clone $salesQuery)->sum('sale_items.quantity') ?? 0
        );

        $ticketAverage = $salesCount > 0
            ? $salesTotal / $salesCount
            : 0;

        $profitMargin = $salesTotal > 0
            ? ($profitTotal / $salesTotal) * 100
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Compras
        |--------------------------------------------------------------------------
        */

        $purchasesQuery = $this->purchaseItemsQuery(
            $dateFrom,
            $dateTo,
            $categoryId
        );

        $purchasesTotal = (float) (
            (clone $purchasesQuery)
            ->sum('purchase_items.total_usd') ?? 0
        );

        $purchasedUnits = (int) (
            (clone $purchasesQuery)
            ->sum('purchase_items.quantity') ?? 0
        );

        $purchasesBs = (float) (
            (clone $purchasesQuery)
            ->selectRaw(
                'COALESCE(
                        SUM(
                            purchase_items.total_usd *
                            purchases.exchange_rate_value
                        ),
                        0
                    ) AS total'
            )
            ->value('total') ?? 0
        );

        /*
        |--------------------------------------------------------------------------
        | Inventario actual
        |--------------------------------------------------------------------------
        */

        $inventoryQuery = Product::query()
            ->when(
                $categoryId,
                fn($query) => $query->where(
                    'category_id',
                    $categoryId
                )
            );

        $inventoryValue = (float) (
            (clone $inventoryQuery)
            ->selectRaw(
                'COALESCE(
                        SUM(
                            current_stock *
                            purchase_price_usd
                        ),
                        0
                    ) AS total'
            )
            ->value('total') ?? 0
        );

        $availableUnits = (int) (
            (clone $inventoryQuery)->sum('current_stock') ?? 0
        );

        $totalProducts = (int) (
            (clone $inventoryQuery)->count()
        );

        $lowStockCount = (int) (
            (clone $inventoryQuery)
            ->where('current_stock', '>', 0)
            ->whereColumn(
                'current_stock',
                '<=',
                'minimum_stock'
            )
            ->count()
        );

        $outOfStockCount = (int) (
            (clone $inventoryQuery)
            ->where('current_stock', '<=', 0)
            ->count()
        );

        /*
        |--------------------------------------------------------------------------
        | Comparación con el período anterior
        |--------------------------------------------------------------------------
        */

        $periodDays = $dateFrom->diffInDays($dateTo) + 1;

        $previousDateTo = $dateFrom->subDay();

        $previousDateFrom = $previousDateTo->subDays(
            $periodDays - 1
        );

        $previousSalesQuery = $this->salesItemsQuery(
            $previousDateFrom,
            $previousDateTo,
            $categoryId
        );

        $previousSalesTotal = (float) (
            (clone $previousSalesQuery)
            ->sum('sale_items.total_usd') ?? 0
        );

        $previousProfitTotal = (float) (
            (clone $previousSalesQuery)
            ->sum('sale_items.total_profit_usd') ?? 0
        );

        $salesChange = $this->percentageChange(
            $salesTotal,
            $previousSalesTotal
        );

        $profitChange = $this->percentageChange(
            $profitTotal,
            $previousProfitTotal
        );

        /*
        |--------------------------------------------------------------------------
        | Evolución mensual
        |--------------------------------------------------------------------------
        */

        $monthNames = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];

        $monthlySeries = collect(range(5, 0))
            ->map(function (int $offset) use (
                $dateTo,
                $categoryId,
                $monthNames
            ) {
                $monthStart = $dateTo
                    ->startOfMonth()
                    ->subMonths($offset);

                $monthEnd = $monthStart->endOfMonth();

                $query = $this->salesItemsQuery(
                    $monthStart,
                    $monthEnd,
                    $categoryId
                );

                return [
                    'key' => $monthStart->format('Y-m'),
                    'name' => $monthNames[$monthStart->month],
                    'short_name' => mb_substr(
                        $monthNames[$monthStart->month],
                        0,
                        3
                    ),
                    'sales' => (float) (
                        (clone $query)
                        ->sum('sale_items.total_usd') ?? 0
                    ),
                    'profit' => (float) (
                        (clone $query)
                        ->sum(
                            'sale_items.total_profit_usd'
                        ) ?? 0
                    ),
                ];
            });

        $monthlyMaxSales = (float) (
            $monthlySeries->max('sales') ?? 0
        );

        $monthlyMaxProfit = (float) (
            $monthlySeries->max('profit') ?? 0
        );

        /*
        |--------------------------------------------------------------------------
        | Productos más vendidos
        |--------------------------------------------------------------------------
        */

        $topProducts = DB::table('sale_items')
            ->join(
                'sales',
                'sales.id',
                '=',
                'sale_items.sale_id'
            )
            ->join(
                'products',
                'products.id',
                '=',
                'sale_items.product_id'
            )
            ->leftJoin(
                'brands',
                'brands.id',
                '=',
                'products.brand_id'
            )
            ->whereBetween(
                'sales.sale_date',
                [
                    $dateFrom->toDateString(),
                    $dateTo->toDateString(),
                ]
            )
            ->when(
                $categoryId,
                fn($query) => $query->where(
                    'products.category_id',
                    $categoryId
                )
            )
            ->select([
                'products.id',
                'products.name',
                'brands.name as brand_name',
            ])
            ->selectRaw(
                'SUM(sale_items.quantity) AS units_sold'
            )
            ->selectRaw(
                'SUM(sale_items.total_usd) AS sales_total'
            )
            ->groupBy(
                'products.id',
                'products.name',
                'brands.name'
            )
            ->orderByDesc('units_sold')
            ->limit(5)
            ->get();

        $maxTopProductUnits = (int) (
            $topProducts->max('units_sold') ?? 0
        );

        /*
        |--------------------------------------------------------------------------
        | Tasas y conversión del inventario
        |--------------------------------------------------------------------------
        */

        $latestRate = ExchangeRate::query()
            ->orderByDesc('rate_date')
            ->orderByDesc('rate_time')
            ->orderByDesc('id')
            ->first();

        $todayRateExists = ExchangeRate::query()
            ->whereDate(
                'rate_date',
                now()->toDateString()
            )
            ->exists();

        $inventoryValueBs = $latestRate
            ? $inventoryValue * (float) $latestRate->used_rate
            : null;

        $costOfGoodsSoldBs = max(
            0,
            $salesBs - $profitBs
        );

        /*
        |--------------------------------------------------------------------------
        | Indicadores
        |--------------------------------------------------------------------------
        */

        $rotationRatio = $availableUnits > 0
            ? $unitsSold / $availableUnits
            : 0;

        $rotationEstimate = match (true) {
            $unitsSold === 0 => 'Sin datos',
            $rotationRatio >= 1 => 'Alta',
            $rotationRatio >= 0.30 => 'Media',
            default => 'Baja',
        };

        $financialStatus = match (true) {
            $salesTotal <= 0 => 'Sin datos',
            $profitTotal > 0 => 'Positivo',
            default => 'Atención',
        };

        $categories = Category::query()
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        return view('reports.index', compact(
            'period',
            'dateFrom',
            'dateTo',
            'categoryId',
            'categories',
            'salesTotal',
            'profitTotal',
            'costOfGoodsSold',
            'salesBs',
            'profitBs',
            'costOfGoodsSoldBs',
            'salesCount',
            'unitsSold',
            'ticketAverage',
            'profitMargin',
            'purchasesTotal',
            'purchasedUnits',
            'purchasesBs',
            'inventoryValue',
            'inventoryValueBs',
            'availableUnits',
            'totalProducts',
            'lowStockCount',
            'outOfStockCount',
            'salesChange',
            'profitChange',
            'monthlySeries',
            'monthlyMaxSales',
            'monthlyMaxProfit',
            'topProducts',
            'maxTopProductUnits',
            'latestRate',
            'todayRateExists',
            'rotationEstimate',
            'financialStatus'
        ));
    }

    private function resolveDateRange(
        string $period,
        array $validated
    ): array {
        $today = CarbonImmutable::today(
            config('app.timezone')
        );

        if (
            ! empty($validated['date_from']) &&
            ! empty($validated['date_to'])
        ) {
            return [
                'custom',
                CarbonImmutable::parse(
                    $validated['date_from'],
                    config('app.timezone')
                )->startOfDay(),
                CarbonImmutable::parse(
                    $validated['date_to'],
                    config('app.timezone')
                )->endOfDay(),
            ];
        }

        return match ($period) {
            'previous_month' => [
                $period,
                $today
                    ->subMonthNoOverflow()
                    ->startOfMonth(),
                $today
                    ->subMonthNoOverflow()
                    ->endOfMonth(),
            ],

            'last_3_months' => [
                $period,
                $today
                    ->subMonths(2)
                    ->startOfMonth(),
                $today->endOfDay(),
            ],

            'this_year' => [
                $period,
                $today->startOfYear(),
                $today->endOfDay(),
            ],

            default => [
                'this_month',
                $today->startOfMonth(),
                $today->endOfDay(),
            ],
        };
    }

    private function salesItemsQuery(
        CarbonImmutable $dateFrom,
        CarbonImmutable $dateTo,
        ?int $categoryId
    ): Builder {
        return DB::table('sale_items')
            ->join(
                'sales',
                'sales.id',
                '=',
                'sale_items.sale_id'
            )
            ->join(
                'products',
                'products.id',
                '=',
                'sale_items.product_id'
            )
            ->whereBetween(
                'sales.sale_date',
                [
                    $dateFrom->toDateString(),
                    $dateTo->toDateString(),
                ]
            )
            ->when(
                $categoryId,
                fn($query) => $query->where(
                    'products.category_id',
                    $categoryId
                )
            );
    }

    private function purchaseItemsQuery(
        CarbonImmutable $dateFrom,
        CarbonImmutable $dateTo,
        ?int $categoryId
    ): Builder {
        return DB::table('purchase_items')
            ->join(
                'purchases',
                'purchases.id',
                '=',
                'purchase_items.purchase_id'
            )
            ->join(
                'products',
                'products.id',
                '=',
                'purchase_items.product_id'
            )
            ->whereBetween(
                'purchases.purchase_date',
                [
                    $dateFrom->toDateString(),
                    $dateTo->toDateString(),
                ]
            )
            ->when(
                $categoryId,
                fn($query) => $query->where(
                    'products.category_id',
                    $categoryId
                )
            );
    }

    private function percentageChange(
        float $currentValue,
        float $previousValue
    ): ?float {
        if ($previousValue <= 0) {
            return null;
        }

        return (
            ($currentValue - $previousValue) /
            $previousValue
        ) * 100;
    }
}
