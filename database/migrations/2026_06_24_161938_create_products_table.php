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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('tone_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();

            $table->string('internal_code')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('barcode')->nullable();

            $table->text('description')->nullable();
            $table->string('image_path')->nullable();

            $table->decimal('purchase_price_usd', 10, 2)->default(0);
            $table->decimal('sale_price_usd', 10, 2)->default(0);
            $table->decimal('unit_profit_usd', 10, 2)->default(0);
            $table->decimal('profit_margin', 8, 2)->default(0);

            $table->integer('initial_stock')->default(0);
            $table->integer('current_stock')->default(0);
            $table->integer('minimum_stock')->default(0);

            $table->date('entry_date')->nullable();

            $table->enum('status', ['active', 'inactive', 'out_of_stock'])->default('active');

            $table->text('internal_notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
