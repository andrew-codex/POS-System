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
use App\Services\POS\RefundService;
use Illuminate\Support\Facades\DB;
class RefundController extends Controller
{
    protected RefundService $refundService;

    public function __construct(RefundService $refundService)
    {
        $this->refundService = $refundService;
    }

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

        try {
            $this->refundService->processRefund($sale, $request, $itemsToProcess);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed processing refund.');
        }

        return redirect()->route('sales.refunds.index', ['sale' => $sale->id])
                        ->with('success', 'Transaction processed successfully.');
        }

 
}