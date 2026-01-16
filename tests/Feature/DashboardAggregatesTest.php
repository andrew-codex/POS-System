<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Http\Controllers\POS\DashboardController;
use App\Models\Products;
use App\Models\Category;
use App\Models\Sales;
use App\Models\SaleItem;
use App\Models\Stocks;
use App\Models\User;
use Carbon\Carbon;

uses(RefreshDatabase::class);

it('caches dashboard aggregates and returns correct values', function () {
    Cache::flush();

    $today = Carbon::today();
    $yesterday = Carbon::yesterday();


    // Create category and product
    $category = Category::create([
        'category_name' => 'Default',
        'category_description' => 'Default',
    ]);

    $product = Products::create([
        'product_name' => 'Test Product',
        'product_description' => 'Desc',
        'product_price' => 10,
        'product_barcode' => 'TEST123',
        'category_id' => $category->id,
    ]);

    // Create a user for sales.created_by FK
    $user = User::create([
        'name' => 'Tester',
        'email' => 'tester@example.com',
        'password' => 'password',
        'role' => 'cashier',
        'status' => 'active',
    ]);

    // Sale today
    $saleToday = Sales::create([
        'total_amount' => 30,
        'amount_received' => 30,
        'change_amount' => 0,
        'created_by' => $user->id,
        'status' => 'completed'
    ]);
    $saleToday->created_at = $today;
    $saleToday->save();

    SaleItem::create([
        'sale_id' => $saleToday->id,
        'product_id' => $product->id,
        'quantity' => 3,
        'price' => 10,
        'subtotal' => 30,
        'created_at' => $today,
        'updated_at' => $today,
    ]);

    // Sale yesterday
    $saleYesterday = Sales::create([
        'total_amount' => 10,
        'amount_received' => 10,
        'change_amount' => 0,
        'created_by' => $user->id,
        'status' => 'completed'
    ]);
    $saleYesterday->created_at = $yesterday;
    $saleYesterday->save();

    SaleItem::create([
        'sale_id' => $saleYesterday->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'price' => 10,
        'subtotal' => 10,
        'created_at' => $yesterday,
        'updated_at' => $yesterday,
    ]);

    // Low stock
    Stocks::create([
        'product_id' => $product->id,
        'quantity' => 5,
    ]);

    $controller = new DashboardController();
    $view = $controller->index(new Request());

    // The controller returns a View; extract data
    $data = $view->getData();

    expect((int) $data['todayItems'])->toBe(3);
    expect((int) $data['totalSales'])->toBe(30);
    expect((int) $data['diffSales'])->toBe(20);

    $cacheKey = 'dashboard:aggregates:' . $today->toDateString();
    expect(Cache::has($cacheKey))->toBeTrue();
});
