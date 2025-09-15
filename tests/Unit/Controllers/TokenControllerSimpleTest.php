<?php

use Tests\TestCase;
use App\Http\Controllers\TokenController;
use App\Models\NfcAnalytic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

uses(TestCase::class, RefreshDatabase::class);

describe('TokenController Simple Tests', function () {
    
    test('recordAnalyticsAsync method registra acceso correctamente', function () {
        $controller = new TokenController();
        $method = new ReflectionMethod($controller, 'recordAnalyticsAsync');
        $method->setAccessible(true);
        
        $contentId = 'test-content-id';
        $contentType = 'GIFT';
        $tokenId = 1;
        
        $method->invoke($controller, $contentId, $contentType, $tokenId);
        
        expect(NfcAnalytic::count())->toBe(1);
        $analytic = NfcAnalytic::first();
        expect($analytic->content_id)->toBe($contentId);
        expect($analytic->content_type)->toBe($contentType);
        expect($analytic->nfc_token_id)->toBe($tokenId);
    });

    test('recordAnalyticsAsync method maneja excepciones silenciosamente', function () {
        $controller = new TokenController();
        $method = new ReflectionMethod($controller, 'recordAnalyticsAsync');
        $method->setAccessible(true);
        
        // Mock para simular error en NfcAnalytic::recordAccess
        Log::shouldReceive('warning')
            ->once()
            ->with('Analytics recording failed', \Mockery::type('array'));
        
        // Crear un método que falle (simular una base de datos caída)
        NfcAnalytic::shouldReceive('recordAccess')
            ->andThrow(new \Exception('Database connection failed'));
        
        // No debe lanzar excepción, debe manejarla silenciosamente
        expect(fn() => $method->invoke($controller, 'test', 'GIFT', 1))
            ->not->toThrow(\Exception::class);
    });

    test('controller puede ser instanciado', function () {
        $controller = new TokenController();
        
        expect($controller)->toBeInstanceOf(TokenController::class);
    });

    test('recordAnalyticsAsync method valida parámetros', function () {
        $controller = new TokenController();
        $method = new ReflectionMethod($controller, 'recordAnalyticsAsync');
        $method->setAccessible(true);
        
        // Test con diferentes tipos de parámetros
        $method->invoke($controller, 'content-123', 'PROFILE', 5);
        
        expect(NfcAnalytic::count())->toBe(1);
        $analytic = NfcAnalytic::first();
        expect($analytic->content_id)->toBe('content-123');
        expect($analytic->content_type)->toBe('PROFILE');
        expect($analytic->nfc_token_id)->toBe(5);
    });

    test('recordAnalyticsAsync method procesa múltiples registros', function () {
        $controller = new TokenController();
        $method = new ReflectionMethod($controller, 'recordAnalyticsAsync');
        $method->setAccessible(true);
        
        // Registrar múltiples accesos
        $method->invoke($controller, 'content-1', 'GIFT', 1);
        $method->invoke($controller, 'content-2', 'MENU', 2);
        $method->invoke($controller, 'content-3', 'PROFILE', 3);
        
        expect(NfcAnalytic::count())->toBe(3);
        
        $types = NfcAnalytic::pluck('content_type')->toArray();
        expect($types)->toContain('GIFT')
            ->and($types)->toContain('MENU')
            ->and($types)->toContain('PROFILE');
    });
});