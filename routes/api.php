<?php

use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('login', [AuthenticationController::class, 'login']);

Route::post('/products/add/{identifier}', [ProductController::class, 'add'])->name('products.add')
     ->middleware(['auth:sanctum']);
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show')
     ->middleware(['auth:sanctum']);
Route::get('/products', [ProductController::class, 'index'])->name('products.index')
     ->middleware(['auth:sanctum']);
