<?php
namespace App\Services\POS;

use App\Models\Sales;
use App\Models\SaleItem;
use App\Models\Stock;
use App\Services\POS\FifoInventoryService;
use Illuminate\Support\Facades\DB;

class SaleService
{
    protected $fifoService;

    public function __construct(FifoInventoryService $fifoService)
    {
        $this->fifoService = $fifoService;
    }

    /**
     * Create a new sale with FIFO inventory tracking
     */
    public function createSale($cart, $total, $amountReceived, $change, $userId)
    {
        return DB::transaction(function () use ($cart, $total, $amountReceived, $change, $userId) {
            // Generate invoice number
            $invoiceNo = $this->generateInvoiceNumber();

            // Create the sale record
            $sale = Sales::create([
                'invoice_no' => $invoiceNo,
                'status' => 'completed',
                'total_amount' => $total,
                'amount_received' => $amountReceived,
                'change_amount' => $change,
                'created_by' => $userId
            ]);

            $totalCost = 0;
            $totalProfit = 0;

            // Process each cart item
            foreach ($cart as $item) {
                // Deduct stock using FIFO
                try {
                    $fifoResult = $this->fifoService->deductStock(
                        $item['id'],
                        $item['qty'],
                        $userId,
                        "Sale #{$invoiceNo}"
                    );

                    $costPrice = $fifoResult['average_cost'];
                    $itemCost = $costPrice * $item['qty'];
                    $itemProfit = ($item['price'] - $costPrice) * $item['qty'];

                    $totalCost += $itemCost;
                    $totalProfit += $itemProfit;

                } catch (\Exception $e) {
                    // If stock deduction fails, rollback and throw error
                    throw new \Exception("Error processing product ID {$item['id']}: " . $e->getMessage());
                }

                // Create sale item with cost tracking
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['qty'],
                    'cost_price' => $costPrice,
                    'profit' => $itemProfit
                ]);
            }

        

            return $sale;
        });
    }

    /**
     * Generate unique invoice number
     */
    private function generateInvoiceNumber()
    {
        $date = now()->format('Ymd');
        $count = Sales::whereDate('created_at', today())->count() + 1;
        
        return 'INV-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get sale details with profit information
     */
    public function getSaleWithProfitDetails($saleId)
    {
        return Sales::with(['items' => function($query) {
            $query->select(
                'id',
                'sale_id',
                'product_id',
                'quantity',
                'price',
                'subtotal',
                'cost_price',
                'profit'
            )->with('product:id,product_name');
        }])
        ->withSum('items', 'profit')
        ->findOrFail($saleId);
    }

    /**
     * Get sales profit report for a date range
     */
    public function getProfitReport($startDate, $endDate)
    {
        return SaleItem::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                COUNT(DISTINCT sale_id) as total_sales,
                SUM(subtotal) as total_revenue,
                SUM(cost_price * quantity) as total_cost,
                SUM(profit) as total_profit,
                AVG(profit) as average_profit
            ')
            ->first();
    }

    /**
     * Validate stock availability before sale
     */
    public function validateStockAvailability($cart)
    {
        $errors = [];

        foreach ($cart as $item) {
            $stock = Stock::where('product_id', $item['id'])->first();
            
            if (!$stock || $stock->quantity < $item['qty']) {
                $available = $stock ? $stock->quantity : 0;
                $errors[] = [
                    'product_id' => $item['id'],
                    'requested' => $item['qty'],
                    'available' => $available,
                    'message' => "Insufficient stock. Available: {$available}, Requested: {$item['qty']}"
                ];
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Get estimated profit for cart before completing sale
     */
    public function estimateCartProfit($cart)
    {
        $totalProfit = 0;
        $items = [];

        foreach ($cart as $item) {
            try {
                $fifoCost = $this->fifoService->getCurrentFifoCost($item['id'], $item['qty']);
                
                if ($fifoCost !== null) {
                    $itemProfit = ($item['price'] - $fifoCost) * $item['qty'];
                    $totalProfit += $itemProfit;
                    
                    $items[] = [
                        'product_id' => $item['id'],
                        'quantity' => $item['qty'],
                        'selling_price' => $item['price'],
                        'cost_price' => $fifoCost,
                        'profit' => $itemProfit,
                        'margin_percent' => $item['price'] > 0 ? (($item['price'] - $fifoCost) / $item['price']) * 100 : 0
                    ];
                }
            } catch (\Exception $e) {
                // Skip items with errors
                continue;
            }
        }

        return [
            'items' => $items,
            'total_profit' => $totalProfit
        ];
    }
}
