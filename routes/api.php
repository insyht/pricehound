<?php

use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\PackController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::patch('/user', [UserController::class, 'save'])->middleware('auth:sanctum');
Route::post('login', [AuthenticationController::class, 'login']);

Route::post('devices', [DeviceController::class, 'store'])
    ->middleware('auth:sanctum')
    ->name('api.devices.store');

Route::prefix('products')->middleware(['auth:sanctum'])->name('api.products.')->group(function () {
    Route::post('/add/{identifier}', [ProductController::class, 'add'])->name('add');
    Route::get('/{product}', [ProductController::class, 'show'])->name('show');
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/search/{identifier}', [ProductController::class, 'search'])->name('search');
});

Route::prefix('pack')->middleware([/* Check if the request is really from a known Hound */])->name('api.pack.')->group(function () {
    Route::post('/get-source', [PackController::class, 'getSource'])->name('get-source');
    Route::post('/retrieve-source', [PackController::class, 'retrieveSource'])->middleware('auth:sanctum');
});

