<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Products;
use App\Models\Category;
use App\Models\Stocks;
class CartController extends Controller
{
   
  public function index(Request $request)
{
    $search = $request->get('search');
    $selectedCategory = $request->get('category');

    $query = Products::query();

   
    if (!empty($selectedCategory)) {
        $query->where('category_id', $selectedCategory);
    }

 
    if (!empty($search)) {
        $escaped = addcslashes($search, '%_');
        $query->where('product_name', 'LIKE', '%' . $escaped . '%');
    }


    $filteredProducts = $query->paginate(8);
    $categories = Category::select('id', 'category_name')->get();
    $threshold = config('inventory.low_stock_threshold', 10);
    $lowStockItems = Stocks::with('product')
        ->where('quantity', '<', $threshold)
        ->orderBy('quantity', 'asc')
        ->paginate(8);
    return view('POS.Cart.cart', compact('categories', 'filteredProducts', 'lowStockItems'));
}


  
   
}