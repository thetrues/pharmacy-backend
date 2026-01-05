<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CorsMiddleware;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::middleware([CorsMiddleware::class])->group(function () {
Route::post('/login', [AuthController::class, 'login']);


//users
Route::get('/users', [App\Http\Controllers\UserController::class, 'getUsers']);
Route::get('/users/{id}', [App\Http\Controllers\UserController::class, 'getUser']);
Route::delete('/users/{id}', [App\Http\Controllers\UserController::class, 'deleteUser']);
Route::put('/users/{id}', [App\Http\Controllers\UserController::class, 'updateUser']);
Route::post('/users/create', [AuthController::class, 'register']);

//products
Route::get('/products', [App\Http\Controllers\ProductController::class, 'getProducts']);
Route::get('/products/{id}', [App\Http\Controllers\ProductController::class, 'getProduct']);
Route::delete('/products/{id}', [App\Http\Controllers\ProductController::class, 'deleteProduct']);
Route::put('/products/{id}', [App\Http\Controllers\ProductController::class, 'updateProduct']);
Route::post('/products/create', [App\Http\Controllers\ProductController::class, 'createProduct']);

//inventories
Route::get('/inventories', [App\Http\Controllers\InventoryController::class, 'getInventories']);
Route::get('/inventories/{id}', [App\Http\Controllers\InventoryController::class, 'getInventory']);
Route::delete('/inventories/{id}', [App\Http\Controllers\InventoryController::class, 'deleteInventory']);
Route::put('/inventories/{id}', [App\Http\Controllers\InventoryController::class, 'updateInventory']);
Route::post('/inventories/create', [App\Http\Controllers\InventoryController::class, 'createInventory']);
});