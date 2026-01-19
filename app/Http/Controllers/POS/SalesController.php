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
        $sales = Sales::select('id', 'invoice_no', 'created_at', 'created_by', 'total_amount', 'status')
            ->with(['cashier:id,name', 'items:id,sale_id'])
            ->withCount('items')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('POS.Sales.sales', compact('sales'));
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Sales::select('id', 'invoice_no', 'created_at', 'created_by', 'total_amount', 'status')
            ->with(['cashier:id,name', 'items:id,sale_id'])
            ->withCount('items');

        if (!empty($status)) {
            $query->where('status', $status);
        }
      
        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween('created_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ]);
        } elseif (!empty($startDate)) {
            $query->whereDate('created_at', '>=', $startDate);
        } elseif (!empty($endDate)) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        if (!empty($search)) {
            $escaped = addcslashes($search, '%_');
            $query->where(function ($q) use ($escaped) {
                $q->where('invoice_no', 'like', '%' . $escaped . '%')
                  ->orWhere('total_amount', 'like', '%' . $escaped . '%')
                  ->orWhereHas('cashier', function($q2) use ($escaped) {
                      $q2->where('name', 'like', '%' . $escaped . '%');
                  });
            });
        }

        $sales = $query->orderBy('created_at', 'desc')->limit(100)->get();

        return response()->json([
            'sales' => $sales,
            'count' => $sales->count()
        ]);
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
