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

Route::get('/customers', [CustomerController::class, 'getCustomers']);
Route::get('/customers/{id}', [CustomerController::class, 'getCustomer']);
Route::delete('/customers/{id}', [CustomerController::class, 'deleteCustomer']);
Route::put('/customers/{id}', [CustomerController::class, 'updateCustomer']);
Route::post('/customers/create', [CustomerController::class, 'createCustomer']);
