<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;


//products
Route::get('/products', [ProductController::class, 'getProducts']);
Route::get('/products/{id}', [ProductController::class, 'getProduct']);
Route::delete('/products/{id}', [ProductController::class, 'deleteProduct']);
Route::put('/products/{id}', [ProductController::class, 'updateProduct']);
Route::post('/products/create', [ProductController::class, 'createProduct']);
Route::post('/products/import', [ProductController::class, 'importProducts']);
//inventories
Route::get('/inventories', [InventoryController::class, 'getInventories']);
Route::get('/inventories/{id}', [InventoryController::class, 'getInventory']);
Route::delete('/inventories/{id}', [InventoryController::class, 'deleteInventory']);
Route::put('/inventories/{id}', [InventoryController::class, 'updateInventory']);
Route::post('/inventories/create', [InventoryController::class, 'createInventory']);
Route::post('/inventories/import', [InventoryController::class, 'importInventories']);