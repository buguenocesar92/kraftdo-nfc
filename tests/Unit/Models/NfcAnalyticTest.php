<?php

use App\Models\DynamicContent;
use App\Models\NfcAnalytic;
use App\Models\NfcToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('NfcAnalytic Model', function () {
    test('puede crear una analítica', function () {
        $analytic = NfcAnalytic::factory()->create();

        expect($analytic)->toBeInstanceOf(NfcAnalytic::class)
            ->and($analytic->content_id)->toBeString()
            ->and($analytic->ip_address)->toBeString();
    });

    test('pertenece a un token NFC', function () {
        $token = NfcToken::factory()->create();
        $analytic = NfcAnalytic::factory()->create(['nfc_token_id' => $token->id]);

        expect($analytic->nfcToken)->toBeInstanceOf(NfcToken::class)
            ->and($analytic->nfcToken->id)->toBe($token->id);
    });

    test('registra acceso correctamente', function () {
        $token = NfcToken::factory()->create();
        $content = DynamicContent::factory()->create(['nfc_token_id' => $token->id]);

        // Mock request
        $request = new Request([], [], [], [], [], [
            'REMOTE_ADDR' => '192.168.1.1',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 Test Browser',
        ]);
        app()->instance('request', $request);

        NfcAnalytic::recordAccess($content->content_id, $content->type, $token->id);

        $analytic = NfcAnalytic::latest()->first();
        expect($analytic->content_id)->toBe($content->content_id)
            ->and($analytic->content_type)->toBe($content->type)
            ->and($analytic->nfc_token_id)->toBe($token->id)
            ->and($analytic->ip_address)->toBe('192.168.1.1');
    });

    test('detecta visitas únicas correctamente', function () {
        $contentId = 'unique-test-content-id';

        // Mock request con misma IP
        $request = new Request([], [], [], [], [], [
            'REMOTE_ADDR' => '192.168.1.1',
            'HTTP_USER_AGENT' => 'Mozilla/5.0',
        ]);
        app()->instance('request', $request);

        // Primera visita - debe ser única
        NfcAnalytic::recordAccess($contentId, 'GIFT');
        $firstVisit = NfcAnalytic::where('content_id', $contentId)->orderBy('id')->first();
        expect($firstVisit->is_unique_visit)->toBeTrue();

        // Segunda visita desde misma IP - no debe ser única
        NfcAnalytic::recordAccess($contentId, 'GIFT');
        $secondVisit = NfcAnalytic::where('content_id', $contentId)->orderBy('id', 'desc')->first();
        expect($secondVisit->is_unique_visit)->toBeFalse();
    });

    test('detecta tipo de dispositivo', function () {
        $mobileUA = 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X)';
        $tabletUA = 'Mozilla/5.0 (iPad; CPU OS 14_0 like Mac OS X)';
        $desktopUA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)';

        // Test móvil
        $request1 = new Request([], [], [], [], [], ['HTTP_USER_AGENT' => $mobileUA]);
        app()->instance('request', $request1);
        NfcAnalytic::recordAccess('mobile-test-unique', 'GIFT');
        $mobileRecord = NfcAnalytic::where('content_id', 'mobile-test-unique')->first();
        expect($mobileRecord->device_type)->toBe('mobile');

        // Test tablet
        $request2 = new Request([], [], [], [], [], ['HTTP_USER_AGENT' => $tabletUA]);
        app()->instance('request', $request2);
        NfcAnalytic::recordAccess('tablet-test-unique', 'GIFT');
        $tabletRecord = NfcAnalytic::where('content_id', 'tablet-test-unique')->first();
        expect($tabletRecord->device_type)->toBe('tablet');

        // Test desktop
        $request3 = new Request([], [], [], [], [], ['HTTP_USER_AGENT' => $desktopUA]);
        app()->instance('request', $request3);
        NfcAnalytic::recordAccess('desktop-test-unique', 'GIFT');
        $desktopRecord = NfcAnalytic::where('content_id', 'desktop-test-unique')->first();
        expect($desktopRecord->device_type)->toBe('desktop');
    });

    test('detecta navegador correctamente', function () {
        $chromeUA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/91.0';
        $firefoxUA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0';

        $request1 = new Request([], [], [], [], [], ['HTTP_USER_AGENT' => $chromeUA]);
        app()->instance('request', $request1);
        NfcAnalytic::recordAccess('chrome-test-unique', 'GIFT');
        $chromeRecord = NfcAnalytic::where('content_id', 'chrome-test-unique')->first();
        expect($chromeRecord->browser)->toBe('Chrome');

        $request2 = new Request([], [], [], [], [], ['HTTP_USER_AGENT' => $firefoxUA]);
        app()->instance('request', $request2);
        NfcAnalytic::recordAccess('firefox-test-unique', 'GIFT');
        $firefoxRecord = NfcAnalytic::where('content_id', 'firefox-test-unique')->first();
        expect($firefoxRecord->browser)->toBe('Firefox');
    });

    test('obtiene estadísticas para contenido específico', function () {
        $contentId = 'test-content-123';

        // Crear analíticas de prueba
        NfcAnalytic::factory()->count(5)->create([
            'content_id' => $contentId,
            'content_type' => 'GIFT',
        ]);

        NfcAnalytic::factory()->count(3)->create([
            'content_id' => 'other-content',
            'content_type' => 'GIFT',
        ]);

        $stats = NfcAnalytic::getContentStats($contentId);

        expect($stats['total_views'])->toBe(5)
            ->and($stats['unique_views'])->toBeGreaterThanOrEqual(0)
            ->and($stats['device_breakdown'])->toBeInstanceOf(\Illuminate\Database\Eloquent\Collection::class);
    });

    test('obtiene estadísticas globales', function () {
        // Crear analíticas de diferentes tipos
        NfcAnalytic::factory()->count(10)->create(['content_type' => 'GIFT']);
        NfcAnalytic::factory()->count(5)->create(['content_type' => 'PROFILE']);

        $stats = NfcAnalytic::getGlobalStats();

        expect($stats['total_scans'])->toBe(15)
            ->and($stats['content_type_breakdown'])->toHaveKey('GIFT')
            ->and($stats['content_type_breakdown'])->toHaveKey('PROFILE')
            ->and($stats['content_type_breakdown']['GIFT'])->toBe(10)
            ->and($stats['content_type_breakdown']['PROFILE'])->toBe(5);
    });

    test('scopes funcionan correctamente', function () {
        // Crear analíticas de hoy y de ayer
        NfcAnalytic::factory()->count(3)->create([
            'accessed_at' => now(),
        ]);

        NfcAnalytic::factory()->count(2)->create([
            'accessed_at' => now()->subDay(),
        ]);

        $todayCount = NfcAnalytic::today()->count();
        $thisWeekCount = NfcAnalytic::thisWeek()->count();

        expect($todayCount)->toBe(3)
            ->and($thisWeekCount)->toBeGreaterThanOrEqual(3); // Al menos los de hoy
    });

    test('actualiza last_used_at del token al registrar acceso', function () {
        $token = NfcToken::factory()->create(['last_used_at' => null]);
        $content = DynamicContent::factory()->create(['nfc_token_id' => $token->id]);

        NfcAnalytic::recordAccess($content->content_id, $content->type, $token->id);

        $token->refresh();
        expect($token->last_used_at)->not->toBeNull();
    });

    test('incrementa contador de vistas del token', function () {
        $token = NfcToken::factory()->create(['total_investment_views' => 5]);
        $content = DynamicContent::factory()->create(['nfc_token_id' => $token->id]);

        NfcAnalytic::recordAccess($content->content_id, $content->type, $token->id);

        $token->refresh();
        expect($token->total_investment_views)->toBe(6);
    });
});
