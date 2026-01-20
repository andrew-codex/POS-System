<?php

namespace App\Models;
use App\Models\Sales;
use App\Models\Products;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
class SaleItem extends Model
{
     protected $table = 'sale_items';
    protected $fillable = [
        'sale_id', 'product_id', 'quantity', 'price', 'subtotal', 'cost_price', 'profit'
    ];

    public function sale()
    {
        return $this->belongsTo(Sales::class, 'sale_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
    
        protected static function booted()
    {
        static::saved(function () {
            Cache::flush(); 
        });

        static::deleted(function () {
            Cache::flush();
        });
    }

     public static function getSalesByProductQuery()
    {
        return self::join('products as p', 'sale_items.product_id', '=', 'p.id')
            ->select(
                'sale_items.product_id',
                'p.product_name as product_name',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.subtotal) as total_sales')
            )
            ->groupBy('sale_items.product_id', 'p.product_name')
            ->orderBy('total_sales', 'desc');
    }

    public function getPriceAttribute($value)
    {
        return number_format($value, 2);
    }

    public function getSubtotalAttribute($value)
    {
        return number_format($value, 2);
    }


    
  
}