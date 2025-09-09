<?php

use App\Http\Controllers\NfcContentController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

// ========================================
// RUTAS PÚBLICAS PARA CONTENIDO NFC
// ========================================

// RETROCOMPATIBILIDAD: Formato antiguo /nfc?TYPE=X&ID=uuid
Route::get('/nfc', [NfcContentController::class, 'showLegacy'])
    ->name('nfc.legacy');

// 🎯 Rutas de Onboarding NFC
Route::get('/nfc/onboarding', [NfcContentController::class, 'onboarding'])->name('nfc.onboarding');
Route::post('/nfc/onboarding', [NfcContentController::class, 'createAccount'])->name('nfc.create-account');

// 🔗 Ruta para asignar chip a usuario autenticado
Route::post('/nfc/assign-token', [NfcContentController::class, 'assignTokenToAuthenticatedUser'])
    ->middleware('auth')
    ->name('nfc.assign-token');

// Mostrar contenido por content_id (UUID)
Route::get('/c/{contentId}', [NfcContentController::class, 'show'])
    ->name('nfc.content')
    ->where('contentId', '[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}');

// Mostrar contenido por token_id (UUID del chip físico)
Route::get('/t/{tokenId}', [NfcContentController::class, 'showByToken'])
    ->name('nfc.token')
    ->where('tokenId', '[A-Za-z0-9\-]+');

// Información sobre NFC
Route::get('/nfc/info', [NfcContentController::class, 'info'])
    ->name('nfc.info');

// API para validación de contenido (público)
Route::get('/api/validate/content/{contentId}', [NfcContentController::class, 'validateContent'])
    ->name('api.validate.content');

Route::get('/api/validate/token/{tokenId}', [NfcContentController::class, 'validateToken'])
    ->name('api.validate.token');

// ========================================
// RUTAS PRIVADAS PARA USUARIOS AUTENTICADOS
// ========================================

Route::middleware(['auth'])->group(function () {
    // Vista previa de contenido (solo propietarios)
    Route::get('/preview/{contentId}', [NfcContentController::class, 'preview'])
        ->name('nfc.preview')
        ->where('contentId', '[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}');
    
    // Estadísticas de contenido (solo propietarios)
    Route::get('/api/stats/{contentId}', [NfcContentController::class, 'getStats'])
        ->name('api.content.stats')
        ->where('contentId', '[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}');
});

require __DIR__.'/auth.php';
