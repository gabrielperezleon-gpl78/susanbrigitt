<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Tone;
use App\Models\UnitMeasure;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ZipArchive;

class ProductCatalogExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_and_exports_require_sign_in(): void
    {
        $this->get(route('products.catalog'))->assertRedirect(route('login'));
        $this->get(route('products.catalog.print'))->assertRedirect(route('login'));
        $this->get(route('products.catalog.xlsx'))->assertRedirect(route('login'));
    }

    public function test_catalog_shows_variants_and_inactive_records(): void
    {
        $tone = Tone::create(['name' => 'Ciruela', 'slug' => 'ciruela']);
        $unit = UnitMeasure::create(['name' => 'Caja', 'slug' => 'caja']);
        Product::create(['internal_code' => 'PR-LAB-001', 'name' => 'Labial mate', 'slug' => 'labial-uno', 'tone_id' => $tone->id]);
        Product::create(['internal_code' => 'PR-LAB-002', 'name' => 'Labial mate', 'slug' => 'labial-dos', 'unit_measure_id' => $unit->id, 'status' => 'inactive']);

        $this->actingAs(User::factory()->create())
            ->get(route('products.catalog'))
            ->assertOk()->assertSee('2 fichas encontradas')->assertSee('PR-LAB-001')
            ->assertSee('PR-LAB-002')->assertSee('Ciruela')->assertSee('Caja')->assertSee('Inactivo');

        $this->get(route('products.catalog', ['status' => 'inactive']))
            ->assertOk()->assertSee('PR-LAB-002')->assertDontSee('PR-LAB-001');
    }

    public function test_purchase_and_sale_selectors_identify_variant_by_code_and_tone(): void
    {
        $tone = Tone::create(['name' => 'Ciruela', 'slug' => 'ciruela']);
        Product::create(['internal_code' => 'PR-LAB-001', 'name' => 'Labial mate',
            'slug' => 'labial-ciruela', 'tone_id' => $tone->id, 'current_stock' => 2]);
        Product::create(['internal_code' => 'PR-LAB-002', 'name' => 'Labial mate',
            'slug' => 'labial-otro', 'current_stock' => 2]);

        $this->actingAs(User::factory()->create());
        foreach (['purchases.create', 'sales.create'] as $route) {
            $this->get(route($route))->assertOk()
                ->assertSee('PR-LAB-001 · Labial mate')
                ->assertSee('Tono: Ciruela')
                ->assertSee('PR-LAB-002 · Labial mate');
        }
    }

    public function test_print_and_excel_include_all_filtered_rows_not_only_first_page(): void
    {
        for ($i = 1; $i <= 51; $i++) {
            Product::create([
                'internal_code' => sprintf('PR-%03d', $i), 'name' => 'Labial '.sprintf('%03d', $i),
                'slug' => 'labial-'.$i, 'status' => $i === 51 ? 'inactive' : 'active',
            ]);
        }
        $this->actingAs(User::factory()->create());
        $this->get(route('products.catalog'))->assertOk()->assertSee('51 fichas encontradas')->assertDontSee('PR-051');
        $this->get(route('products.catalog.print'))->assertOk()->assertSee('PR-051');
        $this->get(route('products.catalog.print', ['status' => 'inactive']))
            ->assertOk()->assertSee('PR-051')->assertDontSee('PR-001');

        $response = $this->get(route('products.catalog.xlsx'));
        $response->assertOk()->assertDownload();
        if (!class_exists(ZipArchive::class)) {
            return;
        }
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($response->baseResponse->getFile()->getPathname()) === true);
        $sheet = $zip->getFromName('xl/worksheets/sheet1.xml');
        $this->assertIsString($sheet);
        $this->assertStringContainsString('PR-051', $sheet);
        $this->assertStringContainsString('autoFilter ref="A1:K52"', $sheet);
        $zip->close();

        $response = $this->get(route('products.catalog.xlsx', ['status' => 'inactive']));
        $response->assertOk();
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($response->baseResponse->getFile()->getPathname()) === true);
        $sheet = $zip->getFromName('xl/worksheets/sheet1.xml');
        $this->assertStringContainsString('PR-051', $sheet);
        $this->assertStringNotContainsString('PR-001', $sheet);
        $zip->close();
    }
}
