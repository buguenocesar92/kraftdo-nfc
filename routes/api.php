<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\TokenController;
use Illuminate\Support\Facades\Route;

// Health check para el API
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'app' => config('app.name'),
        'env' => config('app.env'),
        'version' => '1.0.0',
    ]);
});

// SPA Authentication routes (Frontend Next.js)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware([\App\Http\Middleware\AuthTokenFromCookie::class, 'auth:sanctum'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
    });
});

// Legacy auth routes (keep for backward compatibility)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware([\App\Http\Middleware\AuthTokenFromCookie::class, 'auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
});

// Rutas de contenido - acceso público
Route::prefix('content')->group(function () {
    Route::get('{type}/{id}', [ContentController::class, 'show']);
    Route::put('{type}/{id}', [ContentController::class, 'update']);
    Route::post('{type}', [ContentController::class, 'store']);
});

// Rutas de tokens - acceso público para visualización
Route::prefix('tokens')->group(function () {
    Route::get('{tokenId}', [TokenController::class, 'show']);
    Route::get('{tokenId}/products', [TokenController::class, 'showProducts']);
});

// Rutas protegidas con autenticación (Sanctum)
Route::middleware([\App\Http\Middleware\AuthTokenFromCookie::class, 'auth:sanctum'])->group(function () {
    // Usuario autenticado handled by AuthController above

    // CRUD completo de tokens para usuarios autenticados
    Route::apiResource('tokens', TokenController::class)->except(['show']);

    // CRUD completo de contenido para usuarios autenticados
    Route::prefix('content')->group(function () {
        Route::delete('{type}/{id}', [ContentController::class, 'destroy']);
    });
});

// Rutas de autenticación (sin Sanctum)
Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout'])->middleware([\App\Http\Middleware\AuthTokenFromCookie::class, 'auth:sanctum']);
