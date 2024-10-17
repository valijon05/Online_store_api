<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::resource('/products', \App\Http\Controllers\ProductController::class );
//    ->middleware('auth:sanctum');
Route::resource('/categories', \App\Http\Controllers\CategoryController::class);
    //->middleware('auth:sanctum');
Route::get('/category/{category}/products',function (string $id){
 return new \App\Http\Resources\ProductResource(\App\Models\Product::query()->findOrFail($id));
});

