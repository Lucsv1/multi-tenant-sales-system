<?php

use App\Interface\Auth\AuthController;
use App\Interface\Customer\Http\Controllers\CustomerController;
use App\Interface\Product\Http\Controllers\ProductController;
use App\Interface\Sale\Http\Controllers\SaleController;
use App\Interface\Tenant\Http\Controllers\TenantController;
use App\Interface\User\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

Route::get('/docs', function () {
    $json = File::get(storage_path('api-docs/api-docs.json'));
    return response($json, 200)->header('Content-Type', 'application/json');
});

Route::get('/docs/view', function () {
    return view('swagger');
});

// Rotas de autenticação
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Sale report - any authenticated user
    Route::get('sale/report', [SaleController::class, 'report']);

    Route::middleware('role:Admin|Vendedor')->group(function () {
        //Customer
        Route::get('customer', [CustomerController::class, 'index']);
        Route::post('customer', [CustomerController::class, 'store']);
        Route::get('customer/{customer}', [CustomerController::class, 'show']);
        Route::put('customer/{customer}', [CustomerController::class, 'update']);
        Route::delete('customer/{customer}', [CustomerController::class, 'destroy']);

        //Sale
        Route::get('sale', [SaleController::class, 'index']);
        Route::post('sale', [SaleController::class, 'store']);
        Route::get('sale/{sale}', [SaleController::class, 'show']);
        Route::delete('sale/{sale}', [SaleController::class, 'cancel']);
    });

    Route::middleware('role:Admin')->group(function () {
        //Products
        Route::get('product', [ProductController::class, 'index']);
        Route::post('product', [ProductController::class, 'store']);
        Route::get('product/{product}', [ProductController::class, 'show']);
        Route::put('product/{product}', [ProductController::class, 'update']);
        Route::delete('product/{product}', [ProductController::class, 'destroy']);

        //User
        Route::get('user', [UserController::class, 'index']);
        Route::post('user', [UserController::class, 'store']);
        Route::get('user/{user}', [UserController::class, 'show']);
        Route::put('user/{user}', [UserController::class, 'update']);
        Route::delete('user/{user}', [UserController::class, 'destroy']);
    });

    Route::middleware('role:SuperAdmin')->group(function () {
        //Tenant
        Route::get('tenant', [TenantController::class, 'index']);
        Route::post('tenant', [TenantController::class, 'store']);
        Route::get('tenant/{tenant}', [TenantController::class, 'show']);
        Route::put('tenant/{tenant}', [TenantController::class, 'update']);
        Route::delete('tenant/{tenant}', [TenantController::class, 'destroy']);
    });
});
