<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite también utiliza una restricción CHECK para los campos ENUM.
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('exchange_rates', function (Blueprint $table) {
                $table->enum('source', ['bcv', 'binance', 'euro', 'manual'])->default('binance')->change();
            });
        }

        foreach (['purchases', 'sales'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->decimal('exchange_rate_value', 12, 4)->nullable()->change();
                $table->decimal('total_bs', 14, 2)->nullable()->change();
                $table->enum('rate_source', ['bcv', 'binance', 'euro', 'manual'])->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        foreach (['purchases', 'sales'] as $tableName) {
            if (DB::table($tableName)->whereNull('rate_source')->orWhereNull('exchange_rate_value')->orWhereNull('total_bs')->exists()) {
                throw new RuntimeException('Existen operaciones sin tasa. Conserve la migración para no perder el historial.');
            }
        }

        foreach (['purchases', 'sales'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->decimal('exchange_rate_value', 12, 4)->default(0)->change();
                $table->decimal('total_bs', 14, 2)->default(0)->change();
                $table->enum('rate_source', ['bcv', 'binance', 'euro', 'manual'])->default('binance')->change();
            });
        }
    }
};
