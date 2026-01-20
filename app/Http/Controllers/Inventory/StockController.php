<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stocks;
use App\Models\Products;
use App\Models\Category;
use App\Models\Stock_logs as StockLog;
use App\Models\StockBatch;
use App\Services\POS\FifoInventoryService;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    protected $fifoService;

    public function __construct(FifoInventoryService $fifoService)
    {
        $this->fifoService = $fifoService;
    }

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
        $products = Products::select('id', 'product_name', 'product_price')->get();

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
            'purchase_price' => 'required|numeric|min:0',
        ]);

        try {
            $batch = $this->fifoService->addStock(
                $request->product_id,
                $request->quantity,
                $request->purchase_price,
                auth()->id()
            );

            return redirect()->route('inventory.stock')
                ->with('success', "Stock added successfully. Batch #" . $batch->batch_number);
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error adding stock: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $stock = Stocks::with('product')->findOrFail($id);
     
        $batches = StockBatch::where('product_id', $stock->product_id)
            ->where('quantity_remaining', '>', 0)
            ->orderBy('received_date', 'asc')
            ->get();
            
        return view('Inventory.Stock.edit_stock', compact('stock', 'batches'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
            'adjustment_reason' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $id) {
            $stock = Stocks::findOrFail($id);
            $oldQty = $stock->quantity;
            $newQty = $request->quantity;
            $difference = $newQty - $oldQty;

            if ($difference == 0) {
                return;
            }

          
            $stock->update(['quantity' => $newQty]);

          
            if ($difference > 0) {

                $product = Products::find($stock->product_id);
                $reason = $request->input('adjustment_reason', 'Stock adjustment (increase)');

                StockBatch::create([
                    'product_id' => $stock->product_id,
                    'quantity_remaining' => abs($difference),
                    'quantity_initial' => abs($difference),
                    'purchase_price' => $product->product_price ?? 0,
                    'batch_number' => 'ADJ-' . now()->format('YmdHis'),
                    'received_date' => now()
                ]);

                StockLog::create([
                    'product_id' => $stock->product_id,
                    'type' => 'in',
                    'quantity' => abs($difference),
                    'remarks' => $reason,
                    'user_id' => auth()->id()
                ]);
            } else {
                try {
                    $reasonDec = $request->input('adjustment_reason', 'Stock adjustment (decrease)');
                    $this->fifoService->deductStock(
                        $stock->product_id,
                        abs($difference),
                        auth()->id(),
                        $reasonDec
                    );
                } catch (\Exception $e) {
                    throw new \Exception('Cannot decrease stock: ' . $e->getMessage());
                }
            }
        });

        return redirect()->route('inventory.stock')
            ->with('success', 'Stock adjusted successfully.');
    }

 
    public function batches($productId)
    {
        $product = Products::findOrFail($productId);
        
        $batches = StockBatch::where('product_id', $productId)
            ->orderBy('received_date', 'asc')
            ->paginate(15);

        $totalValue = $batches->sum(function($batch) {
            return $batch->quantity_remaining * $batch->purchase_price;
        });

        return view('Inventory.Stock.batches', compact('product', 'batches', 'totalValue'));
    }

  
    public function valuation()
    {
        $stocksWithBatches = Products::with(['stockBatches' => function($q) {
            $q->where('quantity_remaining', '>', 0)
              ->orderBy('received_date', 'asc');
        }, 'category'])
        ->whereHas('stockBatches', function($q) {
            $q->where('quantity_remaining', '>', 0);
        })
        ->get()
        ->map(function($product) {
            $totalQty = $product->stockBatches->sum('quantity_remaining');
            $totalValue = $product->stockBatches->sum(function($batch) {
                return $batch->quantity_remaining * $batch->purchase_price;
            });
            $avgCost = $totalQty > 0 ? $totalValue / $totalQty : 0;

            return [
                'product' => $product,
                'total_quantity' => $totalQty,
                'total_value' => $totalValue,
                'average_cost' => $avgCost,
                'selling_price' => $product->product_price,
                'potential_profit' => ($product->product_price - $avgCost) * $totalQty
            ];
        });

        $totalInventoryValue = $stocksWithBatches->sum('total_value');
        $totalPotentialProfit = $stocksWithBatches->sum('potential_profit');

        return view('Inventory.Stock.valuation', compact(
            'stocksWithBatches',
            'totalInventoryValue',
            'totalPotentialProfit'
        ));
    }

  
    public function lowStock(Request $request)
    {
        $threshold = $request->get('threshold', 10);

        $lowStockProducts = Stocks::with(['product', 'product.stockBatches' => function($q) {
            $q->where('quantity_remaining', '>', 0);
        }])
        ->where('quantity', '<=', $threshold)
        ->get();

        return view('Inventory.Stock.low_stock', compact('lowStockProducts', 'threshold'));
    }

  
    public function history($productId)
    {
        $product = Products::findOrFail($productId);
        
        $logs = StockLog::where('product_id', $productId)
            ->with('batch', 'user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('Inventory.Stock.history', compact('product', 'logs'));
    }

  
    public function getFifoCost(Request $request)
    {
        $productId = $request->product_id;
        $quantity = $request->quantity ?? 1;

        try {
            $cost = $this->fifoService->getCurrentFifoCost($productId, $quantity);
            
            return response()->json([
                'success' => true,
                'cost_per_unit' => $cost,
                'total_cost' => $cost * $quantity,
                'quantity' => $quantity
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

  
    public function allBatches(Request $request)
    {
        $search = $request->input('search');

        $query = StockBatch::with('product');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('batch_number', 'like', '%' . $search . '%')
                  ->orWhereHas('product', function ($q2) use ($search) {
                      $q2->where('product_name', 'like', '%' . $search . '%');
                  });
            });
        }

        $batches = $query->orderBy('received_date', 'asc')->paginate(25);

        $totalValue = $batches->getCollection()->sum(function ($batch) {
            return $batch->quantity_remaining * $batch->purchase_price;
        });

        return view('Inventory.Stock.all_batches', compact('batches', 'totalValue'));
    }
}