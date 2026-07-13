<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        try {
            Schema::table('exchange_rates', function (Blueprint $table) {
                $table->dropUnique('exchange_rates_rate_date_unique');
            });
        } catch (\Throwable $e) {
            // En SQLite local puede variar la forma de eliminar el índice único.
            // Si ya no existe o no puede eliminarse por este método, se continúa.
        }

        if (! Schema::hasColumn('exchange_rates', 'rate_time')) {
            Schema::table('exchange_rates', function (Blueprint $table) {
                $table->time('rate_time')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('exchange_rates', 'rate_time')) {
            Schema::table('exchange_rates', function (Blueprint $table) {
                $table->dropColumn('rate_time');
            });
        }

        try {
            Schema::table('exchange_rates', function (Blueprint $table) {
                $table->unique('rate_date');
            });
        } catch (\Throwable $e) {
            //
        }
    }
};
