<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sales;
use App\Services\POS\SaleService;
use App\Traits\LogsActivity;

class SalesController extends Controller
{
    use LogsActivity;

    protected $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Sales::with('cashier');

        if ($status) {
            $query->where('status', $status);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        if (!empty($search)) {
            $escapedSearch = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search);
            $likePattern = '%' . $escapedSearch . '%';

            $query->where(function ($q) use ($likePattern) {
                $q->where('id', 'like', $likePattern)
                  ->orWhere('total_amount', 'like', $likePattern)
                  ->orWhere('amount_received', 'like', $likePattern)
                  ->orWhere('change_amount', 'like', $likePattern)
                  ->orWhereHas('cashier', fn($q2) => $q2->where('name', 'like', $likePattern));
            });
        }

        $sales = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('POS.Sales.sales', compact('sales'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|exists:products,id',
            'cart.*.qty' => 'required|integer|min:1',
            'cart.*.price' => 'required|numeric|min:0',
            'total' => 'required|numeric',
            'amount_received' => 'required|numeric',
            'change' => 'required|numeric',
        ]);

        try {
            $sale = $this->saleService->createSale(
                $request->cart,
                $request->total,
                $request->amount_received,
                $request->change,
                auth()->id()
            );
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        $this->logActivity("Completed Sale", [
            "sale_id" => $sale->id,
            "total_amount" => $sale->total_amount
        ]);

        return redirect()->route('pos.cart')
                         ->with('success', 'Sale completed successfully!');
    }

    public function info($id)
    {
        $sale = Sales::with('cashier')->findOrFail($id);

        return response()->json([
            'id' => $sale->id,
            'cashier' => $sale->cashier->name,
            'date' => $sale->created_at->format('Y-m-d h:i A'),
        ]);
    }
}
