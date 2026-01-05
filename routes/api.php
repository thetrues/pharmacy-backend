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
});