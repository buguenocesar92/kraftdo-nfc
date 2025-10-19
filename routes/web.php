<?php

use App\Http\Controllers\TokenController;
use Illuminate\Support\Facades\Route;

// Include auth routes
require __DIR__ . '/auth.php';

// Home route
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Health check para Docker
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'app' => config('app.name'),
        'env' => config('app.env'),
    ]);
});


Route::get('/token/{tokenId}', [TokenController::class, 'show'])->name('token.show')
    ->where('tokenId', '[A-Za-z0-9\-]+');

// Debug route for testing
Route::get('/debug-bus-stop', function () {
    \Log::info('Debug route hit');

    return 'Debug route works';
});

Route::get('/token/{tokenId}/products', [TokenController::class, 'showProducts'])->name('token.products')
    ->where('tokenId', '[A-Za-z0-9\-]+');
