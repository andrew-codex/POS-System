<?php

namespace App\Models;
use App\Models\Products;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'category_name',
        'category_description',
    ];

    public function products()
    {
        return $this->hasMany(Products::class, 'category_id');
    }
}
