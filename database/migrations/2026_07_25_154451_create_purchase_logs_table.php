<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('purchase_id')
                ->constrained('purchases')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('action', 40);

            $table->text('reason')
                ->nullable();

            $table->json('previous_data')
                ->nullable();

            $table->timestamps();

            $table->index([
                'purchase_id',
                'created_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_logs');
    }
};
