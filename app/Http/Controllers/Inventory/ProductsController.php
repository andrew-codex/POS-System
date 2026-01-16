<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\Category;
use App\Models\Stocks;
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
        $escapedSearch = addcslashes($search, '%_');
        $query->where(function($q) use ($escapedSearch) {
            $q->where('product_name', 'like', '%' . $escapedSearch . '%')
              ->orWhere('product_description', 'like', '%' . $escapedSearch . '%');
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

    $categories = Category::select('id', 'category_name')->get();

    return view('Inventory.products.products', compact('products', 'categories', 'selectedCategory', 'search'));
}

  
    public function create()
    {
        $categories = Category::select('id', 'category_name')->get();
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

    
    // Wrap product creation and initial stock creation in a DB transaction
    $initialStock = $request->input('initial_stock');

    DB::beginTransaction();
    try {
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

        if (is_numeric($initialStock) && (int)$initialStock > 0) {
            Stocks::create([
                'product_id' => $product->id,
                'quantity' => (int)$initialStock,
            ]);

            $this->logActivity("Initial Stock Set", [
                "product_id" => $product->id,
                "quantity" => (int)$initialStock,
            ]);
        }

        DB::commit();
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }

    return redirect()->route('inventory.products')->with('success', 'Product created successfully.');
}



    public function edit($id)
    {
        $product = Products::findOrFail($id);
        $categories = Category::all();
        return view('Inventory.products.edit_products', compact('product', 'categories'));
    }
    
   
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'product_name' => 'required|string|max:255',
            'product_description' => 'nullable|string',
            'product_price' => 'required|numeric',
            'product_barcode' => 'nullable|string|max:100',
            'category_id' => 'nullable|string|max:100',
        ]);

        $product = Products::findOrFail($id);
        $product->update($validatedData);

        $this->logActivity("Updated Product", [
            "product_id" => $product->id,
            "name"       => $product->product_name
        ]);

        return redirect()->route('inventory.products')->with('success', 'Product updated successfully.');
    }


  
    public function destroy($id)
    {
        Products::findOrFail($id);

        Products::destroy($id);

        $this->logActivity("Deleted Product", [
            "product_id" => $id,
        ]);
        return redirect()->route('inventory.products')->with('success', 'Product deleted successfully.');
    }
}