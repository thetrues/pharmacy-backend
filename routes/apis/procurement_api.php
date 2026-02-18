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
use App\Http\Controllers\PurchaseOrderController;

Route::get('/getPurchaseOrders', [PurchaseOrderController::class, 'getPurchaseOrders']);
Route::get('/getPurchaseOrder/{id}', [PurchaseOrderController::class, 'getPurchaseOrder']);
Route::post('/createPurchaseOrder', [PurchaseOrderController::class, 'createPurchaseOrder']);
Route::put('/updatePurchaseOrder/{id}', [PurchaseOrderController::class, 'updatePurchaseOrder']);
Route::delete('/deletePurchaseOrder/{id}', [PurchaseOrderController::class, 'deletePurchaseOrder']);