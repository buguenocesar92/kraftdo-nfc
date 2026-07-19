<?php

use App\Models\NfcToken;
use App\Models\User;
use App\Models\DynamicContent;
use App\Services\ViewingLockService;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);

    $this->user = User::factory()->create();
    $this->user->assignRole('NFC');

    $this->token = NfcToken::factory()->create([
        'user_id' => $this->user->id,
        'is_active' => true,
        'content_type' => 'GIFT', // Solo GIFT tiene bloqueo
    ]);

    // Crear contenido dinámico para que pase la validación de TokenController::show()
    DynamicContent::factory()->create([
        'nfc_token_id' => $this->token->id,
        'user_id' => $this->user->id,
        'type' => 'GIFT',
        'is_active' => true,
        'status' => 'published',
    ]);

    /** @var ViewingLockService $lock */
    $lock = app(ViewingLockService::class);
    $lock->clearLock($this->token->token_id);
});

// ─────────────────────────────────────────────
// Caso 1: Scan físico (sin Referer)
// ─────────────────────────────────────────────

test('scan fisico siempre pasa y activa el lock', function () {
    // Sin Referer = scan físico
    $response = $this->getJson("/api/tokens/{$this->token->token_id}");

    $response->assertStatus(200);

    // La bandera debe estar activa en cache
    expect(app(ViewingLockService::class)->hasLock($this->token->token_id))->toBeTrue();
});

test('scan fisico pasa aunque haya un lock activo previo', function () {
    // Simular que alguien ya está viendo
    app(ViewingLockService::class)->setLock($this->token->token_id);

    // Un nuevo scan físico siempre pasa y renueva el lock
    $response = $this->getJson("/api/tokens/{$this->token->token_id}");

    $response->assertStatus(200);
    expect(app(ViewingLockService::class)->hasLock($this->token->token_id))->toBeTrue();
});

// ─────────────────────────────────────────────
// Caso 2: Link compartido (con Referer) + lock activo → 423
// ─────────────────────────────────────────────

test('link compartido retorna 423 si hay lock activo', function () {
    // Activar lock (como si alguien estuviera viendo físicamente)
    app(ViewingLockService::class)->setLock($this->token->token_id);

    // Request con Referer = viene de WhatsApp, web, etc.
    // Importante: El TokenController::show() verifica el lock ANTES de cargar el contenido si cachedData existe
    $response = $this->withHeaders(['Referer' => 'https://web.whatsapp.com'])
        ->getJson("/api/tokens/{$this->token->token_id}");

    $response->assertStatus(423)
        ->assertJson([
            'message' => 'Este regalo está siendo visualizado en este momento. Inténtalo en unos segundos.',
            'status' => 423,
        ]);
});

// ─────────────────────────────────────────────
// Caso 3: Link compartido sin lock → pasa normal
// ─────────────────────────────────────────────

test('link compartido pasa normal si no hay lock activo', function () {
    // Sin lock activo
    expect(app(ViewingLockService::class)->hasLock($this->token->token_id))->toBeFalse();

    $response = $this->withHeaders(['Referer' => 'https://web.whatsapp.com'])
        ->getJson("/api/tokens/{$this->token->token_id}");

    $response->assertStatus(200);
});

// ─────────────────────────────────────────────
// Heartbeat
// ─────────────────────────────────────────────

test('heartbeat renueva el lock si existe y retorna renewed true', function () {
    app(ViewingLockService::class)->setLock($this->token->token_id);

    $response = $this->putJson("/api/tokens/{$this->token->token_id}/heartbeat");

    $response->assertStatus(200)
        ->assertJson([
            'renewed' => true,
            'message' => 'Visualización renovada.',
        ]);

    expect(app(ViewingLockService::class)->hasLock($this->token->token_id))->toBeTrue();
});

test('heartbeat retorna renewed false si no hay lock activo', function () {
    $response = $this->putJson("/api/tokens/{$this->token->token_id}/heartbeat");

    $response->assertStatus(200)
        ->assertJson([
            'renewed' => false,
            'message' => 'No hay visualización activa para este regalo.',
            'retry_after' => null,
        ]);
});

// ─────────────────────────────────────────────
// Caso 4: No bloquear otros tipos (PROFILE)
// ─────────────────────────────────────────────

test('no bloquea tokens de tipo PROFILE aunque haya lock manual', function () {
    $profileToken = NfcToken::factory()->create([
        'user_id' => $this->user->id,
        'is_active' => true,
        'content_type' => 'PROFILE',
    ]);

    // Crear contenido para PROFILE también
    DynamicContent::factory()->create([
        'nfc_token_id' => $profileToken->id,
        'user_id' => $this->user->id,
        'type' => 'PROFILE',
        'is_active' => true,
        'status' => 'published',
    ]);

    // Intentar activar lock manual
    app(ViewingLockService::class)->setLock($profileToken->token_id);

    // Link compartido debe pasar normal (200) porque no es GIFT
    $response = $this->withHeaders(['Referer' => 'https://web.whatsapp.com'])
        ->getJson("/api/tokens/{$profileToken->token_id}");

    $response->assertStatus(200);
});
