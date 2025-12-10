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
        $query->whereRaw('LOWER(product_name) LIKE ?', ['%' . strtolower($search) . '%']);
    }


    $filteredProducts = $query->get();
    $categories = Category::all();
    $lowStockItems = Stocks::with('product')
        ->where('quantity', '<', 10)
        ->orderBy('quantity', 'asc')
        ->paginate(8);
    return view('POS.Cart.cart', compact('categories', 'filteredProducts', 'lowStockItems'));
}


  
    public function create()
    {
        //
    }

  
    public function store(Request $request)
    {
        
    }

 
    public function show(string $id)
    {
        
    }

  
    public function edit(string $id)
    {
        
    }

    
    public function update(Request $request, string $id)
    {
        
    }

 
    public function destroy(string $id)
    {
        
    }
}