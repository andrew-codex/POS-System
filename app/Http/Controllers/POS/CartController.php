<?php

namespace App\Http\Controllers\POS;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\Category;
use App\Models\Stocks;
use Illuminate\Support\Facades\DB;
use App\Services\POS\FifoInventoryService;

class CartController extends Controller
{
    protected FifoInventoryService $fifoService;

    public function __construct(FifoInventoryService $fifoService)
    {
        $this->fifoService = $fifoService;
    }
    public function index(Request $request)
    {
        $categories = Category::select('id', 'category_name')
            ->orderBy('category_name')
            ->get();

   
        $filteredProducts = Products::select('id', 'product_name', 'category_id', 'product_price', 'product_description')
            ->with(['category:id,category_name', 'stock:id,product_id,quantity'])
            ->orderBy('product_name')
            ->limit(100)
            ->get();

        $threshold = config('inventory.low_stock_threshold', 10);
        
   
        $lowStockItems = Stocks::select('id', 'product_id', 'quantity')
            ->with(['product:id,product_name,product_price'])
            ->where('quantity', '>', 0)
            ->where('quantity', '<', $threshold)
            ->orderBy('quantity', 'asc')
            ->limit(8)
            ->get();

        return view('POS.Cart.cart', compact('categories', 'filteredProducts', 'lowStockItems'));
    }

    public function searchProducts(Request $request)
    {
        $search = $request->get('search');
        $selectedCategory = $request->get('category');

        $query = Products::select('id', 'product_name', 'category_id', 'product_price', 'product_description')
            ->with(['category:id,category_name', 'stock:id,product_id,quantity']);

        if (!empty($selectedCategory)) {
            $query->where('category_id', $selectedCategory);
        }

        if (!empty($search)) {
            $escaped = addcslashes($search, '%_');
            $query->where(function($q) use ($escaped) {
                $q->where('product_name', 'LIKE', '%' . $escaped . '%')
                  ->orWhere('product_description', 'LIKE', '%' . $escaped . '%');
            });
        }
        $products = $query->orderBy('product_name')->limit(100)->get();

        return response()->json([
            'products' => $products,
            'count' => $products->count()
        ]);
    }


 
    public function processSale(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $userId = auth()->id();

        DB::beginTransaction();
        try {
            $results = [];
            $totalCost = 0;

            foreach ($data['items'] as $item) {
                $productId = $item['product_id'];
                $quantity = $item['quantity'];

                $deduction = $this->fifoService->deductStock($productId, $quantity, $userId, 'Sale');

                $results[] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'deduction' => $deduction,
                ];

                $totalCost += $deduction['total_cost'] ?? 0;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'results' => $results,
                'total_cost' => $totalCost,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}