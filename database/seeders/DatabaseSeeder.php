<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
         * Se cargan únicamente los datos maestros indispensables.
         * No se crean usuarios, productos, proveedores, tasas,
         * compras, ventas ni registros de demostración.
         */
        $this->call([
            MasterDataSeeder::class,
        ]);
    }
}
