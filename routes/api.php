<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\productsController;
use App\Http\Controllers\api\categoryController;
use App\Http\Controllers\api\loginController;
use App\Http\Controllers\api\stockController;


Route::post('/login', [loginController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {

    Route::post('/auth/logout', [loginController::class, 'logout']);
    Route::get('/products', [productsController::class, 'index']);
    Route::post('/products', [productsController::class, 'store']);
    Route::get('/categories', [categoryController::class, 'getCategories']);
    Route::put('/products/{productId}/stock', [stockController::class, 'update']);
    Route::put('products/{product}/stock', [stockController::class, 'updateByProduct']);
    

   
});