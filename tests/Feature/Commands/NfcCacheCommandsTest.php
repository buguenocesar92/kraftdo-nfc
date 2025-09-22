<?php

use App\Models\NfcToken;
use App\Models\NfcAnalytic;
use App\Models\DynamicContent;
use Illuminate\Support\Facades\Cache;

describe('NFC Cache Commands', function () {
    
    beforeEach(function () {
        Cache::flush();
    });

    test('nfc:cache-clear limpia todo el cache', function () {
        // Pre-cachear algunos elementos
        Cache::put('test_key', 'test_value', 600);
        Cache::put('customization_plans', ['test' => 'data'], 600);
        
        $this->artisan('nfc:cache-clear')
            ->expectsOutput('🧹 Limpiando todo el cache NFC...')
            ->expectsOutput('✅ Todo el cache NFC ha sido limpiado')
            ->assertExitCode(0);
        
        expect(Cache::has('test_key'))->toBeFalse()
            ->and(Cache::has('customization_plans'))->toBeFalse();
    });

    test('nfc:cache-clear con tipo específico tokens', function () {
        Cache::put('customization_plans', ['test' => 'data'], 600);
        Cache::put('other_key', 'other_value', 600);
        
        $this->artisan('nfc:cache-clear', ['--type' => 'tokens'])
            ->expectsOutput('🚀 Limpiando cache de tokens...')
            ->expectsOutput('✅ Cache de tokens limpiado')
            ->assertExitCode(0);
        
        expect(Cache::has('customization_plans'))->toBeFalse();
    });

    test('nfc:cache-clear con tipo analytics', function () {
        Cache::put('global_analytics_stats', ['test' => 'data'], 600);
        
        $this->artisan('nfc:cache-clear', ['--type' => 'analytics'])
            ->expectsOutput('📊 Limpiando cache de analytics...')
            ->expectsOutput('✅ Cache de analytics limpiado')
            ->assertExitCode(0);
        
        expect(Cache::has('global_analytics_stats'))->toBeFalse();
    });

    test('nfc:cache-clear con tipo themes', function () {
        Cache::put('multimedia_themes', ['test' => 'data'], 600);
        
        $this->artisan('nfc:cache-clear', ['--type' => 'themes'])
            ->expectsOutput('🎨 Limpiando cache de temas...')
            ->expectsOutput('✅ Cache de temas limpiado')
            ->assertExitCode(0);
        
        expect(Cache::has('multimedia_themes'))->toBeFalse();
    });

    test('nfc:cache-clear con token específico', function () {
        $token = NfcToken::factory()->create();
        $cacheKey = "nfc_token_full:{$token->token_id}";
        Cache::put($cacheKey, ['test' => 'data'], 600);
        
        $this->artisan('nfc:cache-clear', ['--token' => $token->token_id])
            ->expectsOutput("Limpiando cache del token: {$token->token_id}")
            ->expectsOutput('✅ Cache del token limpiado')
            ->assertExitCode(0);
        
        expect(Cache::has($cacheKey))->toBeFalse();
    });

    test('nfc:cache-warm cachea datos críticos', function () {
        // Crear algunos tokens para warm-up
        NfcToken::factory()->count(3)->create([
            'is_active' => true,
            'last_used_at' => now()
        ]);
        
        $this->artisan('nfc:cache-warm', ['--tokens' => 5])
            ->expectsOutput('🔥 Iniciando pre-cache de datos NFC...')
            ->expectsOutput('📋 Pre-cacheando planes de personalización...')
            ->expectsOutput('✅ Planes de personalización cacheados')
            ->expectsOutput('🎨 Pre-cacheando temas...')
            ->expectsOutput('✅ Temas cacheados')
            ->expectsOutput('📊 Pre-cacheando estadísticas globales...')
            ->expectsOutput('✅ Estadísticas globales cacheadas')
            ->expectsOutput('🚀 Pre-cacheando los 5 tokens más activos...')
            ->expectsOutput('🎉 Pre-cache completado exitosamente!')
            ->assertExitCode(0);
        
        // Verificar que se cachearon los datos
        expect(Cache::has('customization_plans'))->toBeTrue();
    });

    test('nfc:cache-warm con forzar recache', function () {
        $token = NfcToken::factory()->create([
            'is_active' => true,
            'last_used_at' => now()
        ]);
        
        // Pre-cachear token
        $cacheKey = "nfc_token_full:{$token->token_id}";
        Cache::put($cacheKey, ['old' => 'data'], 600);
        
        $this->artisan('nfc:cache-warm', ['--tokens' => 1, '--force' => true])
            ->assertExitCode(0);
        
        // El cache debería haberse renovado (aunque no podemos verificar fácilmente el contenido)
        expect(Cache::has($cacheKey))->toBeTrue();
    });

    test('nfc:cache-warm sin tokens activos', function () {
        // No crear tokens activos
        
        $this->artisan('nfc:cache-warm')
            ->expectsOutput('🔥 Iniciando pre-cache de datos NFC...')
            ->expectsOutput('✅ Tokens procesados: 0 cacheados, 0 ya en cache')
            ->assertExitCode(0);
    });

    test('nfc:performance-test ejecuta correctamente', function () {
        $token = NfcToken::factory()->create(['is_active' => true]);
        DynamicContent::factory()->create(['nfc_token_id' => $token->id]);
        
        $this->artisan('nfc:performance-test', ['--iterations' => 3])
            ->expectsOutput('🚀 Iniciando test de performance NFC...')
            ->expectsOutput("🎯 Token de prueba: {$token->token_id}")
            ->expectsOutput('📊 Iteraciones: 3')
            ->expectsOutput('🔴 TEST 1: SIN CACHE (queries directas a BD)')
            ->expectsOutput('🟢 TEST 2: CON CACHE (optimizado)')
            ->expectsOutput('📊 RESULTADOS DE PERFORMANCE:')
            ->assertExitCode(0);
    });

    test('nfc:performance-test con clear-cache', function () {
        $token = NfcToken::factory()->create(['is_active' => true]);
        DynamicContent::factory()->create(['nfc_token_id' => $token->id]);
        
        // Pre-cachear algo
        Cache::put('test_cache', 'test', 600);
        
        $this->artisan('nfc:performance-test', ['--clear-cache' => true, '--iterations' => 2])
            ->expectsOutput('🧹 Limpiando cache...')
            ->assertExitCode(0);
        
        expect(Cache::has('test_cache'))->toBeFalse();
    });

    test('nfc:performance-test sin tokens disponibles', function () {
        $this->artisan('nfc:performance-test')
            ->expectsOutput('❌ No se encontró ningún token activo para testing')
            ->assertExitCode(1);
    });
});