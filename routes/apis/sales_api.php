<?php
use App\Http\Controllers\CashierController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SalesController;
use Illuminate\Support\Facades\Route;

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

//orders
Route::post('/orders/create', [OrderController::class, 'createOrder']);
Route::get('/orders/{id}', [OrderController::class, 'getOrder']);
Route::get('/orders', [OrderController::class, 'getOrders']);
Route::get('/orders/today', [OrderController::class, 'todayOrders']);

//dashboard

});
Route::get('/dashboard/summary', [ProductController::class, 'getDashboardProducts']);
