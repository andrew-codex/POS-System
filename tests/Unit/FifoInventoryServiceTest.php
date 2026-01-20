<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\Products;
use App\Models\Stocks;
use App\Models\StockBatch;
use App\Models\Stock_logs as StockLog;
use App\Services\POS\FifoInventoryService;

beforeEach(function () {
    if (!Schema::hasTable('products')) {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('product_name');
            $table->text('product_description')->nullable();
            $table->decimal('product_price', 10, 2)->default(0);
            $table->string('product_barcode')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->timestamps();
        });
    }

    if (!Schema::hasTable('stocks')) {
        Schema::create('stocks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity')->default(0);
            $table->timestamps();
        });
    }

    if (!Schema::hasTable('stock_batches')) {
        Schema::create('stock_batches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity_remaining')->default(0);
            $table->integer('quantity_initial')->default(0);
            $table->decimal('purchase_price', 10, 2)->default(0);
            $table->string('batch_number')->nullable();
            $table->timestamp('received_date')->nullable();
            $table->timestamps();
        });
    }

    if (!Schema::hasTable('stock_logs')) {
        Schema::create('stock_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->enum('type', ['in', 'out'])->default('in');
            $table->integer('quantity');
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->string('remarks')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }
});

it('adds stock as a batch and creates a stock log', function () {
    $product = Products::create([
        'product_name' => 'Test Product A',
        'product_price' => 10.00
    ]);

    $service = new FifoInventoryService();

    $batch = $service->addStock($product->id, 10, 5.00, 1);

    expect(StockBatch::where('id', $batch->id)->exists())->toBeTrue();
    expect(StockLog::where('batch_id', $batch->id)->where('type', 'in')->exists())->toBeTrue();

    $stock = Stocks::where('product_id', $product->id)->first();
    expect($stock)->not->toBeNull();
    expect($stock->quantity)->toBe(10);
});

it('deducts from oldest batches first and returns correct cost', function () {
    $product = Products::create([
        'product_name' => 'Test Product B',
        'product_price' => 20.00
    ]);

    $service = new FifoInventoryService();

    // Create two batches: first 5 @ 3.00, then 10 @ 4.00
    $batch1 = $service->addStock($product->id, 5, 3.00, 1);
    sleep(1); // ensure different timestamps
    $batch2 = $service->addStock($product->id, 10, 4.00, 1);

    // Deduct 7 units => should take 5 from batch1 and 2 from batch2
    $result = $service->deductStock($product->id, 7, 1, 'Test sale');

    expect($result['total_cost'])->toBe(5 * 3.00 + 2 * 4.00);
    expect($result['average_cost'])->toBe(($result['total_cost']) / 7);

    // Check batches remaining
    $b1 = StockBatch::find($batch1->id);
    $b2 = StockBatch::find($batch2->id);

    expect($b1->quantity_remaining)->toBe(0);
    expect($b2->quantity_remaining)->toBe(8);
});
