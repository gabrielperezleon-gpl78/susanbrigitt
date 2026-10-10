<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsPeriodTest extends TestCase
{
    use RefreshDatabase;

    public function test_named_period_ignores_dates_left_in_the_form(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-10-09 12:00:00', 'America/Caracas'));
        try {
            $this->actingAs(User::factory()->create())
                ->get(route('reports.index', [
                    'period' => 'this_month',
                    'date_from' => '2026-09-01',
                    'date_to' => '2026-09-30',
                ]))
                ->assertOk()
                ->assertSee('Resumen del 01/10/2026 al 09/10/2026.')
                ->assertDontSee('Resumen del 01/09/2026 al 30/09/2026.');
        } finally {
            CarbonImmutable::setTestNow();
        }
    }

    public function test_custom_period_requires_both_dates(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('reports.index', ['period' => 'custom']))
            ->assertSessionHasErrors(['date_from', 'date_to']);
    }

    public function test_monthly_summary_includes_confirmed_purchase_and_sale(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-10-09 12:00:00', 'America/Caracas'));
        try {
            $product = Product::create([
                'name' => 'Labial mate', 'slug' => 'labial-ciruela',
                'internal_code' => 'PR-LAB-001', 'current_stock' => 10,
                'purchase_price_usd' => 5,
            ]);
            $purchase = Purchase::create(['purchase_date' => '2026-10-09', 'total_usd' => 60, 'status' => 'confirmed']);
            PurchaseItem::create(['purchase_id' => $purchase->id, 'product_id' => $product->id,
                'quantity' => 12, 'unit_cost_usd' => 5, 'total_usd' => 60]);
            $sale = Sale::create(['sale_date' => '2026-10-09', 'total_usd' => 16,
                'estimated_profit_usd' => 6, 'status' => 'confirmed']);
            SaleItem::create(['sale_id' => $sale->id, 'product_id' => $product->id,
                'quantity' => 2, 'unit_price_usd' => 8, 'unit_cost_usd' => 5,
                'total_usd' => 16, 'total_profit_usd' => 6]);

            $this->actingAs(User::factory()->create())
                ->get(route('reports.index', ['period' => 'this_month']))
                ->assertOk()
                ->assertViewHas('salesTotal', 16.0)
                ->assertViewHas('purchasesTotal', 60.0)
                ->assertViewHas('profitTotal', 6.0)
                ->assertViewHas('inventoryValue', 50.0);
        } finally {
            CarbonImmutable::setTestNow();
        }
    }
}
