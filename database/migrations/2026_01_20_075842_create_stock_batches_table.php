<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockBatchesTable extends Migration
{
    public function up()
    {
        Schema::create('stock_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity_remaining')->default(0);
            $table->integer('quantity_initial');
            $table->decimal('purchase_price', 10, 2);
            $table->string('batch_number')->nullable();
            $table->timestamp('received_date')->useCurrent();
            $table->timestamps();
            
            $table->index(['product_id', 'received_date']);
        });

        // Add batch_id to stock_logs
        Schema::table('stock_logs', function (Blueprint $table) {
            $table->foreignId('batch_id')->nullable()->after('product_id')->constrained('stock_batches')->onDelete('set null');
            $table->decimal('purchase_price', 10, 2)->nullable();
        });

        // Add cost tracking to sale_items
        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('cost_price', 10, 2)->nullable()->comment('FIFO cost at time of sale');
            $table->decimal('profit', 10, 2)->nullable()->comment('Profit per item');
        });
    }

    public function down()
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn(['cost_price', 'profit']);
        });

        Schema::table('stock_logs', function (Blueprint $table) {
            $table->dropForeign('stock_logs_batch_id_foreign');
            $table->dropColumn(['batch_id', 'purchase_price']);
        });

        Schema::dropIfExists('stock_batches');
    }
}