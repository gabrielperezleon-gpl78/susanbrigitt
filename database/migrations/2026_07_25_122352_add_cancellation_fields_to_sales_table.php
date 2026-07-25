<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('status', 20)
                ->default('confirmed')
                ->after('notes')
                ->index();

            $table->timestamp('cancelled_at')
                ->nullable()
                ->after('status');

            $table->unsignedBigInteger('cancelled_by')
                ->nullable()
                ->after('cancelled_at');

            $table->text('cancellation_reason')
                ->nullable()
                ->after('cancelled_by');
        });

        /*
         * SQLite local no requiere agregar esta restriccion
         * mediante una segunda alteracion de la tabla.
         * MariaDB y MySQL si tendran la clave foranea.
         */
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('sales', function (Blueprint $table) {
                $table->foreign('cancelled_by')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['cancelled_by']);
            }

            $table->dropColumn([
                'status',
                'cancelled_at',
                'cancelled_by',
                'cancellation_reason',
            ]);
        });
    }
};
