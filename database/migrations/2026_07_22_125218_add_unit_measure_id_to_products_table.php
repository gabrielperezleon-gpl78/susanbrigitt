<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('products', 'unit_measure_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedBigInteger('unit_measure_id')->nullable()->after('tone_id');
            });
        }

        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('products', function (Blueprint $table) {
                $table->foreign('unit_measure_id')
                    ->references('id')
                    ->on('unit_measures')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'unit_measure_id')) {
            Schema::table('products', function (Blueprint $table) {
                if (DB::getDriverName() !== 'sqlite') {
                    $table->dropForeign(['unit_measure_id']);
                }

                $table->dropColumn('unit_measure_id');
            });
        }
    }
};
