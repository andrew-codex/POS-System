<?php

namespace App\Models;
use App\Models\Category;
use App\Models\Stocks;
use App\Models\RefundItem;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'product_name',
        'product_description',
        'product_price',
        'product_barcode',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function stock()
    { 
        return $this->hasOne(Stocks::class, 'product_id');

    }

    public function refunds()
    {
        return $this->hasMany(RefundItem::class, 'product_id');
    }

}
