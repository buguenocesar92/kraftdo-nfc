<?php

use Tests\TestCase;
use App\Http\Controllers\TokenController;
use App\Models\NfcAnalytic;
use App\Models\NfcToken;
use App\Services\NfcCacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

uses(TestCase::class, RefreshDatabase::class);

describe('TokenController Simple Tests', function () {
    
    test('recordAnalyticsAsync method registra acceso correctamente', function () {
        // Simular request HTTP
        $this->get('/');
        
        // Mock del cache service para evitar errores
        $this->mock(NfcCacheService::class, function ($mock) {
            $mock->shouldReceive('invalidateAnalyticsCache')->andReturn(null);
        });
        
        $controller = new TokenController();
        $method = new ReflectionMethod($controller, 'recordAnalyticsAsync');
        $method->setAccessible(true);
        
        // Crear un token válido primero
        $token = NfcToken::factory()->create();
        
        $contentId = 'test-content-id';
        $contentType = 'GIFT';
        $tokenId = $token->id;
        
        $method->invoke($controller, $contentId, $contentType, $tokenId);
        
        expect(NfcAnalytic::count())->toBe(1);
        $analytic = NfcAnalytic::first();
        expect($analytic->content_id)->toBe($contentId);
        expect($analytic->content_type)->toBe($contentType);
        expect($analytic->nfc_token_id)->toBe($tokenId);
    });

    test('recordAnalyticsAsync method maneja excepciones silenciosamente', function () {
        // Simular request HTTP pero con datos faltantes que causen error
        $this->withoutMiddleware();
        
        // Crear un token válido
        $token = NfcToken::factory()->create();
        
        $controller = new TokenController();
        $method = new ReflectionMethod($controller, 'recordAnalyticsAsync');
        $method->setAccessible(true);
        
        // Mock para capturar el log de warning (puede o no ocurrir dependiendo del error)
        Log::shouldReceive('warning')
            ->atMost()
            ->once()
            ->with('Analytics recording failed', \Mockery::type('array'));
        
        // Mock del cache service para que falle
        $this->mock(NfcCacheService::class, function ($mock) {
            $mock->shouldReceive('invalidateAnalyticsCache')
                ->andThrow(new \Exception('Cache service failed'));
        });
        
        // No debe lanzar excepción, debe manejarla silenciosamente
        expect(fn() => $method->invoke($controller, 'test', 'GIFT', $token->id))
            ->not->toThrow(\Exception::class);
    });

    test('controller puede ser instanciado', function () {
        $controller = new TokenController();
        
        expect($controller)->toBeInstanceOf(TokenController::class);
    });

    test('recordAnalyticsAsync method valida parámetros', function () {
        // Simular request HTTP
        $this->get('/');
        
        // Mock del cache service para evitar errores
        $this->mock(NfcCacheService::class, function ($mock) {
            $mock->shouldReceive('invalidateAnalyticsCache')->andReturn(null);
        });
        
        $controller = new TokenController();
        $method = new ReflectionMethod($controller, 'recordAnalyticsAsync');
        $method->setAccessible(true);
        
        // Crear un token válido primero
        $token = NfcToken::factory()->create();
        
        // Test con diferentes tipos de parámetros
        $method->invoke($controller, 'content-123', 'PROFILE', $token->id);
        
        expect(NfcAnalytic::count())->toBe(1);
        $analytic = NfcAnalytic::first();
        expect($analytic->content_id)->toBe('content-123');
        expect($analytic->content_type)->toBe('PROFILE');
        expect($analytic->nfc_token_id)->toBe($token->id);
    });

    test('recordAnalyticsAsync method procesa múltiples registros', function () {
        // Simular request HTTP
        $this->get('/');
        
        // Mock del cache service para evitar errores
        $this->mock(NfcCacheService::class, function ($mock) {
            $mock->shouldReceive('invalidateAnalyticsCache')->andReturn(null);
        });
        
        $controller = new TokenController();
        $method = new ReflectionMethod($controller, 'recordAnalyticsAsync');
        $method->setAccessible(true);
        
        // Crear tokens válidos primero
        $token1 = NfcToken::factory()->create();
        $token2 = NfcToken::factory()->create();
        $token3 = NfcToken::factory()->create();
        
        // Registrar múltiples accesos
        $method->invoke($controller, 'content-1', 'GIFT', $token1->id);
        $method->invoke($controller, 'content-2', 'MENU', $token2->id);
        $method->invoke($controller, 'content-3', 'PROFILE', $token3->id);
        
        expect(NfcAnalytic::count())->toBe(3);
        
        $types = NfcAnalytic::pluck('content_type')->toArray();
        expect($types)->toContain('GIFT')
            ->and($types)->toContain('MENU')
            ->and($types)->toContain('PROFILE');
    });
});