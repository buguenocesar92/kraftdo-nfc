<?php

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


// Debug route for testing
Route::get('/debug-bus-stop', function () {
    \Log::info('Debug route hit');

    return 'Debug route works';
});
