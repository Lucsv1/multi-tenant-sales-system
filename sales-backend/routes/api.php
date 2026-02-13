<?php

use App\Interface\Auth\AuthController;
use App\Interface\Customer\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;


// Rotas de autenticação
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::middleware('role:Admin|Vendedor')->group(function () {
        //Costumer
        Route::get('customer', [CustomerController::class, 'index']);
        Route::post('customer', [CustomerController::class, 'store']);
        Route::get('customer/{customer}', [CustomerController::class, 'show']);
        Route::put('customer/{customer}', [CustomerController::class, 'update']);
        Route::delete('customer/{customer}', [CustomerController::class, 'destroy']);
    });

});
