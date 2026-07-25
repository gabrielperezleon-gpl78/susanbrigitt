<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\UnitMeasure;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $this->seedCategories();
            $this->seedUnitMeasures();
        });
    }

    private function seedCategories(): void
    {
        $categories = [
            [
                'name' => 'Cuidado Facial (Skincare)',
                'slug' => 'cuidado-facial-skincare',
                'description' => 'Productos de limpieza, hidratación y antienvejecimiento (cremas, sérums y mascarillas).',
                'is_active' => true,
            ],
            [
                'name' => 'Maquillaje',
                'slug' => 'maquillaje',
                'description' => 'Maquillaje (Cosmética Decorativa): Embellecimiento del rostro (bases, correctores, sombras, labiales y rímel).',
                'is_active' => true,
            ],
            [
                'name' => 'Cuidado Capilar',
                'slug' => 'cuidado-capilar',
                'description' => 'Higiene y tratamiento del cabello y cuero cabelludo (shampoos, acondicionadores, tintes y tratamientos).',
                'is_active' => true,
            ],
            [
                'name' => 'Higiene y Cuidado Corporal',
                'slug' => 'higiene-y-cuidado-corporal',
                'description' => 'Hidratantes corporales, cremas, desodorantes y jabones.',
                'is_active' => true,
            ],
            [
                'name' => 'Perfumes y Fragancias',
                'slug' => 'perfumes-y-fragancias',
                'description' => 'Lociones, aguas de tocador y perfumes.',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $data) {
            $category = Category::query()
                ->where('slug', $data['slug'])
                ->orWhere('name', $data['name'])
                ->first();

            if (! $category) {
                $category = new Category();
            }

            $category->forceFill($data);
            $category->save();
        }
    }

    private function seedUnitMeasures(): void
    {
        $unitMeasures = [
            [
                'name' => 'Unidad',
                'slug' => 'unidad',
                'abbreviation' => 'und',
                'is_active' => true,
            ],
            [
                'name' => 'Caja',
                'slug' => 'caja',
                'abbreviation' => 'caja',
                'is_active' => true,
            ],
            [
                'name' => 'Set',
                'slug' => 'set',
                'abbreviation' => 'set',
                'is_active' => true,
            ],
            [
                'name' => 'Paquete',
                'slug' => 'paquete',
                'abbreviation' => 'paq',
                'is_active' => true,
            ],
        ];

        foreach ($unitMeasures as $data) {
            $unitMeasure = UnitMeasure::query()
                ->where('slug', $data['slug'])
                ->orWhere('name', $data['name'])
                ->first();

            if (! $unitMeasure) {
                $unitMeasure = new UnitMeasure();
            }

            $unitMeasure->forceFill($data);
            $unitMeasure->save();
        }
    }
}
