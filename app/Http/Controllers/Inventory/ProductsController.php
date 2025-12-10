<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\Category;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\DB;
class ProductsController extends Controller
{
    use LogsActivity;

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

   
    $products = $query->orderBy('created_at', 'desc')->paginate(10);
    $products->appends([
        'search' => $search,
        'category' => $selectedCategory,
    ]);

    $categories = Category::all();

    return view('Inventory.products.products', compact('products', 'categories', 'selectedCategory', 'search'));
}

  
    public function create()
    {
        $categories = Category::all();
        return view('Inventory.products.create_products', compact('categories'));
    }

  
public function store(Request $request)
{
    $request->validate([
        'product_name' => 'required|string|max:255',
        'product_description' => 'nullable|string',
        'product_price' => 'required|numeric',
        'product_barcode' => 'nullable|string|max:100',
        'category_id' => 'nullable|exists:categories,id',
        'initial_stock' => 'nullable|integer|min:0',
    ]);

    
    $product = Products::create([
        'product_name' => $request->input('product_name'),
        'product_description' => $request->input('product_description'),
        'product_price' => $request->input('product_price'),
        'product_barcode' => $request->input('product_barcode'),
        'category_id' => $request->input('category_id'),
    ]);

    $this->logActivity("Created Product", [
        "product_id" => $product->id,
        "name"       => $product->product_name
    ]);



    return redirect()->route('inventory.products')->with('success', 'Product created successfully.');
}



    public function edit($id)
    {
        $product = Products::findOrFail($id);
        return view('Inventory.products.edit_products', compact('product'));
    }
    
   
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'product_name' => 'required|string|max:255',
            'product_description' => 'nullable|string',
            'product_price' => 'required|numeric',
            'product_barcode' => 'nullable|string|max:100',
            'product_category' => 'nullable|string|max:100',
        ]);

        $product = Products::findOrFail($id);
        $product->update($validatedData);

        $this->logActivity("Updated Product", [
            "product_id" => $product->id,
            "name"       => $product->product_name
        ]);

        return redirect()->route('inventory.products')->with('success', 'Product updated successfully.');
    }


  
    public function destroy(Products $product, $id)
    {
        Products::findOrFail($id);

        Products::destroy($id);

        $this->logActivity("Deleted Product", [
            "product_id" => $id,
        ]);
        return redirect()->route('inventory.products')->with('success', 'Product deleted successfully.');
    }
}