<?php

use App\Interface\Auth\AuthController;
use App\Interface\Customer\Http\Controllers\CustomerController;
use App\Interface\Product\Http\Controllers\ProductController;
use App\Interface\Sale\Http\Controllers\SaleController;
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

        //Sale
        Route::get('sale', [SaleController::class, 'index']);
        Route::post('sale', [SaleController::class, 'store']);
        Route::get('sale/{sale}', [SaleController::class, 'show']);
        Route::put('sale/{sale}', [SaleController::class, 'update']);
        Route::delete('sale/{sale}', [SaleController::class, 'cancel']);

        //Products
        Route::get('product', [ProductController::class, 'index']);
        Route::post('product', [ProductController::class, 'store']);
        Route::get('product/{product}', [ProductController::class, 'show']);
        Route::put('product/{product}', [ProductController::class, 'update']);
        Route::delete('product/{product}', [ProductController::class, 'destroy']);

    });

    Route::middleware('role:Admin')->group(function () {

        // //Products
        // Route::get('product', [ProductController::class, 'index']);
        // Route::post('product', [ProductController::class, 'store']);
        // Route::get('product/{product}', [ProductController::class, 'show']);
        // Route::put('product/{product}', [ProductController::class, 'update']);
        // Route::delete('product/{product}', [ProductController::class, 'destroy']);

    });


});
