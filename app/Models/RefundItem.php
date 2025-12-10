<?php

namespace App\Models;
use App\Models\Refund;
use App\Models\Products;
use Illuminate\Database\Eloquent\Model;

class RefundItem extends Model
{
     protected $fillable = [
        'refund_id',
        'product_id',
        'quantity',
        'price',
        'new_product_id',
        'new_price',
        'is_expired',
        'is_damaged',
        'is_changed'
    ];

    public function refund()
    {
        return $this->belongsTo(Refund::class);
    }

    public function product()
    {
        return $this->belongsTo(Products::class , 'product_id');
    }

    public function newProduct()
    {
        return $this->belongsTo(Products::class, 'new_product_id');
    }

}
