<?php

namespace App\Http\Controllers\POS;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sales;
use App\Models\User;
use App\Models\Refund;
use App\Models\RefundItem;
use App\Models\Products;
use App\Models\Stocks;
use Illuminate\Support\Facades\DB;
class RefundController extends Controller
{

    public function index(Sales $sale)
    {
        $products = Products::paginate(50);
        $refunds = $sale->refunds()
            ->with(['user'])
            ->latest()
            ->paginate(10);

        $refundItems = RefundItem::whereHas('refund', fn($q) => $q->where('sale_id', $sale->id))
            ->with(['product', 'newProduct'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('POS.Sales.refund', compact('sale', 'refunds', 'refundItems', 'products'));
    }




    public function store(Request $request, Sales $sale)
    {
        $request->validate([
            'refund_amount' => 'required|numeric|min:0.01|max:' . max(0, $sale->total_amount - $sale->totalRefunded()),
            'refund_type' => 'required|string',
            'refund_reason' => 'nullable|string|max:255',
        ]);

        $existingRefundedItems = $sale->refunds()
            ->with('items')
            ->get()
            ->pluck('items.*.product_id')
            ->flatten()
            ->toArray();

        
        $itemsToProcess = collect($request->items)
            ->filter(fn($item) => (($item['quantity'] ?? 0) > 0 || !empty($item['is_changed'])))
            ->values();

        if ($itemsToProcess->isEmpty()) {
            return redirect()->back()->with('error', 'No valid items selected for refund or exchange.');
        }

    
        foreach ($itemsToProcess as $item) {
            if (in_array($item['product_id'], $existingRefundedItems)) {
                return redirect()->back()->with('error', "Product ID {$item['product_id']} has already been refunded or exchanged.");
            }
        }

        $productIds = $itemsToProcess->pluck('product_id')->filter()->unique()->values()->all();
        $stocksMap = Stocks::whereIn('product_id', $productIds)->get()->keyBy('product_id');

        try {
            DB::transaction(function () use ($sale, $request, $itemsToProcess, $stocksMap) {
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
            });
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed processing refund.');
        }

        return redirect()->route('sales.refunds.index', ['sale' => $sale->id])
                        ->with('success', 'Transaction processed successfully.');
        }

 
}