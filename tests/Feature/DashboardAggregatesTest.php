<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use App\Services\DashboardService;
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


    $user = User::create([
        'name' => 'Tester',
        'email' => 'tester@example.com',
        'password' => bcrypt('password'),
        'role' => 'cashier',
        'status' => 'active',
    ]);


$saleToday = Sales::create([
    'total_amount' => 30,
    'amount_received' => 30,
    'change_amount' => 0,
    'created_by' => $user->id,
    'status' => 'completed',
    'created_at' => $today,
    'updated_at' => $today,
]);

SaleItem::create([
    'sale_id' => $saleToday->id,
    'product_id' => $product->id,
    'quantity' => 3,
    'price' => 10,
    'subtotal' => 30,

]);


$saleYesterday = Sales::create([
    'total_amount' => 10,
    'amount_received' => 10,
    'change_amount' => 0,
    'created_by' => $user->id,
    'status' => 'completed',
    'created_at' => $yesterday,
    'updated_at' => $yesterday,
]);

SaleItem::create([
    'sale_id' => $saleYesterday->id,
    'product_id' => $product->id,
    'quantity' => 1,
    'price' => 10,
    'subtotal' => 10,

]);


 
    Stocks::create([
        'product_id' => $product->id,
        'quantity' => 5,
    ]);


    $service = app(DashboardService::class);
    $data = $service->getDashboardData($today);

    expect($data['todayItems'])->toBe(3);
    expect($data['totalSales'])->toBe(30);
    expect($data['diffSales'])->toBe(20);

    $cacheKey = 'dashboard:aggregates:' . $today->toDateString();
    expect(Cache::has($cacheKey))->toBeTrue();
});
