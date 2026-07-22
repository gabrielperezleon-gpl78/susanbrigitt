<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
         * La base de datos debe iniciar sin registros de demostración.
         *
         * La cuenta administrativa se crea manualmente después de ejecutar
         * las migraciones, para evitar guardar credenciales en el repositorio.
         *
         * Proveedores, categorías, marcas, tonos, unidades de medida,
         * productos, tasas, compras y ventas serán registrados desde
         * la aplicación por la usuaria administradora.
         */
    }
}
