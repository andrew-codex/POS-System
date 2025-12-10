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
        Schema::create('refund_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('refund_id')->constrained('refunds')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->boolean('is_expired')->default(false);
            $table->boolean('is_damaged')->default(false);
            $table->boolean('is_changed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refund_items');
    }
};
