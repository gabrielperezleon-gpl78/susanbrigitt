<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();

            $table->date('rate_date')->unique();

            $table->decimal('bcv_rate', 12, 4)->nullable();
            $table->decimal('binance_rate', 12, 4)->nullable();
            $table->decimal('manual_rate', 12, 4)->nullable();
            $table->decimal('used_rate', 12, 4);

            $table->enum('source', ['bcv', 'binance', 'manual'])->default('binance');
            $table->enum('status', ['active', 'reference', 'archived'])->default('active');

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
