<?php

use App\Http\Controllers\TokenController;
use Illuminate\Support\Facades\Route;

// Health check para Docker
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'app' => config('app.name'),
        'env' => config('app.env')
    ]);
});


Route::get('/token/{tokenId}', [TokenController::class, 'show'])->name('token.show')
    ->where('tokenId', '[A-Za-z0-9\-]+');

Route::get('/token/{tokenId}/products', [TokenController::class, 'showProducts'])->name('token.products')
    ->where('tokenId', '[A-Za-z0-9\-]+');


