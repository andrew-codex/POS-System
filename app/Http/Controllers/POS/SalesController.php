<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sales;
use App\Models\SaleItem;
use App\Models\Stocks;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Traits\LogsActivity;
class SalesController extends Controller
{ use LogsActivity;
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
        

     
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('total_amount', 'like', "%{$search}%")
                  ->orWhere('amount_received', 'like', "%{$search}%")
                  ->orWhere('change_amount', 'like', "%{$search}%")
                  ->orWhereHas('cashier', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $sales = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('POS.Sales.sales', compact('sales'));
    }



   public function store(Request $request)
{


    $request->validate([
        'cart' => 'required|array',
        'total' => 'required|numeric',
        'amount_received' => 'required|numeric',
        'change' => 'required|numeric',
       
    ]);

    DB::transaction(function() use ($request, &$sale) {

            $sale = Sales::create([
                'total_amount' => $request->total,
                'amount_received' => $request->amount_received,
                'change_amount' => $request->change,
                'status' => 'completed',
                'created_by' => auth()->id(),
            ]);

        foreach ($request->cart as $item) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item['id'],
                'quantity' => $item['qty'],
                'price' => $item['price'],
                'subtotal' => $item['price'] * $item['qty'],
            ]);

    $stock = Stocks::where('product_id', $item['id'])->first();
    if ($stock) {
        $stock->quantity = max(0, $stock->quantity - $item['qty']);
        $stock->save();
    }
        }
    });

    $this->logActivity("Completed Sale", [
        "sale_id" => $sale->id,
        "total_amount" => $sale->total_amount
    ]);

    
    return redirect()->route('pos.cart')
                     ->with('success', 'Sale completed successfully!');
}


    public function info($id)
    {
        $sale = Sale::with('cashier')->findOrFail($id);

        return response()->json([
            'id' => $sale->id,
            'cashier' => $sale->cashier->name,
            'date' => $sale->created_at->format('Y-m-d h:i A'),
        ]);
    }


}