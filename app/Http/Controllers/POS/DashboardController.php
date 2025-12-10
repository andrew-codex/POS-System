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
class DashboardController extends Controller
{

public function index(Request $request)
{   
    $today = Carbon::today();
    $yesterday = Carbon::yesterday();


    $todaySaleIds = Sales::whereDate('created_at', $today)->pluck('id');
    $yesterdaySaleIds = Sales::whereDate('created_at', $yesterday)->pluck('id');

    $todayItems = SaleItem::whereIn('sale_id', $todaySaleIds)->sum('quantity');
    $yesterdayItems = SaleItem::whereIn('sale_id', $yesterdaySaleIds)->sum('quantity');

    $diffItems = $todayItems - $yesterdayItems;
    $diffText = ($diffItems > 0 ? '+' : '') . $diffItems;


    $todayIdsArray = $todaySaleIds->toArray() ?: [0];
    $yesterdayIdsArray = $yesterdaySaleIds->toArray() ?: [0];

    $totals = SaleItem::selectRaw("
        SUM(CASE WHEN sale_id IN (" . implode(',', $todayIdsArray) . ") 
            THEN quantity * price ELSE 0 END) as today_total,
        SUM(CASE WHEN sale_id IN (" . implode(',', $yesterdayIdsArray) . ") 
            THEN quantity * price ELSE 0 END) as yesterday_total
    ")->first();

    $totalSales = $totals->today_total ?? 0;
    $yesterdayTotal = $totals->yesterday_total ?? 0;
    $diffSales = $totalSales - $yesterdayTotal;



    $lowStockItems = Stocks::with('product')
        ->where('quantity', '<', 10)
        ->orderBy('quantity', 'asc')
        ->get();




  
    $topProduct = SaleItem::select('product_id', DB::raw('SUM(quantity) as total_qty'))
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

 
    $productName = Products::where('id', $topProduct->product_id)->value('product_name');

  
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