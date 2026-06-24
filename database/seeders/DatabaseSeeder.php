<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\ExchangeRate;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Supplier;
use App\Models\Tone;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Susan',
            'email' => 'admin@susanbrigitt.com',
            'password' => Hash::make('password'),
        ]);

        $makeup = Category::create([
            'name' => 'Maquillaje',
            'slug' => 'maquillaje',
            'description' => 'Productos de maquillaje y belleza.',
            'is_active' => true,
        ]);

        $facialCare = Category::create([
            'name' => 'Cuidado facial',
            'slug' => 'cuidado-facial',
            'description' => 'Productos para cuidado de la piel.',
            'is_active' => true,
        ]);

        $vogue = Brand::create([
            'name' => 'Vogue',
            'slug' => 'vogue',
            'is_active' => true,
        ]);

        $valmy = Brand::create([
            'name' => 'Valmy',
            'slug' => 'valmy',
            'is_active' => true,
        ]);

        $maybelline = Brand::create([
            'name' => 'Maybelline',
            'slug' => 'maybelline',
            'is_active' => true,
        ]);

        $beigeClaro = Tone::create([
            'name' => 'Beige claro',
            'slug' => 'beige-claro',
            'hex_color' => '#E7C7A9',
            'is_active' => true,
        ]);

        $rojoIntenso = Tone::create([
            'name' => 'Rojo intenso',
            'slug' => 'rojo-intenso',
            'hex_color' => '#B32035',
            'is_active' => true,
        ]);

        $negro = Tone::create([
            'name' => 'Negro',
            'slug' => 'negro',
            'hex_color' => '#111111',
            'is_active' => true,
        ]);

        $supplier1 = Supplier::create([
            'name' => 'Proveedoría Beauty C.A.',
            'contact_name' => 'María González',
            'phone' => '+58 412 0000000',
            'email' => 'ventas@beauty.test',
            'address' => 'Caracas, Venezuela',
            'notes' => 'Proveedor principal de maquillaje.',
            'is_active' => true,
        ]);

        $supplier2 = Supplier::create([
            'name' => 'Distribuidora Glam',
            'contact_name' => 'Carlos Pérez',
            'phone' => '+58 414 0000000',
            'email' => 'contacto@glam.test',
            'address' => 'Valencia, Venezuela',
            'is_active' => true,
        ]);

        $rateToday = ExchangeRate::create([
            'rate_date' => '2024-05-21',
            'bcv_rate' => 36.92,
            'binance_rate' => 37.65,
            'manual_rate' => null,
            'used_rate' => 37.65,
            'source' => 'binance',
            'status' => 'active',
            'notes' => 'Tasa usada para compras y ventas del día.',
        ]);

        ExchangeRate::create([
            'rate_date' => '2024-05-20',
            'bcv_rate' => 36.80,
            'binance_rate' => 37.40,
            'manual_rate' => 37.50,
            'used_rate' => 37.50,
            'source' => 'manual',
            'status' => 'reference',
            'notes' => 'Referencia comercial interna.',
        ]);

        ExchangeRate::create([
            'rate_date' => '2024-05-19',
            'bcv_rate' => 36.70,
            'binance_rate' => 37.25,
            'manual_rate' => null,
            'used_rate' => 36.70,
            'source' => 'bcv',
            'status' => 'reference',
            'notes' => 'Reporte formal.',
        ]);

        $products = [
            [
                'category_id' => $makeup->id,
                'brand_id' => $vogue->id,
                'tone_id' => $beigeClaro->id,
                'supplier_id' => $supplier1->id,
                'internal_code' => 'SB-0001',
                'name' => 'Base líquida',
                'purchase_price_usd' => 4.50,
                'sale_price_usd' => 8.00,
                'initial_stock' => 20,
                'current_stock' => 17,
                'minimum_stock' => 5,
                'entry_date' => '2024-05-21',
                'description' => 'Base líquida de cobertura media, acabado natural.',
            ],
            [
                'category_id' => $makeup->id,
                'brand_id' => $valmy->id,
                'tone_id' => $rojoIntenso->id,
                'supplier_id' => $supplier2->id,
                'internal_code' => 'SB-0002',
                'name' => 'Labial mate',
                'purchase_price_usd' => 2.00,
                'sale_price_usd' => 4.50,
                'initial_stock' => 30,
                'current_stock' => 3,
                'minimum_stock' => 5,
                'entry_date' => '2024-05-20',
                'description' => 'Labial mate de alta duración.',
            ],
            [
                'category_id' => $makeup->id,
                'brand_id' => $maybelline->id,
                'tone_id' => $negro->id,
                'supplier_id' => $supplier2->id,
                'internal_code' => 'SB-0003',
                'name' => 'Máscara de pestañas',
                'purchase_price_usd' => 3.80,
                'sale_price_usd' => 7.00,
                'initial_stock' => 15,
                'current_stock' => 0,
                'minimum_stock' => 3,
                'entry_date' => '2024-05-18',
                'description' => 'Máscara para volumen intenso.',
                'status' => 'out_of_stock',
            ],
            [
                'category_id' => $facialCare->id,
                'brand_id' => $vogue->id,
                'tone_id' => $beigeClaro->id,
                'supplier_id' => $supplier1->id,
                'internal_code' => 'SB-0004',
                'name' => 'Corrector líquido',
                'purchase_price_usd' => 2.80,
                'sale_price_usd' => 5.50,
                'initial_stock' => 10,
                'current_stock' => 8,
                'minimum_stock' => 5,
                'entry_date' => '2024-05-19',
                'description' => 'Corrector líquido de cobertura ligera.',
            ],
        ];

        foreach ($products as $productData) {
            $unitProfit = $productData['sale_price_usd'] - $productData['purchase_price_usd'];
            $profitMargin = $productData['sale_price_usd'] > 0
                ? ($unitProfit / $productData['sale_price_usd']) * 100
                : 0;

            $product = Product::create([
                ...$productData,
                'slug' => Str::slug($productData['name'] . '-' . $productData['internal_code']),
                'unit_profit_usd' => $unitProfit,
                'profit_margin' => $profitMargin,
                'status' => $productData['status'] ?? 'active',
            ]);

            InventoryMovement::create([
                'product_id' => $product->id,
                'type' => 'initial',
                'quantity' => $product->initial_stock,
                'stock_after_movement' => $product->initial_stock,
                'movement_date' => $product->entry_date,
                'notes' => 'Carga inicial de inventario.',
            ]);
        }

        $base = Product::where('internal_code', 'SB-0001')->first();
        $labial = Product::where('internal_code', 'SB-0002')->first();
        $mascara = Product::where('internal_code', 'SB-0003')->first();

        $purchase = Purchase::create([
            'supplier_id' => $supplier1->id,
            'exchange_rate_id' => $rateToday->id,
            'purchase_date' => '2024-05-21',
            'total_usd' => 90.00,
            'exchange_rate_value' => 37.65,
            'total_bs' => 3388.50,
            'rate_source' => 'binance',
            'payment_method' => 'pago_movil',
            'notes' => 'Compra inicial de bases líquidas.',
        ]);

        PurchaseItem::create([
            'purchase_id' => $purchase->id,
            'product_id' => $base->id,
            'quantity' => 20,
            'unit_cost_usd' => 4.50,
            'total_usd' => 90.00,
        ]);

        $sale = Sale::create([
            'exchange_rate_id' => $rateToday->id,
            'sale_date' => '2024-05-21',
            'customer_name' => 'Cliente ocasional',
            'total_usd' => 20.50,
            'exchange_rate_value' => 37.65,
            'total_bs' => 771.83,
            'estimated_profit_usd' => 9.50,
            'rate_source' => 'binance',
            'payment_method' => 'pago_movil',
            'notes' => 'Venta registrada como prueba.',
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $base->id,
            'quantity' => 2,
            'unit_price_usd' => 8.00,
            'unit_cost_usd' => 4.50,
            'unit_profit_usd' => 3.50,
            'total_usd' => 16.00,
            'total_profit_usd' => 7.00,
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $labial->id,
            'quantity' => 1,
            'unit_price_usd' => 4.50,
            'unit_cost_usd' => 2.00,
            'unit_profit_usd' => 2.50,
            'total_usd' => 4.50,
            'total_profit_usd' => 2.50,
        ]);

        foreach ([$base, $labial, $mascara] as $product) {
            InventoryMovement::create([
                'product_id' => $product->id,
                'type' => 'sale',
                'quantity' => -1,
                'stock_after_movement' => $product->current_stock,
                'movement_date' => '2024-05-21',
                'notes' => 'Movimiento de prueba asociado a ventas.',
            ]);
        }
    }
}
