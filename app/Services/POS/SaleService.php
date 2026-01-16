<?php

namespace App\Services\POS;

use App\Models\Sales;
use App\Models\SaleItem;
use App\Models\Stocks;
use Illuminate\Support\Facades\DB;

class SaleService
{
    /**
     * Create a new sale with sale items and update stock.
     *
     * @param array $cart
     * @param float $total
     * @param float $amountReceived
     * @param float $change
     * @param int $userId
     * @return Sales
     * @throws \Exception
     */
    public function createSale(array $cart, float $total, float $amountReceived, float $change, int $userId): Sales
    {
        return DB::transaction(function () use ($cart, $total, $amountReceived, $change, $userId) {
            $sale = Sales::create([
                'total_amount' => $total,
                'amount_received' => $amountReceived,
                'change_amount' => $change,
                'status' => 'completed',
                'created_by' => $userId,
            ]);

            foreach ($cart as $item) {
                $stock = Stocks::where('product_id', $item['id'])->lockForUpdate()->first();

                if (!$stock || $stock->quantity < $item['qty']) {
                    throw new \Exception("Insufficient stock for product ID {$item['id']}");
                }

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['qty'],
                ]);

                $stock->quantity -= $item['qty'];
                $stock->save();
            }

            return $sale;
        });
    }
}
