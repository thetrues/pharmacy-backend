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
  include 'apis/user_api.php';
//products
  include 'apis/product_api.php';
//sales
  include 'apis/sales_api.php';
//customers
  include 'apis/customers_api.php';
   Route::middleware('auth:sanctum')->group(function () {
//procurement
  include 'apis/procurement_api.php';

//suppliers
 
  include 'apis/supplier_api.php';
  });


});