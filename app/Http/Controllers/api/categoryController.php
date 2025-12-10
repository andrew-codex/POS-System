<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
class categoryController extends Controller
{


    public function getCategories(Request $request)
    {

    $categories = Category::orderBy('created_at', 'desc')->get();
    return response()->json($categories);
    
    }
}