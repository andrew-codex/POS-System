<?php

namespace App\Services;

use App\Models\SaleItem;
use App\Models\Stocks;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getDashboardData(?Carbon $today = null): array
    {
        $today = $today ?? Carbon::today();
        $yesterday = (clone $today)->subDay();

        $ttl = Config::get('dashboard.cache_ttl', 60);
        $cacheKey = 'dashboard:aggregates:' . $today->toDateString();

        return Cache::remember($cacheKey, $ttl, function () use ($today, $yesterday) {

            $todayItems = SaleItem::whereDate('created_at', $today)
                ->whereHas('sale', fn ($q) =>
                    $q->whereDate('created_at', $today)
                )
                ->sum('quantity');

            $yesterdayItems = SaleItem::whereDate('created_at', $yesterday)
                ->whereHas('sale', fn ($q) =>
                    $q->whereDate('created_at', $yesterday)
                )
                ->sum('quantity');

            $diffItems = $todayItems - $yesterdayItems;
            $diffText = ($diffItems > 0 ? '+' : '') . $diffItems;

            $totalSales = SaleItem::whereDate('created_at', $today)
                ->whereHas('sale', fn ($q) =>
                    $q->whereDate('created_at', $today)
                )
                ->sum(DB::raw('quantity * price'));

            $yesterdayTotal = SaleItem::whereDate('created_at', $yesterday)
                ->whereHas('sale', fn ($q) =>
                    $q->whereDate('created_at', $yesterday)
                )
                ->sum(DB::raw('quantity * price'));

            $diffSales = $totalSales - $yesterdayTotal;

            $lowStockItems = Stocks::with('product')
                ->where('quantity', '<', Config::get('inventory.low_stock_threshold', 10))
                ->orderBy('quantity')
                ->get();

            $topProduct = SaleItem::select('product_id', DB::raw('SUM(quantity) as total_qty'))
                ->groupBy('product_id')
                ->orderByDesc('total_qty')
                ->with('product')
                ->first();

            $dates = collect();
            $quantities = collect();
            $productName = 'No Sales Data';

            if ($topProduct && $topProduct->product) {
                $productName = $topProduct->product->product_name;

                $salesOverTime = SaleItem::select(
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('SUM(quantity) as qty')
                    )
                    ->where('product_id', $topProduct->product_id)
                    ->groupBy(DB::raw('DATE(created_at)'))
                    ->orderBy('date')
                    ->get();

                $dates = $salesOverTime->pluck('date');
                $quantities = $salesOverTime->pluck('qty');
            }

            return [
                'lowStockItems' => $lowStockItems,
                'totalSales'    => $totalSales,
                'todayItems'    => $todayItems,
                'diffText'      => $diffText,
                'diffSales'     => $diffSales,
                'dates'         => $dates,
                'quantities'    => $quantities,
                'productName'   => $productName,
            ];
        });
    }
}
