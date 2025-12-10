<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\Stocks;
use App\Models\Category;

class productsController extends Controller
{
     public function index(Request $request)
    {
        $search = $request->input('search');
        $selectedCategory = $request->get('category');

        $query = Products::query();

       
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('product_description', 'like', "%{$search}%");
            });
        }

        if ($selectedCategory) {
            $query->where('category_id', $selectedCategory);
        }

        $products = $query->with(['category', 'stock'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->product_name,
                    'description' => $product->product_description,
                    'price' => $product->product_price,
                    'barcode' => $product->product_barcode,
                    'stock' => $product->stock->quantity ?? 0,
                    'low_stock_threshold' => 10,
                    'category' => $product->category->category_name ?? 'Uncategorized',
                    'category_id' => $product->category_id,
                ];
            });
        
        return response()->json($products, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'product_description' => 'nullable|string',
            'product_price' => 'required|numeric|min:0',
            'product_barcode' => 'nullable|string|unique:products,product_barcode',
            'category_id' => 'nullable|exists:categories,id',
            'initial_stock' => 'nullable|integer|min:0',
        ]);

        $product = Products::create([
            'product_name' => $validated['product_name'],
            'product_description' => $validated['product_description'] ?? null,
            'product_price' => $validated['product_price'],
            'product_barcode' => $validated['product_barcode'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
        ]);

     
        if (isset($validated['initial_stock'])) {
            $product->stock()->create([
                'quantity' => $validated['initial_stock'],
            ]);
        }

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product->load(['category', 'stock'])
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $product = Products::findOrFail($id);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        if ($product->stock) {
            $product->stock->update(['quantity' => $validated['quantity']]);
        } else {
            $product->stock()->create(['quantity' => $validated['quantity']]);
        }

        return response()->json([
            'message' => 'Stock updated successfully',
            'product' => $product->load('stock')
        ], 200);
    }
}