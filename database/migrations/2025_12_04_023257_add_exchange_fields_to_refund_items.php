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
        Schema::table('refund_items', function (Blueprint $table) {
            $table->unsignedBigInteger('new_product_id')->nullable()->after('product_id');
            $table->decimal('new_price', 10, 2)->nullable()->after('price');

   
        $table->foreign('new_product_id')->references('id')->on('products')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('refund_items', function (Blueprint $table) {
            //
        });
    }
};
