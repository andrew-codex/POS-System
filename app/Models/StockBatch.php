<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Products;
use App\Models\Stock_logs as StockLog;

class StockBatch extends Model
{
    protected $fillable = [
        'product_id',
        'quantity_remaining',
        'quantity_initial',
        'purchase_price',
        'batch_number',
        'received_date'
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'received_date' => 'datetime'
    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function stockLogs()
    {
        return $this->hasMany(StockLog::class, 'batch_id');
    }

    
    public function isDepleted()
    {
        return $this->quantity_remaining <= 0;
    }

    public function deduct($quantity)
    {
        if ($quantity > $this->quantity_remaining) {
            throw new \Exception("Cannot deduct {$quantity} from batch. Only {$this->quantity_remaining} remaining.");
        }

        $this->quantity_remaining -= $quantity;
        $this->save();

        return $this;
    }

    public function addBack($quantity)
    {
        $this->quantity_remaining += $quantity;
        $this->save();

        return $this;
    }
}

