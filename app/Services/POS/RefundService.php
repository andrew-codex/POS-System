<?php

namespace App\Services\POS;

use App\Models\Refund;
use App\Models\RefundItem;
use App\Models\Stocks;
use Illuminate\Support\Facades\DB;

class RefundService
{
    /**
     * Process a refund transactionally.
     *
     * @param  \App\Models\Sales  $sale
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Support\Collection  $itemsToProcess
     * @return \App\Models\Refund
     */
    public function processRefund($sale, $request, $itemsToProcess)
    {
        $productIds = $itemsToProcess->pluck('product_id')->filter()->unique()->values()->all();
        $stocksMap = Stocks::whereIn('product_id', $productIds)->get()->keyBy('product_id');

        return DB::transaction(function () use ($sale, $request, $itemsToProcess, $stocksMap) {
            $refund = Refund::create([
                'sale_id' => $sale->id,
                'refund_amount' => $request->refund_amount,
                'refund_type' => $request->refund_type,
                'refund_reason' => $request->refund_reason,
                'refunded_by' => auth()->id(),
            ]);

            foreach ($itemsToProcess as $item) {
                $refund->items()->create([
                    'product_id' => $item['product_id'],
                    'new_product_id' => $item['new_product_id'] ?? null,
                    'quantity' => $item['quantity'] ?? 0,
                    'price' => $item['price'],
                    'new_price' => $item['new_price'] ?? null,
                    'is_expired' => $item['is_expired'] ?? 0,
                    'is_damaged' => $item['is_damaged'] ?? 0,
                    'is_changed' => $item['is_changed'] ?? 0,
                ]);

                if (empty($item['is_changed']) && empty($item['is_expired']) && empty($item['is_damaged'])) {
                    if (isset($stocksMap[$item['product_id']])) {
                        Stocks::where('product_id', $item['product_id'])
                            ->increment('quantity', $item['quantity']);
                    }
                }
            }

            $totalRefunded = $sale->totalRefunded();
            $hasExchange = $sale->refunds()->whereHas('items', fn($q) => $q->where('is_changed', 1))->exists();

            if ($hasExchange) {
                $sale->update(['status' => 'exchanged']);
            } elseif ($totalRefunded >= $sale->total_amount) {
                $sale->update(['status' => 'refunded']);
            } elseif ($totalRefunded > 0) {
                $sale->update(['status' => 'partially_refunded']);
            } else {
                $sale->update(['status' => 'completed']);
            }

            return $refund;
        });
    }
}
