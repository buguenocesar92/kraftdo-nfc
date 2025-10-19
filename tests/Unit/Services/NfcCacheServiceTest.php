<?php

use App\Models\DynamicContent;
use App\Models\NfcAnalytic;
use App\Models\NfcToken;
use App\Services\NfcCacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('NfcCacheService', function () {
    beforeEach(function () {
        Cache::flush();
    });

    test('obtiene token con contenido desde cache', function () {
        $token = NfcToken::factory()->gift()->create(['is_active' => true]);
        $content = DynamicContent::factory()->gift()->create(['nfc_token_id' => $token->id]);

        $result = NfcCacheService::getTokenWithContent($token->token_id);

        expect($result)->not()->toBeNull()
            ->and($result['token'])->toBeInstanceOf(NfcToken::class)
            ->and($result['dynamicContent'])->toBeInstanceOf(DynamicContent::class);
    });

    test('retorna null para token inexistente', function () {
        $result = NfcCacheService::getTokenWithContent('non-existent-token');

        expect($result)->toBeNull();
    });

    test('retorna null para token inactivo', function () {
        $token = NfcToken::factory()->inactive()->create();

        $result = NfcCacheService::getTokenWithContent($token->token_id);

        expect($result)->toBeNull();
    });

    test('cachea token PROFILE con enlaces sociales', function () {
        $token = NfcToken::factory()->profile()->create(['is_active' => true]);
        $content = DynamicContent::factory()->profile()->create(['nfc_token_id' => $token->id]);

        $result = NfcCacheService::getTokenWithContent($token->token_id);

        expect($result)->not()->toBeNull()
            ->and($result['dynamicContent'])->toBeInstanceOf(DynamicContent::class);
    });

    test('usa cache en segunda llamada', function () {
        $token = NfcToken::factory()->gift()->create(['is_active' => true]);
        $content = DynamicContent::factory()->gift()->create(['nfc_token_id' => $token->id]);

        // Primera llamada
        $start = microtime(true);
        $result1 = NfcCacheService::getTokenWithContent($token->token_id);
        $time1 = microtime(true) - $start;

        // Segunda llamada (debería usar cache)
        $start = microtime(true);
        $result2 = NfcCacheService::getTokenWithContent($token->token_id);
        $time2 = microtime(true) - $start;

        expect($result1)->not()->toBeNull()
            ->and($result2)->not()->toBeNull()
            ->and($time2)->toBeLessThan($time1 * 0.5); // Cache debería ser mucho más rápido
    });

    test('obtiene analytics cacheadas', function () {
        $contentId = 'test-content-123';
        NfcAnalytic::factory()->count(5)->create(['content_id' => $contentId, 'accessed_at' => now()]);

        $result = NfcCacheService::getCachedAnalytics($contentId);

        expect($result)->toHaveKeys([
            'total_views', 'unique_views', 'last_access', 'views_today',
        ])
            ->and($result['total_views'])->toBe(5);
    });

    test('obtiene estadísticas globales cacheadas', function () {
        NfcAnalytic::factory()->count(10)->create();
        NfcToken::factory()->count(3)->create(['is_active' => true, 'last_used_at' => null]);

        $result = NfcCacheService::getCachedGlobalStats();

        expect($result)->toHaveKeys([
            'total_scans', 'unique_scans', 'active_tokens',
        ])
            ->and($result['total_scans'])->toBe(10)
            ->and($result['active_tokens'])->toBeGreaterThanOrEqual(3);
    });

    test('obtiene planes de personalización cacheados', function () {
        $result = NfcCacheService::getCachedCustomizationPlans();

        expect($result)->toHaveKeys(['BASIC', 'STANDARD', 'PREMIUM', 'DELUXE'])
            ->and($result['BASIC'])->toHaveKeys(['name', 'description', 'features']);
    });

    test('obtiene ROI cacheado de token', function () {
        $token = NfcToken::factory()->create([
            'purchase_price' => 100.00,
            'total_investment_views' => 50,
        ]);

        $result = NfcCacheService::getCachedTokenROI($token->id);

        expect($result)->toHaveKeys([
            'total_views', 'purchase_price', 'roi_percentage',
        ])
            ->and($result['total_views'])->toBe(50)
            ->and($result['purchase_price'])->toBe(100.0);
    });

    test('invalida cache de token correctamente', function () {
        $token = NfcToken::factory()->gift()->create(['is_active' => true]);
        $content = DynamicContent::factory()->gift()->create(['nfc_token_id' => $token->id]);

        // Cachear primero
        $result1 = NfcCacheService::getTokenWithContent($token->token_id);
        expect($result1)->not()->toBeNull();

        // Invalidar cache
        NfcCacheService::invalidateTokenCache($token->token_id);

        // Verificar que el cache fue invalidado
        $cacheKey = "nfc_token_full:{$token->token_id}";
        expect(Cache::has($cacheKey))->toBeFalse();
    });

    test('invalida cache de contenido correctamente', function () {
        $contentId = 'test-content-456';
        $token = NfcToken::factory()->create();
        $content = DynamicContent::factory()->create([
            'content_id' => $contentId,
            'nfc_token_id' => $token->id,
        ]);

        // Cachear analytics
        NfcCacheService::getCachedAnalytics($contentId);

        // Invalidar
        NfcCacheService::invalidateContentCache($contentId);

        // Verificar invalidación
        expect(Cache::has("analytics_stats:{$contentId}"))->toBeFalse();
    });

    test('invalida cache de analytics correctamente', function () {
        $contentId = 'test-content-789';

        // Cachear analytics y stats globales
        NfcCacheService::getCachedAnalytics($contentId);
        NfcCacheService::getCachedGlobalStats();

        // Invalidar
        NfcCacheService::invalidateAnalyticsCache($contentId);

        // Verificar invalidación
        expect(Cache::has("analytics_stats:{$contentId}"))->toBeFalse()
            ->and(Cache::has("global_analytics_stats"))->toBeFalse();
    });

    test('limpia todo el cache NFC', function () {
        $token = NfcToken::factory()->create();

        // Crear múltiples entradas de cache
        NfcCacheService::getTokenWithContent($token->token_id);
        NfcCacheService::getCachedCustomizationPlans();
        NfcCacheService::getCachedGlobalStats();

        // Limpiar todo
        NfcCacheService::clearAllNfcCache();

        // En este caso, Cache::flush() limpia todo
        expect(Cache::has("customization_plans"))->toBeFalse()
            ->and(Cache::has("global_analytics_stats"))->toBeFalse();
    });

    test('obtiene estadísticas de cache', function () {
        $stats = NfcCacheService::getCacheStats();

        expect($stats)->toHaveKeys(['cache_driver'])
            ->and($stats['cache_driver'])->toBe('array'); // En testing usa array driver
    });

    test('maneja tokens sin contenido dinámico', function () {
        $token = NfcToken::factory()->create(['is_active' => true]);
        // No crear contenido dinámico

        $result = NfcCacheService::getTokenWithContent($token->token_id);

        expect($result)->toBeNull();
    });

    test('maneja ROI de token inexistente', function () {
        $result = NfcCacheService::getCachedTokenROI(99999);

        expect($result)->toBeArray()
            ->and($result)->toBeEmpty();
    });

    test('respeta TTL configurados', function () {
        // Verificar que las constantes TTL están definidas
        expect(NfcCacheService::TOKEN_CACHE_TTL)->toBe(3600)
            ->and(NfcCacheService::CONTENT_CACHE_TTL)->toBe(1800)
            ->and(NfcCacheService::ANALYTICS_CACHE_TTL)->toBe(600)
            ->and(NfcCacheService::STATIC_CACHE_TTL)->toBe(86400);
    });
});
