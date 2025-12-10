<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('stock_logs', function (Blueprint $table) {
        $table->id();

        $table->unsignedBigInteger('product_id');
        $table->enum('type', ['in', 'out']);
        $table->integer('quantity');
        $table->string('remarks')->nullable();
        $table->unsignedBigInteger('user_id')->nullable();

        $table->timestamps();

        $table->foreign('product_id')
              ->references('id')
              ->on('products')
              ->onDelete('cascade');
    });
}

        public function down()
        {
            Schema::dropIfExists('stock_logs');
        }

};
