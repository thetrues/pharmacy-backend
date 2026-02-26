<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PurchaseOrderController;

Route::get('/getPurchaseOrders', [PurchaseOrderController::class, 'getPurchaseOrders']);
Route::get('/getPurchaseOrder/{id}', [PurchaseOrderController::class, 'getPurchaseOrder']);
Route::post('/createPurchaseOrder', [PurchaseOrderController::class, 'createPurchaseOrder']);
Route::put('/updatePurchaseOrder/{id}', [PurchaseOrderController::class, 'updatePurchaseOrder']);
Route::delete('/deletePurchaseOrder/{id}', [PurchaseOrderController::class, 'deletePurchaseOrder']);

// api/getPurchaseOrders, api/getPurchaseOrder/{id}, api/createPurchaseOrder, api/updatePurchaseOrder/{id}, api/deletePurchaseOrder/{id}