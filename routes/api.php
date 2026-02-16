<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CorsMiddleware;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InventoryController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::middleware([CorsMiddleware::class])->group(function () {
Route::post('/login', [AuthController::class, 'login']);


//users
Route::get('/users', [UserController::class, 'getUsers']);
Route::get('/users/{id}', [UserController::class, 'getUser']);
Route::delete('/users/{id}', [UserController::class, 'deleteUser']);
Route::put('/users/{id}', [UserController::class, 'updateUser']);
Route::post('/users/create', [AuthController::class, 'register']);

//products
Route::get('/products', [ProductController::class, 'getProducts']);
Route::get('/products/{id}', [ProductController::class, 'getProduct']);
Route::delete('/products/{id}', [ProductController::class, 'deleteProduct']);
Route::put('/products/{id}', [ProductController::class, 'updateProduct']);
Route::post('/products/create', [ProductController::class, 'createProduct']);
//inventories
Route::get('/inventories', [InventoryController::class, 'getInventories']);
Route::get('/inventories/{id}', [InventoryController::class, 'getInventory']);
Route::delete('/inventories/{id}', [InventoryController::class, 'deleteInventory']);
Route::put('/inventories/{id}', [InventoryController::class, 'updateInventory']);
Route::post('/inventories/create', [InventoryController::class, 'createInventory']);
//customers
Route::get('/customers', [CustomerController::class, 'getCustomers']);
Route::get('/customers/{id}', [CustomerController::class, 'getCustomer']);
Route::delete('/customers/{id}', [CustomerController::class, 'deleteCustomer']);
Route::put('/customers/{id}', [CustomerController::class, 'updateCustomer']);
Route::post('/customers/create', [CustomerController::class, 'createCustomer']);

//orders
Route::post('/orders/create', [OrderController::class, 'createOrder']);
Route::get('/orders/{id}', [OrderController::class, 'getOrder']);
Route::get('/orders', [OrderController::class, 'getOrders']);
Route::get('/orders/today', [OrderController::class, 'todayOrders']);



Route::middleware('auth:sanctum')->group(function () {
    //cashier shifts
Route::post('/shifts/start', [CashierController::class, 'startShift']);
Route::post('/shifts/end', [CashierController::class, 'endShift']);
Route::get('/shifts/current', [CashierController::class, 'getShift']);
Route::get('/sales', [CashierController::class, 'getSales']);
Route::post('/cashier/order-payments/{order_id}', [CashierController::class, 'orderPayment']);
Route::post('/cashier/sales-payments/{sales_id}', [CashierController::class, 'salesPayment']);
//Sales
Route::get('/sales/products', [SalesController::class, 'getSalesProducts']);
Route::post('/sales/create', [SalesController::class, 'saleCreate']);
Route::get('/sales/report', [SalesController::class, 'getSalesReport']);
Route::get('/sales/daily', [SalesController::class, 'getDailySales']);
Route::get('/sales/monthly', [SalesController::class, 'getMonthlySales']);
});



});