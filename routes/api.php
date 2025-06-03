<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\ProductController;

// Public routes
Route::get('/tenants', [TenantController::class, 'index']);
Route::get('/tenants/{id}', [TenantController::class, 'show']);
Route::post('/tenants', [TenantController::class, 'create']);

// Authentication routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Public product routes
Route::get('/products/all', [ProductController::class, 'getAllProducts']);
Route::get('/stores/{slug}/products', [ProductController::class, 'getStoreProducts']);

// Public add tenant route
Route::post('/tenants/add', [TenantController::class, 'create']);

// Protected routes (require authentication and tenant context)
Route::middleware(['tenant-sanctum-auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Product CRUD
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
});