<?php

namespace Tests\Feature;

use App\Models\ExchangeRate;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EuroOperationalRateTest extends TestCase
{
    use RefreshDatabase;

    public function test_euro_rate_can_be_saved_without_a_binance_quote(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('exchange-rates.store'), [
                'save_mode' => 'create_new',
                'rate_date' => '2026-10-14',
                'rate_time' => '12:00',
                'euro_rate' => '275,50',
                'source' => 'euro',
                'status' => 'active',
            ])
            ->assertRedirect(route('exchange-rates.index'));

        $this->assertDatabaseHas('exchange_rates', [
            'source' => 'euro',
            'euro_rate' => 275.5,
            'used_rate' => 275.5,
        ]);
    }

    public function test_a_sale_keeps_its_euro_rate_after_the_reference_is_edited(): void
    {
        $rate = ExchangeRate::create([
            'rate_date' => '2026-10-14',
            'rate_time' => '12:00',
            'euro_rate' => 275.5,
            'used_rate' => 275.5,
            'source' => 'euro',
            'status' => 'active',
        ]);
        $product = Product::create([
            'name' => 'Labial',
            'slug' => 'labial',
            'internal_code' => 'LAB-001',
            'purchase_price_usd' => 5,
            'sale_price_usd' => 10,
            'current_stock' => 2,
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('sales.create'))
            ->assertOk()
            ->assertSee('Euro');

        $this->post(route('sales.store'), [
            'product_id' => $product->id,
            'sale_date' => '2026-10-14',
            'quantity' => 2,
            'unit_price_usd' => '10',
            'exchange_rate_choice' => $rate->id . '|euro',
            'payment_method' => 'pago_movil',
        ])->assertRedirect(route('sales.index'));

        $sale = Sale::firstOrFail();
        $this->assertSame('euro', $sale->rate_source);
        $this->assertEquals(5510, $sale->total_bs);

        $rate->update(['euro_rate' => 280, 'used_rate' => 280]);
        $this->assertEquals(275.5, $sale->fresh()->exchange_rate_value);
        $this->assertEquals(5510, $sale->fresh()->total_bs);
    }
}
