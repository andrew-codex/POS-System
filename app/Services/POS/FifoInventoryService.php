<?php 
namespace App\Services\POS;

use App\Models\StockBatch;
use App\Models\Stocks;
use App\Models\Stock_logs as StockLog;
use App\Models\Products;
use Illuminate\Support\Facades\DB;

class FifoInventoryService
{

    public function addStock($productId, $quantity, $purchasePrice, $userId = null)
    {
        return DB::transaction(function () use ($productId, $quantity, $purchasePrice, $userId) {
          
            $batch = StockBatch::create([
                'product_id' => $productId,
                'quantity_remaining' => $quantity,
                'quantity_initial' => $quantity,
                'purchase_price' => $purchasePrice,
                'batch_number' => $this->generateBatchNumber($productId),
                'received_date' => now()
            ]);

          
            $stock = Stocks::firstOrCreate(
                ['product_id' => $productId],
                ['quantity' => 0]
            );
            $stock->increment('quantity', $quantity);

            StockLog::create([
                'product_id' => $productId,
                'batch_id' => $batch->id,
                'type' => 'in',
                'quantity' => $quantity,
                'purchase_price' => $purchasePrice,
                'remarks' => "Restock - Batch #{$batch->batch_number}",
                'user_id' => $userId
            ]);

            return $batch;
        });
    }


    public function deductStock($productId, $quantity, $userId = null, $remarks = 'Sale')
    {
        return DB::transaction(function () use ($productId, $quantity, $userId, $remarks) {
            $remainingQty = $quantity;
            $batchesUsed = [];
            $totalCost = 0;

           
            $batches = StockBatch::where('product_id', $productId)
                ->where('quantity_remaining', '>', 0)
                ->orderBy('received_date', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            if ($batches->sum('quantity_remaining') < $quantity) {
                throw new \Exception("Insufficient stock. Available: {$batches->sum('quantity_remaining')}, Requested: {$quantity}");
            }

            foreach ($batches as $batch) {
                if ($remainingQty <= 0) break;

                $qtyFromBatch = min($remainingQty, $batch->quantity_remaining);
                
            
                $batch->deduct($qtyFromBatch);

                
                $batchesUsed[] = [
                    'batch_id' => $batch->id,
                    'quantity' => $qtyFromBatch,
                    'purchase_price' => $batch->purchase_price,
                    'cost' => $qtyFromBatch * $batch->purchase_price
                ];

                $totalCost += $qtyFromBatch * $batch->purchase_price;

                StockLog::create([
                    'product_id' => $productId,
                    'batch_id' => $batch->id,
                    'type' => 'out',
                    'quantity' => $qtyFromBatch,
                    'purchase_price' => $batch->purchase_price,
                    'remarks' => $remarks,
                    'user_id' => $userId
                ]);

                $remainingQty -= $qtyFromBatch;
            }

            
            $stock = Stocks::where('product_id', $productId)->first();
            if ($stock) {
                $stock->decrement('quantity', $quantity);
            }

            return [
                'batches_used' => $batchesUsed,
                'total_cost' => $totalCost,
                'average_cost' => $quantity > 0 ? $totalCost / $quantity : 0
            ];
        });
    }


    public function getCurrentFifoCost($productId, $quantity = 1)
    {
        $remainingQty = $quantity;
        $totalCost = 0;

        $batches = StockBatch::where('product_id', $productId)
            ->where('quantity_remaining', '>', 0)
            ->orderBy('received_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($batches as $batch) {
            if ($remainingQty <= 0) break;

            $qtyFromBatch = min($remainingQty, $batch->quantity_remaining);
            $totalCost += $qtyFromBatch * $batch->purchase_price;
            $remainingQty -= $qtyFromBatch;
        }

        if ($remainingQty > 0) {
            return null; 
        }

        return $quantity > 0 ? $totalCost / $quantity : 0;
    }


    public function getStockValuation($productId = null)
    {
        $query = StockBatch::where('quantity_remaining', '>', 0);
        
        if ($productId) {
            $query->where('product_id', $productId);
        }

        return $query->get()->sum(function ($batch) {
            return $batch->quantity_remaining * $batch->purchase_price;
        });
    }

 
    private function generateBatchNumber($productId)
    {
        $product = Products::find($productId);
        $date = now()->format('Ymd');
        $count = StockBatch::where('product_id', $productId)
            ->whereDate('created_at', today())
            ->count() + 1;

        return "BTH-{$productId}-{$date}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

 
    public function returnStock($productId, $quantity, $batchId = null, $userId = null)
    {
        return DB::transaction(function () use ($productId, $quantity, $batchId, $userId) {
            if ($batchId) {
                $batch = StockBatch::findOrFail($batchId);
                $batch->addBack($quantity);
            } else {
                $batch = StockBatch::where('product_id', $productId)
                    ->orderBy('received_date', 'desc')
                    ->first();

                if (!$batch) {
                    throw new \Exception("No batch found for this product");
                }

                $batch->addBack($quantity);
            }

            $stock = Stocks::where('product_id', $productId)->first();
            if ($stock) {
                $stock->increment('quantity', $quantity);
            }

            StockLog::create([
                'product_id' => $productId,
                'batch_id' => $batch->id,
                'type' => 'in',
                'quantity' => $quantity,
                'purchase_price' => $batch->purchase_price,
                'remarks' => 'Stock return/refund',
                'user_id' => $userId
            ]);

            return $batch;
        });
    }
}