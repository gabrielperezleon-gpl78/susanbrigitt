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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();

            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('exchange_rate_id')->nullable()->constrained()->nullOnDelete();

            $table->date('purchase_date');

            $table->decimal('total_usd', 12, 2)->default(0);
            $table->decimal('exchange_rate_value', 12, 4)->default(0);
            $table->decimal('total_bs', 14, 2)->default(0);

            $table->enum('rate_source', ['bcv', 'binance', 'manual'])->default('binance');
            $table->enum('payment_method', ['pago_movil', 'transferencia_bs', 'efectivo_usd', 'binance', 'zelle', 'mixto'])->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
