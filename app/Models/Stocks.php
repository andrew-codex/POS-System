<?php

namespace App\Models;
use App\Models\Products;
use Illuminate\Database\Eloquent\Model;

class Stocks extends Model
{
    protected $table = 'stocks';

    protected $fillable = [
        'product_id',
        'quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}
