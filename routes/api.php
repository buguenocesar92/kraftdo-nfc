<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\QrCodeController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\TokenController;
use Illuminate\Support\Facades\Auth;
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

// Debug endpoint - TEMPORAL
Route::middleware([\App\Http\Middleware\AuthTokenFromCookie::class, 'auth:sanctum'])->get('/debug/user', function () {
    return response()->json([
        'user_id' => Auth::id(),
        'user_email' => Auth::user()?->email,
        'user_data' => Auth::user(),
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

// Rutas protegidas con autenticación (Sanctum) - DEFINIR PRIMERO
Route::middleware([\App\Http\Middleware\AuthTokenFromCookie::class, 'auth:sanctum'])->group(function () {
    // User status and onboarding routes
    Route::prefix('user')->group(function () {
        Route::get('status', [UserController::class, 'status']);
        Route::get('progress', [UserController::class, 'progress']);
        Route::put('onboarding/progress', [UserController::class, 'updateOnboardingProgress']);
        Route::put('onboarding/complete', [UserController::class, 'completeOnboarding']);
    });

    // CRUD completo de tokens para usuarios autenticados
    Route::apiResource('tokens', TokenController::class)->except(['show']);
    
    // QR Code generation routes for tokens
    Route::prefix('tokens/{token}/qr')->group(function () {
        Route::get('/', [QrCodeController::class, 'generate'])->name('api.tokens.qr.generate');
        Route::get('/multiple', [QrCodeController::class, 'generateMultiple'])->name('api.tokens.qr.multiple');
        Route::get('/branded', [QrCodeController::class, 'generateBranded'])->name('api.tokens.qr.branded');
        Route::get('/print', [QrCodeController::class, 'generatePrint'])->name('api.tokens.qr.print');
        Route::get('/analytics', [QrCodeController::class, 'analytics'])->name('api.tokens.qr.analytics');
        Route::get('/info', [QrCodeController::class, 'info'])->name('api.tokens.qr.info');
        Route::delete('/cache', [QrCodeController::class, 'clearCache'])->name('api.tokens.qr.cache');
    });

    // CRUD completo de contenido para usuarios autenticados
    Route::prefix('content')->group(function () {
        // Dynamic content management - ESTAS RUTAS DEBEN IR PRIMERO
        Route::post('dynamic', [ContentController::class, 'createDynamicContent']);
        Route::get('dynamic/{id}', [ContentController::class, 'getDynamicContent']);
        Route::put('dynamic/{id}', [ContentController::class, 'updateDynamicContent']);
        Route::delete('dynamic/{id}', [ContentController::class, 'deleteDynamicContent']);
        
        // Specific content types linked to dynamic content
        Route::post('profile/{dynamicContentId}', [ContentController::class, 'createProfileContent']);
        Route::get('profile/{dynamicContentId}', [ContentController::class, 'getProfileContent']);
        Route::put('profile/{profileId}', [ContentController::class, 'updateProfileContent']);
        
        Route::post('business/{dynamicContentId}', [ContentController::class, 'createBusinessContent']);
        Route::get('business/{dynamicContentId}', [ContentController::class, 'getBusinessContent']);
        Route::put('business/{businessId}', [ContentController::class, 'updateBusinessContent']);
        
        Route::post('gift/{dynamicContentId}', [ContentController::class, 'createGiftContent']);
        Route::get('gift/{dynamicContentId}', [ContentController::class, 'getGiftContent']);
        Route::put('gift/{giftId}', [ContentController::class, 'updateGiftContent']);
        
        // Social links for profiles
        Route::get('profile/{profileId}/social-links', [ContentController::class, 'getSocialLinks']);
        Route::post('profile/{profileId}/social-links', [ContentController::class, 'createSocialLink']);
        Route::delete('social-links/{linkId}', [ContentController::class, 'deleteSocialLink']);
        
        // Business products
        Route::get('business/{businessId}/products', [ContentController::class, 'getBusinessProducts']);
        Route::post('business/{businessId}/products', [ContentController::class, 'createBusinessProduct']);
        Route::put('products/{productId}', [ContentController::class, 'updateBusinessProduct']);
        Route::delete('products/{productId}', [ContentController::class, 'deleteBusinessProduct']);
        
        // Gift gallery
        Route::get('gift/{giftId}/gallery', [ContentController::class, 'getGiftGallery']);
        Route::post('gift/{giftId}/gallery', [ContentController::class, 'createGiftGalleryItem']);
        Route::delete('gallery/{itemId}', [ContentController::class, 'deleteGiftGalleryItem']);
        Route::delete('gallery/image/{imageId}', [ContentController::class, 'deleteGalleryImage']);
        
        // Multimedia content management
        Route::get('multimedia/{dynamicContentId}', [ContentController::class, 'getMultimediaContent']);
        
        // File upload routes
        Route::post('multimedia/{multimediaId}/audio', [ContentController::class, 'uploadAudioFile']);
        Route::post('multimedia/{multimediaId}/video', [ContentController::class, 'uploadVideoFile']);
        Route::post('gallery/{multimediaId}', [ContentController::class, 'uploadGalleryImage']);
        Route::post('profile/{profileId}/image', [ContentController::class, 'uploadProfileImage']);
        
        // Legacy routes - ESTAS VAN AL FINAL
        Route::delete('{type}/{id}', [ContentController::class, 'destroy']);
    });
});

// Rutas de contenido - acceso público - DESPUÉS DE LAS PROTEGIDAS
Route::prefix('content')->group(function () {
    Route::get('{type}/{id}', [ContentController::class, 'show']);
    Route::put('{type}/{id}', [ContentController::class, 'update']);
    Route::post('{type}', [ContentController::class, 'store']);
});

// Rutas de tokens - acceso público para visualización
Route::prefix('tokens')->group(function () {
    Route::get('{tokenId}', [TokenController::class, 'show']);
    Route::get('{tokenId}/products', [TokenController::class, 'showProducts']);
    
    // QR Code generation routes - public access for sharing
    Route::prefix('{token}/qr')->group(function () {
        Route::get('/', [QrCodeController::class, 'generate'])->name('api.public.tokens.qr.generate');
        Route::get('/multiple', [QrCodeController::class, 'generateMultiple'])->name('api.public.tokens.qr.multiple');
        Route::get('/info', [QrCodeController::class, 'info'])->name('api.public.tokens.qr.info');
    });
});

// Rutas de autenticación (sin Sanctum)
Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout'])->middleware([\App\Http\Middleware\AuthTokenFromCookie::class, 'auth:sanctum']);
