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
    Schema::create('sale_items', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('sale_id');
        $table->unsignedBigInteger('product_id');
        $table->integer('quantity');
        $table->decimal('price', 10, 2);
        $table->decimal('subtotal', 10, 2);

        $table->timestamps();

        $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
        $table->foreign('product_id')->references('id')->on('products');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
