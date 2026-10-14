<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Las columnas de fuente son ENUM en MySQL; SQLite las almacena como texto.
        if (DB::getDriverName() === 'mysql') {
            foreach (['exchange_rates' => 'source', 'purchases' => 'rate_source', 'sales' => 'rate_source'] as $table => $column) {
                DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` ENUM('bcv','binance','euro','manual') NOT NULL DEFAULT 'binance'");
            }
        }

        Schema::table('exchange_rates', function (Blueprint $table) {
            $table->decimal('euro_rate', 12, 4)->nullable()->after('bcv_rate');
        });
    }

    public function down(): void
    {
        // Nunca reducir el ENUM si una operación o una tasa ya utiliza Euro.
        foreach (['exchange_rates' => 'source', 'purchases' => 'rate_source', 'sales' => 'rate_source'] as $table => $column) {
            if (DB::table($table)->where($column, 'euro')->exists()) {
                throw new RuntimeException('Hay registros Euro. Conserve la migración para no perder el historial.');
            }
        }

        Schema::table('exchange_rates', function (Blueprint $table) {
            $table->dropColumn('euro_rate');
        });

        if (DB::getDriverName() === 'mysql') {
            foreach (['exchange_rates' => 'source', 'purchases' => 'rate_source', 'sales' => 'rate_source'] as $table => $column) {
                DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` ENUM('bcv','binance','manual') NOT NULL DEFAULT 'binance'");
            }
        }
    }
};
