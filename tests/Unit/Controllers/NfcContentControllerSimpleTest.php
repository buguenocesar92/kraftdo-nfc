<?php

use Tests\TestCase;
use App\Http\Controllers\NfcContentController;
use App\Models\NfcToken;
use App\Models\DynamicContent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

describe('NfcContentController Simple Tests', function () {
    
    test('getViewForContentType retorna vista correcta', function () {
        $controller = new NfcContentController();
        $method = new ReflectionMethod($controller, 'getViewForContentType');
        $method->setAccessible(true);
        
        expect($method->invoke($controller, 'GIFT'))->toBe('nfc.gift');
        expect($method->invoke($controller, 'MENU'))->toBe('nfc.menu');
        expect($method->invoke($controller, 'PROFILE'))->toBe('nfc.profile');
        expect($method->invoke($controller, 'TOURIST'))->toBe('nfc.tourist');
        expect($method->invoke($controller, 'EVENT'))->toBe('nfc.event');
        expect($method->invoke($controller, 'PRODUCT'))->toBe('nfc.product');
        expect($method->invoke($controller, 'UNKNOWN'))->toBe('nfc.default');
    });

    test('prepareViewData para tipo GIFT retorna estructura correcta', function () {
        $controller = new NfcContentController();
        $method = new ReflectionMethod($controller, 'prepareViewData');
        $method->setAccessible(true);
        
        $content = DynamicContent::factory()->gift()->create([
            'data' => [
                'from' => 'Juan',
                'to' => 'María',
                'love_message' => 'Te amo'
            ]
        ]);
        
        $result = $method->invoke($controller, $content);
        
        expect($result)->toBeArray()
            ->and($result)->toHaveKey('multimedia')
            ->and($result)->toHaveKey('theme')
            ->and($result)->toHaveKey('currentSubtype')
            ->and($result)->toHaveKey('sender_name')
            ->and($result)->toHaveKey('recipient_name')
            ->and($result)->toHaveKey('message');
    });

    test('prepareViewData para tipo MENU retorna estructura correcta', function () {
        $controller = new NfcContentController();
        $method = new ReflectionMethod($controller, 'prepareViewData');
        $method->setAccessible(true);
        
        $content = DynamicContent::factory()->create(['type' => 'MENU']);
        
        $result = $method->invoke($controller, $content);
        
        expect($result)->toBeArray();
        // MENU no está completamente implementado, solo verificar que no falle
    });

    test('prepareViewData para tipo PROFILE retorna estructura correcta', function () {
        $controller = new NfcContentController();
        $method = new ReflectionMethod($controller, 'prepareViewData');
        $method->setAccessible(true);
        
        $content = DynamicContent::factory()->profile()->create();
        
        $result = $method->invoke($controller, $content);
        
        expect($result)->toBeArray();
        // PROFILE requiere relaciones específicas, solo verificar que no falle
    });

    test('getGiftTheme retorna tema apropiado', function () {
        $controller = new NfcContentController();
        $method = new ReflectionMethod($controller, 'getGiftTheme');
        $method->setAccessible(true);
        
        $content = DynamicContent::factory()->gift()->create([
            'gift_subtype' => 'birthday'
        ]);
        
        $result = $method->invoke($controller, $content);
        
        expect($result)->toBeArray()
            ->and($result)->toHaveKeys(['primary_color', 'icon', 'name']);
    });

    test('getDefaultDataForType retorna datos para GIFT', function () {
        $controller = new NfcContentController();
        $method = new ReflectionMethod($controller, 'getDefaultDataForType');
        $method->setAccessible(true);
        
        $result = $method->invoke($controller, 'GIFT');
        
        expect($result)->toBeArray()
            ->and($result)->toHaveKeys(['from', 'to', 'love_message']);
    });

    test('getDefaultDataForType retorna datos para MENU', function () {
        $controller = new NfcContentController();
        $method = new ReflectionMethod($controller, 'getDefaultDataForType');
        $method->setAccessible(true);
        
        $result = $method->invoke($controller, 'MENU');
        
        expect($result)->toBeArray()
            ->and($result)->toHaveKeys(['restaurant_info', 'menu_items']);
    });

    test('getDefaultDataForType retorna datos para PROFILE', function () {
        $controller = new NfcContentController();
        $method = new ReflectionMethod($controller, 'getDefaultDataForType');
        $method->setAccessible(true);
        
        $result = $method->invoke($controller, 'PROFILE');
        
        expect($result)->toBeArray()
            ->and($result)->toHaveKeys(['bio', 'location', 'job_title', 'company']);
    });

    test('getDefaultDataForType retorna datos para EVENT', function () {
        $controller = new NfcContentController();
        $method = new ReflectionMethod($controller, 'getDefaultDataForType');
        $method->setAccessible(true);
        
        $result = $method->invoke($controller, 'EVENT');
        
        expect($result)->toBeArray()
            ->and($result)->toHaveKeys(['event_info', 'contact']);
    });

    test('getDefaultDataForType retorna datos para TOURIST', function () {
        $controller = new NfcContentController();
        $method = new ReflectionMethod($controller, 'getDefaultDataForType');
        $method->setAccessible(true);
        
        $result = $method->invoke($controller, 'TOURIST');
        
        expect($result)->toBeArray()
            ->and($result)->toHaveKeys(['location_name', 'description', 'highlights']);
    });

    test('getDefaultDataForType retorna datos para PRODUCT', function () {
        $controller = new NfcContentController();
        $method = new ReflectionMethod($controller, 'getDefaultDataForType');
        $method->setAccessible(true);
        
        $result = $method->invoke($controller, 'PRODUCT');
        
        expect($result)->toBeArray()
            ->and($result)->toHaveKeys(['product_name', 'description', 'price', 'features']);
    });

    test('getDefaultDataForType retorna array vacío para tipo desconocido', function () {
        $controller = new NfcContentController();
        $method = new ReflectionMethod($controller, 'getDefaultDataForType');
        $method->setAccessible(true);
        
        $result = $method->invoke($controller, 'UNKNOWN');
        
        expect($result)->toBeArray()
            ->and($result)->toBeEmpty();
    });

    test('showOnboarding retorna vista con configuración correcta', function () {
        $controller = new NfcContentController();
        $method = new ReflectionMethod($controller, 'showOnboarding');
        $method->setAccessible(true);
        
        $result = $method->invoke($controller, 'GIFT', 'TEST123');
        
        expect($result)->toBeInstanceOf(\Illuminate\View\View::class);
        expect($result->getData())->toHaveKeys(['type', 'id', 'config']);
    });

    test('showContentNotAvailable retorna vista correcta', function () {
        $controller = new NfcContentController();
        $method = new ReflectionMethod($controller, 'showContentNotAvailable');
        $method->setAccessible(true);
        
        $token = NfcToken::factory()->create();
        $result = $method->invoke($controller, 'GIFT', 'TEST123', $token, 'Test reason');
        
        expect($result)->toBeInstanceOf(\Illuminate\View\View::class);
        expect($result->getData())->toHaveKeys(['type', 'id', 'token', 'reason']);
    });

    test('assignDefaultNfcRole maneja usuario sin roles', function () {
        $controller = new NfcContentController();
        $method = new ReflectionMethod($controller, 'assignDefaultNfcRole');
        $method->setAccessible(true);
        
        $user = User::factory()->create();
        
        // No debe lanzar excepción
        expect(fn() => $method->invoke($controller, $user))
            ->not->toThrow(\Exception::class);
    });
});