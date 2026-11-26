<?php

namespace Tests\Feature;

use App\Models\ExchangeRate;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsdOperationWithoutRateTest extends TestCase
{
    use RefreshDatabase;

    private function product(): Product
    {
        return Product::create([
            'name' => 'Labial',
            'slug' => 'labial',
            'internal_code' => 'LAB-001',
            'purchase_price_usd' => 5,
            'sale_price_usd' => 10,
            'current_stock' => 10,
        ]);
    }

    public function test_purchase_can_be_created_and_edited_in_usd_without_a_rate(): void
    {
        $this->actingAs(User::factory()->create());
        $product = $this->product();
        $supplier = Supplier::create(['name' => 'Proveedor']);
        $data = [
            'supplier_id' => $supplier->id,
            'product_id' => $product->id,
            'purchase_date' => '2026-11-26',
            'quantity' => 2,
            'unit_cost_usd' => 6,
            'payment_method' => 'efectivo_usd',
        ];

        $this->post(route('purchases.store'), $data)
            ->assertRedirect(route('purchases.index'));
        $purchase = Purchase::firstOrFail();
        $this->assertEquals(12, $purchase->total_usd);
        $this->assertNull($purchase->exchange_rate_id);
        $this->assertNull($purchase->exchange_rate_value);
        $this->assertNull($purchase->rate_source);
        $this->assertNull($purchase->total_bs);
        $this->assertEquals(12, $product->fresh()->current_stock);

        $this->put(route('purchases.update', $purchase), array_merge($data, [
            'quantity' => 3, 'payment_method' => 'zelle',
        ]))->assertRedirect(route('purchases.index'));
        $this->assertEquals(18, $purchase->fresh()->total_usd);
        $this->assertNull($purchase->fresh()->total_bs);
        $this->assertEquals(13, $product->fresh()->current_stock);
    }

    public function test_sale_can_be_created_and_edited_in_usd_without_a_rate(): void
    {
        $this->actingAs(User::factory()->create());
        $product = $this->product();
        $data = [
            'product_id' => $product->id,
            'sale_date' => '2026-11-26',
            'quantity' => 2,
            'unit_price_usd' => 10,
            'payment_method' => 'zelle',
        ];

        $this->post(route('sales.store'), $data)
            ->assertRedirect(route('sales.index'));
        $sale = Sale::firstOrFail();
        $this->assertEquals(20, $sale->total_usd);
        $this->assertEquals(10, $sale->estimated_profit_usd);
        $this->assertNull($sale->exchange_rate_id);
        $this->assertNull($sale->exchange_rate_value);
        $this->assertNull($sale->rate_source);
        $this->assertNull($sale->total_bs);
        $this->assertEquals(8, $product->fresh()->current_stock);

        $this->put(route('sales.update', $sale), array_merge($data, [
            'quantity' => 3, 'payment_method' => 'binance',
        ]))->assertRedirect(route('sales.index'));
        $this->assertEquals(30, $sale->fresh()->total_usd);
        $this->assertEquals(15, $sale->fresh()->estimated_profit_usd);
        $this->assertNull($sale->fresh()->total_bs);
        $this->assertEquals(7, $product->fresh()->current_stock);
    }

    public function test_bs_and_mixed_operations_still_require_a_rate_for_both_modules(): void
    {
        $this->actingAs(User::factory()->create());
        $product = $this->product();
        $supplier = Supplier::create(['name' => 'Proveedor']);
        $purchaseData = [
            'supplier_id' => $supplier->id, 'product_id' => $product->id,
            'purchase_date' => '2026-11-26', 'quantity' => 2, 'unit_cost_usd' => 6,
        ];
        $saleData = [
            'product_id' => $product->id, 'sale_date' => '2026-11-26',
            'quantity' => 2, 'unit_price_usd' => 10,
        ];

        foreach (['pago_movil', 'transferencia_bs', 'mixto'] as $method) {
            $this->post(route('purchases.store'), $purchaseData + ['payment_method' => $method])
                ->assertSessionHasErrors('exchange_rate_choice');
            $this->post(route('sales.store'), $saleData + ['payment_method' => $method])
                ->assertSessionHasErrors('exchange_rate_choice');
        }
        $this->assertDatabaseCount('purchases', 0);
        $this->assertDatabaseCount('sales', 0);
        $this->assertEquals(10, $product->fresh()->current_stock);

        $rate = ExchangeRate::create([
            'rate_date' => '2026-11-26', 'rate_time' => '12:00',
            'bcv_rate' => 200, 'used_rate' => 200, 'source' => 'bcv', 'status' => 'active',
        ]);
        $payment = ['payment_method' => 'pago_movil', 'exchange_rate_choice' => $rate->id . '|bcv'];
        $this->post(route('purchases.store'), $purchaseData + $payment)
            ->assertRedirect(route('purchases.index'));
        $this->post(route('sales.store'), $saleData + $payment)
            ->assertRedirect(route('sales.index'));
        $this->assertEquals(2400, Purchase::firstOrFail()->total_bs);
        $this->assertEquals(4000, Sale::firstOrFail()->total_bs);

        $rate->update(['bcv_rate' => 250, 'used_rate' => 250]);
        $purchase = Purchase::firstOrFail();
        $sale = Sale::firstOrFail();
        $this->put(route('purchases.update', $purchase), $purchaseData + $payment)
            ->assertRedirect(route('purchases.index'));
        $this->put(route('sales.update', $sale), $saleData + $payment)
            ->assertRedirect(route('sales.index'));
        $this->assertEquals(200, $purchase->fresh()->exchange_rate_value);
        $this->assertEquals(200, $sale->fresh()->exchange_rate_value);
    }

    public function test_create_forms_default_to_usd_even_without_rates(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        foreach (['purchases.create', 'sales.create'] as $route) {
            $this->get(route($route))->assertOk()
                ->assertSee('value="efectivo_usd" selected', false)
                ->assertSee('Puedes guardar esta operación en dólares sin registrar una tasa.');
        }
    }
}
