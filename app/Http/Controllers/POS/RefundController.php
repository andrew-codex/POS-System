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

    protected function getAlreadyProcessed(Sales $sale): array
    {
        return $sale->refunds()
            ->with('items')
            ->get()
            ->flatMap(fn($r) => $r->items)
            ->pluck('product_id')
            ->unique()
            ->toArray();
    }

    protected function sanitizeNumber(mixed $value): float
    {
        if (is_numeric($value)) return (float) $value;
        $clean = preg_replace('/[^0-9.\-]/', '', (string) $value);
        return $clean === '' ? 0.0 : (float) $clean;
    }

    public function index(Sales $sale)
    {
        $products = Products::paginate(50);
        $refunds = $sale->refunds()
            ->with(['user', 'items'])
            ->latest()
            ->paginate(10);

        $alreadyProcessed = $this->getAlreadyProcessed($sale);

        $refunds->getCollection()->transform(function ($refund) {
            $refund->has_exchange = $refund->items->contains(fn($i) => (int)$i->is_changed === 1);
            return $refund;
        });

        $refundItems = RefundItem::whereHas('refund', fn($q) => $q->where('sale_id', $sale->id))
            ->with(['product', 'newProduct'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('POS.Sales.refund', compact('sale', 'refunds', 'refundItems', 'products', 'alreadyProcessed'));
    }




    public function store(Request $request, Sales $sale)
    {
        $rawTotal = $sale->getOriginal('total_amount') ?? $sale->total_amount;
        $totalAmount = $this->sanitizeNumber($rawTotal);
        $totalRefunded = is_numeric($sale->totalRefunded()) ? (float) $sale->totalRefunded() : floatval($sale->totalRefunded());
        $maxAllowed = max(0, $totalAmount - $totalRefunded);

        if ($maxAllowed < 0.01) {
            return redirect()->back()->with('error', 'No refundable amount available for this sale.');
        }

     
        $itemsToProcess = collect($request->items ?? [])
            ->filter(fn($item) => (($item['quantity'] ?? 0) > 0 || !empty($item['is_changed'])))
            ->values();

        if ($itemsToProcess->isEmpty()) {
            return redirect()->back()->with('error', 'No valid items selected for refund or exchange.');
        }

       
        $saleQuantities = $sale->items->groupBy('product_id')->map(fn($g) => $g->sum('quantity'))->toArray();

        $previousRefundQtyByProduct = \App\Models\RefundItem::whereHas('refund', fn($q) => $q->where('sale_id', $sale->id))
            ->get()
            ->groupBy('product_id')
            ->map(fn($g) => $g->sum('quantity'))
            ->toArray();

      
        $computedRefundTotal = 0.0;
        foreach ($itemsToProcess as $item) {
            $productId = $item['product_id'];
            $requestedQty = (int) ($item['quantity'] ?? 0);

            $alreadyRefundedQty = isset($previousRefundQtyByProduct[$productId]) ? (int) $previousRefundQtyByProduct[$productId] : 0;
            $saleQty = isset($saleQuantities[$productId]) ? (int) $saleQuantities[$productId] : 0;
            $remainingQty = max(0, $saleQty - $alreadyRefundedQty);

            if ($requestedQty > $remainingQty) {
                return redirect()->back()->with('error', "Requested quantity ({$requestedQty}) for product ID {$productId} exceeds remaining quantity ({$remainingQty}).");
            }

            $price = (float) ($item['price'] ?? 0);
            if (!empty($item['is_changed'])) {
                $newPrice = (float) ($item['new_price'] ?? 0);
                $computedRefundTotal += ($price - $newPrice) * $requestedQty;
            } else {
                $computedRefundTotal += $price * $requestedQty;
            }
        }

        $expectedRefundAmount = round(max(0.01, $computedRefundTotal), 2);

        if (!isset($request->refund_amount) || abs((float)$request->refund_amount - $expectedRefundAmount) > 0.01) {
            return redirect()->back()->with('error', "Refund amount mismatch. Expected {$expectedRefundAmount}.");
        }

        $request->validate([
            'refund_amount' => 'required|numeric|min:0.01|max:' . $maxAllowed,
            'refund_type' => 'required|string',
            'refund_reason' => 'nullable|string|max:255',
        ]);

        try {
            $this->refundService->processRefund($sale, $request, $itemsToProcess);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed processing refund.');
        }

        return redirect()->route('sales.refunds.index', ['sale' => $sale->id])
                        ->with('success', 'Transaction processed successfully.');
        }

 
}