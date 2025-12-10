<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stocks;
use App\Models\Products;
use App\Models\Category;
use App\Models\Stock_logs as StockLog;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
  
    public function index(Request $request)
    {   
        $search = $request->input('search');
        $selectedCategory = $request->get('category');

        $query = Stocks::with('product.category');

        if ($search) {
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('product_name', 'like', '%' . $search . '%');
            });
        }

        if ($selectedCategory && $selectedCategory !== 'all') {
            $query->whereHas('product.category', function ($q) use ($selectedCategory) {
                $q->where('id', $selectedCategory);
            });
        }

        $stocks = $query->orderBy('created_at', 'desc')->paginate(10);
        $categories = Category::all();


        return view('Inventory.Stock.stock', compact('stocks', 'categories'));
    }

  
    public function create(Request $request)
    {
        $productId = $request->product_id;
        $products = Products::all();

        $selectedProduct = null;
        if ($productId) {
            $selectedProduct = Products::find($productId);
        }
        return view('Inventory.Stock.create_stock', compact('products', 'selectedProduct', 'productId'));
    }

  
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        Stocks::updateOrCreate([
            'product_id' => $request->product_id,
        ], [
            'quantity' => DB::raw('quantity + ' . $request->quantity)
        ]);

        StockLog::create([
        'product_id' => $request->product_id,
        'type'       => 'in',
        'quantity'   => $request->quantity,
        'remarks'    => 'Stock added via form',
        'user_id'    => auth()->id()
    ]);

        return redirect()->route('inventory.stock')->with('success', 'Stock created successfully.');
    }

 
    public function show(string $id)
    {
        //
    }

    
    public function edit($id)
    {
        $stock = Stocks::with('product')->findOrFail($id);
        return view('Inventory.Stock.edit_stock', compact('stock'));
    }

  
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $stock = Stocks::findOrFail($id);
        
            $oldQty = $stock->quantity;
            $newQty = $request->quantity;

        Stocks::where('id', $id)->update([
            'quantity' => $newQty
        ]);

          $difference = $newQty - $oldQty;
          $type = $difference >= 0 ? 'adjustment' : 'adjustment';

         StockLog::create([
            'product_id' => $stock->product_id,
            'type'       => $type,
            'quantity' => abs($difference),
            'remarks'    => 'Stock adjusted via edit form',
            'user_id'    => auth()->id()
        ]);

        return redirect()->route('inventory.stock')->with('success', 'Stock updated successfully.');
    }

 
    public function destroy(string $id)
    {
        //
    }
}
