<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stocks;
use App\Models\Products;
use App\Models\Category;
use App\Models\Sales;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
class DashboardController extends Controller
{

public function index(Request $request)
{   
    $today = Carbon::today();
    $yesterday = Carbon::yesterday();


    // Cache key is per-day so aggregates update daily; TTL configurable
    $ttl = config('dashboard.cache_ttl', 60);
    $cacheKey = 'dashboard:aggregates:' . $today->toDateString();

    $aggregates = Cache::remember($cacheKey, $ttl, function () use ($today, $yesterday) {
        $todayItems = SaleItem::whereHas('sale', function ($query) use ($today) {
            $query->whereDate('created_at', $today);
        })->sum('quantity');

        $yesterdayItems = SaleItem::whereHas('sale', function ($query) use ($yesterday) {
            $query->whereDate('created_at', $yesterday);
        })->sum('quantity');

        $diffItems = $todayItems - $yesterdayItems;
        $diffText = ($diffItems > 0 ? '+' : '') . $diffItems;

        $totalSales = SaleItem::whereHas('sale', function ($query) use ($today) {
            $query->whereDate('created_at', $today);
        })->sum(DB::raw('quantity * price'));

        $yesterdayTotal = SaleItem::whereHas('sale', function ($query) use ($yesterday) {
            $query->whereDate('created_at', $yesterday);
        })->sum(DB::raw('quantity * price'));

        $diffSales = $totalSales - $yesterdayTotal;

        $threshold = config('inventory.low_stock_threshold', 10);
        $lowStockItems = Stocks::with('product')
            ->where('quantity', '<', $threshold)
            ->orderBy('quantity', 'asc')
            ->get();

        return [
            'todayItems' => $todayItems,
            'yesterdayItems' => $yesterdayItems,
            'diffText' => $diffText,
            'totalSales' => $totalSales,
            'yesterdayTotal' => $yesterdayTotal,
            'diffSales' => $diffSales,
            'lowStockItems' => $lowStockItems,
        ];
    });

    $todayItems = $aggregates['todayItems'];
    $diffText = $aggregates['diffText'];
    $totalSales = $aggregates['totalSales'];
    $diffSales = $aggregates['diffSales'];
    $lowStockItems = $aggregates['lowStockItems'];




  
    $topProduct = SaleItem::select('product_id', DB::raw('SUM(quantity) as total_qty'))
        ->with('product')
        ->groupBy('product_id')
        ->orderByDesc('total_qty')
        ->first();

  
    if (!$topProduct) {
        return view('POS.Dashboard', [
            'lowStockItems' => $lowStockItems,
            'totalSales' => 0,
            'todayItems' => 0,
            'diffText' => 0,
            'diffSales' => 0,
            'dates' => [],
            'quantities' => [],
            'productName' => 'No Sales Data'
        ]);
    }

 
    $productName = $topProduct->product->product_name ?? 'Unknown Product';

  
    $salesOverTime = SaleItem::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(quantity) as qty')
        )
        ->where('product_id', $topProduct->product_id)
        ->groupBy(DB::raw('DATE(created_at)'))
        ->orderBy('date', 'ASC')
        ->get();

    $dates = $salesOverTime->pluck('date');
    $quantities = $salesOverTime->pluck('qty');


 
    return view('POS.Dashboard', compact(
        'lowStockItems',
        'totalSales',
        'todayItems',
        'diffText',
        'diffSales',
        'dates',
        'quantities',
        'productName'
    ));
}


}