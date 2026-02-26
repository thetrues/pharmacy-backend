<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SupplierController;

Route::get('/suppliers', [SupplierController::class, 'getSuppliers']);
Route::get('/suppliers/{id}', [SupplierController::class, 'getSupplier']);
Route::delete('/suppliers/{id}', [SupplierController::class, 'deleteSupplier']);
Route::put('/suppliers/{id}', [SupplierController::class, 'updateSupplier']);
Route::post('/suppliers/create', [SupplierController::class, 'createSupplier']);

// api/suppliers, api/suppliers/{id}, api/suppliers/create, api/suppliers/{id} (PUT), api/suppliers/{id} (DELETE)