<?php

namespace App\Http\Controllers\POS;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\Category;
use App\Models\Stocks;
use Illuminate\Support\Facades\Cache;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $selectedCategory = $request->get('category');
        $page = $request->get('page', 1);

        $cacheKey = "products_{$selectedCategory}_{$search}_p{$page}";
        
        $filteredProducts = Cache::remember($cacheKey, 600, function () use ($search, $selectedCategory) {
            $query = Products::select('id', 'product_name', 'category_id', 'product_price')
                ->with(['category:id,category_name']);

            if (!empty($selectedCategory)) {
                $query->where('category_id', $selectedCategory);
            }

            if (!empty($search)) {
                $escaped = addcslashes($search, '%_');
                $query->where('product_name', 'LIKE', '%' . $escaped . '%');
            }

            return $query->paginate(20);
        });

        $categories = Cache::remember('categories_list', 3600, function () {
            return Category::select('id', 'category_name')
                ->orderBy('category_name')
                ->get();
        });

        $threshold = config('inventory.low_stock_threshold', 10);
        
        $lowStockItems = Cache::remember('low_stock_items', 600, function () use ($threshold) {
            return Stocks::select('id', 'product_id', 'quantity')
                ->with(['product:id,product_name,product_price'])
                ->where('quantity', '<', $threshold)
                ->orderBy('quantity', 'asc')
                ->limit(8)
                ->get();
        });

        return view('POS.Cart.cart', compact('categories', 'filteredProducts', 'lowStockItems'));
    }
}