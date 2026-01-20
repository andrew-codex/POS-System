<?php

namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Models\StockBatch;

class Stock_logs extends Model
{
     protected $table = 'stock_logs';
     protected $fillable = ['product_id', 'type', 'quantity', 'remarks', 'user_id', 'batch_id', 'purchase_price'];


     public function user()
     {
     return $this->belongsTo(User::class, 'user_id');
     }

     public function batch()
     {
          return $this->belongsTo(StockBatch::class, 'batch_id');
     }
}


