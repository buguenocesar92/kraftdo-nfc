<?php

use App\Http\Controllers\TokenController;
use App\Models\ContentGift;
use App\Models\DynamicContent;
use App\Models\NfcToken;
use App\Services\AnalyticsService;
use App\Services\TokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Mockery\MockInterface;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('TokenController Refactored Tests', function () {
    test('controller puede ser instanciado con dependencias', function () {
        $tokenService = Mockery::mock(TokenService::class);
        $analyticsService = Mockery::mock(AnalyticsService::class);

        $controller = new TokenController($tokenService, $analyticsService);

        expect($controller)->toBeInstanceOf(TokenController::class);
    });

    test('show method maneja token no encontrado', function () {
        $tokenService = Mockery::mock(TokenService::class);
        $analyticsService = Mockery::mock(AnalyticsService::class);

        $tokenService->shouldReceive('getTokenWithContent')
            ->with('invalid-token')
            ->andReturn(null);

        $tokenService->shouldReceive('validateToken')
            ->with(null)
            ->andReturn(false);

        $tokenService->shouldReceive('handleNotFound')
            ->once()
            ->andReturn(response()->json(['message' => 'Token no encontrado'], 404));

        $controller = new TokenController($tokenService, $analyticsService);
        $request = Request::create('/token/invalid-token');
        $request->headers->set('Accept', 'application/json');

        $response = $controller->show($request, 'invalid-token');

        expect($response->getStatusCode())->toBe(404);
    });

    test('show method procesa token válido correctamente', function () {
        // Crear datos de prueba
        $user = \App\Models\User::factory()->create();
        $token = NfcToken::factory()->create(['user_id' => $user->id, 'content_type' => 'GIFT']);
        $dynamicContent = DynamicContent::factory()->create(['nfc_token_id' => $token->id]);
        $contentGift = ContentGift::factory()->create();

        $tokenData = [
            'token' => $token,
            'dynamicContent' => $dynamicContent,
            'content' => ['gift' => $contentGift, 'multimedia' => null],
        ];

        $tokenService = Mockery::mock(TokenService::class);
        $analyticsService = Mockery::mock(AnalyticsService::class);

        $tokenService->shouldReceive('getTokenWithContent')
            ->with($token->token_id)
            ->andReturn($tokenData);

        $tokenService->shouldReceive('validateToken')
            ->with($tokenData)
            ->andReturn(true);

        $analyticsService->shouldReceive('recordAccess')
            ->with($tokenData)
            ->once();

        $tokenService->shouldReceive('renderResponse')
            ->once()
            ->andReturn(response()->json(['data' => $tokenData]));

        $controller = new TokenController($tokenService, $analyticsService);
        $request = Request::create('/token/' . $token->token_id);
        $request->headers->set('Accept', 'application/json');

        $response = $controller->show($request, $token->token_id);

        expect($response)->not->toBeNull();
    });

    test('show method maneja token inactivo', function () {
        $user = \App\Models\User::factory()->create();
        $token = NfcToken::factory()->create([
            'user_id' => $user->id,
            'content_type' => 'GIFT',
            'is_active' => false,
        ]);
        $dynamicContent = DynamicContent::factory()->create(['nfc_token_id' => $token->id]);

        $tokenData = [
            'token' => $token,
            'dynamicContent' => $dynamicContent,
            'content' => [],
        ];

        $tokenService = Mockery::mock(TokenService::class);
        $analyticsService = Mockery::mock(AnalyticsService::class);

        $tokenService->shouldReceive('getTokenWithContent')
            ->andReturn($tokenData);

        $tokenService->shouldReceive('validateToken')
            ->andReturn(true);

        $tokenService->shouldReceive('handleInactiveToken')
            ->once()
            ->andReturn(response()->json(['message' => 'Token inactivo']));

        $controller = new TokenController($tokenService, $analyticsService);
        $request = Request::create('/token/' . $token->token_id);
        $request->headers->set('Accept', 'application/json');

        $response = $controller->show($request, $token->token_id);

        expect($response)->not->toBeNull();
    });

    test('showProducts method valida tipo de contenido', function () {
        $user = \App\Models\User::factory()->create();
        $token = NfcToken::factory()->create([
            'user_id' => $user->id,
            'content_type' => 'GIFT', // No es BUSINESS
        ]);

        $tokenData = [
            'token' => $token,
            'dynamicContent' => null,
            'content' => [],
        ];

        $tokenService = Mockery::mock(TokenService::class);
        $analyticsService = Mockery::mock(AnalyticsService::class);

        $tokenService->shouldReceive('getTokenWithContent')
            ->andReturn($tokenData);

        $tokenService->shouldReceive('validateToken')
            ->andReturn(true);

        $tokenService->shouldReceive('handleNotFound')
            ->once()
            ->with(Mockery::any(), 'Esta página solo está disponible para negocios')
            ->andReturn(response()->json(['message' => 'No disponible'], 404));

        $controller = new TokenController($tokenService, $analyticsService);
        $request = Request::create('/token/' . $token->token_id . '/products');
        $request->headers->set('Accept', 'application/json');

        $response = $controller->showProducts($request, $token->token_id);

        expect($response->getStatusCode())->toBe(404);
    });
});
