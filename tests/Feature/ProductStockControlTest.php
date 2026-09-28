<?php

namespace Tests\Feature;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductStockControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_code_is_required_before_saving(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('products.store'), $this->productData(['internal_code' => '']))
            ->assertSessionHasErrors('internal_code');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_opening_stock_creates_one_initial_movement(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('products.store'), $this->productData())
            ->assertRedirect(route('products.index'));

        $product = Product::firstOrFail();

        $this->assertSame(6, $product->current_stock);
        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $product->id,
            'type' => 'initial',
            'quantity' => 6,
            'stock_after_movement' => 6,
        ]);
    }

    public function test_product_edit_cannot_change_stock_even_with_crafted_fields(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Labial',
            'slug' => 'labial',
            'internal_code' => 'SB-LAB-001',
            'initial_stock' => 6,
            'current_stock' => 4,
        ]);

        $this->actingAs($user)
            ->put(route('products.update', $product), array_merge($this->productData(), [
                'initial_stock' => 90,
                'current_stock' => 90,
                'entry_date' => '2030-01-01',
            ]))
            ->assertRedirect(route('products.index'));

        $this->assertSame(6, $product->fresh()->initial_stock);
        $this->assertSame(4, $product->fresh()->current_stock);
    }

    public function test_physical_count_adjustment_records_direction_and_reason(): void
    {
        $user = User::factory()->create(['name' => 'Susan']);
        $product = Product::create([
            'name' => 'Labial',
            'slug' => 'labial',
            'internal_code' => 'SB-LAB-001',
            'initial_stock' => 6,
            'current_stock' => 6,
        ]);

        $this->actingAs($user)
            ->post(route('products.adjust-stock', $product), [
                'counted_stock' => 4,
                'expected_stock' => 6,
                'reason' => 'Conteo físico de cierre',
            ])
            ->assertRedirect(route('products.show', $product));

        $this->assertSame(4, $product->fresh()->current_stock);
        $movement = InventoryMovement::where('product_id', $product->id)->firstOrFail();
        $this->assertSame('adjustment_out', $movement->type);
        $this->assertSame(2, $movement->quantity);
        $this->assertSame(4, $movement->stock_after_movement);
        $this->assertStringContainsString('Susan', $movement->notes);
        $this->assertStringContainsString('Conteo físico de cierre', $movement->notes);
    }

    public function test_adjustment_rejects_stale_stock_before_writing_a_movement(): void
    {
        $product = Product::create([
            'name' => 'Labial',
            'slug' => 'labial',
            'internal_code' => 'SB-LAB-001',
            'current_stock' => 5,
        ]);

        $this->actingAs(User::factory()->create())
            ->post(route('products.adjust-stock', $product), [
                'counted_stock' => 4,
                'expected_stock' => 6,
                'reason' => 'Conteo físico de cierre',
            ])
            ->assertSessionHasErrors('counted_stock');

        $this->assertSame(5, $product->fresh()->current_stock);
        $this->assertDatabaseCount('inventory_movements', 0);
    }

    private function productData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Labial',
            'internal_code' => 'SB-LAB-001',
            'purchase_price_usd' => '5,00',
            'sale_price_usd' => '8,00',
            'initial_stock' => 6,
            'minimum_stock' => 2,
            'status' => 'active',
        ], $overrides);
    }
}
