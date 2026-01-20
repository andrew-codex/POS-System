<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\Category;
use App\Models\Stocks;
use App\Models\SaleItem;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\DB;
use App\Services\POS\ProductService;
use App\Services\POS\ProductSearchService;
use Exception;

class ProductsController extends Controller
{
    use LogsActivity;
   protected ProductService $productService;
   protected ProductSearchService $searchService;

    public function __construct(ProductService $productService, ProductSearchService $searchService)
    {
        $this->productService = $productService;
        $this->searchService = $searchService;
    }

    /**
     * Display a listing of products
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $selectedCategory = $request->get('category');

        $products = $this->searchService->searchProducts($search, $selectedCategory);


        $products->appends([
            'search' => $search,
            'category' => $selectedCategory,
        ]);

        $categories = $this->searchService->getAllCategories();

        return view('Inventory.products.products', compact('products', 'categories', 'selectedCategory', 'search'));
    }

    /**
     * Show the form for creating a new product
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = $this->searchService->getAllCategories();
        return view('Inventory.products.create_products', compact('categories'));
    }

    /**
     * Store a newly created product in database
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'product_description' => 'nullable|string',
            'product_price' => 'required|numeric',
            'product_barcode' => 'nullable|string|max:100',
            'category_id' => 'nullable|exists:categories,id',
            'initial_stock' => 'nullable|integer|min:0',
        ]);

        $existingProduct = Products::where('product_barcode', $validated['product_barcode'])
            ->first();
        if ($existingProduct) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Product with the same barcode already exists.');
        }

        
        $validated['initial_stock'] = (int) $request->input('initial_stock', 0);

        try {
            $this->productService->createProduct($validated);

            return redirect()->route('inventory.products')
                ->with('success', 'Product created successfully.');

        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create product: ' . $e->getMessage());
        }
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
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $product = Products::findOrFail($id);
        
        $existingProduct = Products::where('product_barcode', $validatedData['product_barcode'])
            ->where('id', '!=', $id)
            ->first();
        if ($existingProduct) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Another product with the same barcode already exists.');
        }

        $product->update($validatedData);

        $this->logActivity("Updated Product", [
            "product_id" => $product->id,
            "name"       => $product->product_name
        ]);

        return redirect()->route('inventory.products')->with('success', 'Product updated successfully.');
    }


  
    public function destroy($id)
    {
        $product = Products::findOrFail($id);

        $hasRefunds = $product->refunds()->exists();
        $hasSaleItems = SaleItem::where('product_id', $id)->exists();
        $hasStock = Stocks::where('product_id', $id)->where('quantity', '>', 0)->exists();

        if ($hasRefunds || $hasSaleItems || $hasStock) {
            $this->logActivity('Failed Delete Product', [
                'product_id' => $id,
                'has_refunds' => $hasRefunds,
                'has_sale_items' => $hasSaleItems,
                'has_stock' => $hasStock,
            ]);

            return redirect()->route('inventory.products')->with('error', 'Product cannot be deleted because it has related records (sales, refunds, or stock). Consider archiving instead.');
        }

        $product->delete();

        $this->logActivity("Deleted Product", [
            "product_id" => $id,
        ]);

        return redirect()->route('inventory.products')->with('success', 'Product deleted successfully.');
    }
}