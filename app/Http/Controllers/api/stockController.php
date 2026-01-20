<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stocks;
use App\Models\Stock_logs as StockLog;
use App\Models\Products;

class stockController extends Controller
{

public function update(Request $request, $productId)
{
    $validated = $request->validate([
        'quantity' => 'required|integer|min:0',
    ]);

 
    $stock = \App\Models\Stocks::firstOrCreate(
        ['product_id' => $productId],
        ['quantity' => 0]
    );

    $oldQuantity = (int) $stock->quantity;
    $newQuantity = (int) $validated['quantity'];

    
    $stock->quantity = $newQuantity;
    $stock->save();

    
    $difference = $newQuantity - $oldQuantity;
    if ($difference !== 0) {
            StockLog::create([
            'product_id' => $stock->product_id,
            'type' => $difference > 0 ? 'in' : 'out',
            'quantity' => abs($difference),
            'remarks' => 'Stock updated via mobile app',
            'user_id' => auth()->id(),
        ]);
    }

    return response()->json([
        'message' => 'Stock updated successfully',
        'stock' => [
            'id' => $stock->id,
            'product_id' => $stock->product_id,
            'quantity' => $stock->quantity,
            'old_quantity' => $oldQuantity,
        ],
    ], 200);
}


public function updateByProduct(Request $request, Products $product) {
    $request->merge(['quantity' => (int) $request->input('quantity')]);
    return $this->update($request, $product->id);
}

}