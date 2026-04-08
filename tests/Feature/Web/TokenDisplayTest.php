<?php

use App\Livewire\TokenDisplay;
use App\Models\ContentGift;
use App\Models\ContentProfile;
use App\Models\DynamicContent;
use App\Models\NfcToken;
use App\Models\User;
use App\Services\ViewingLockService;
use Livewire\Livewire;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);

    $this->user = User::factory()->create();
    $this->user->assignRole('NFC');
});

// ─────────────────────────────────────────────────────────────
// Helper para crear un token con DynamicContent y ContentXxx
// ─────────────────────────────────────────────────────────────

function makeProfileToken(User $user): NfcToken
{
    $token   = NfcToken::factory()->create(['user_id' => $user->id, 'content_type' => 'PROFILE', 'is_active' => true]);
    $dynamic = DynamicContent::factory()->create(['nfc_token_id' => $token->id, 'type' => 'PROFILE']);
    ContentProfile::factory()->create(['dynamic_content_id' => $dynamic->id, 'name' => 'Ana Pérez', 'profession' => 'Ingeniera']);
    return $token;
}

function makeGiftToken(User $user): NfcToken
{
    $token   = NfcToken::factory()->create(['user_id' => $user->id, 'content_type' => 'GIFT', 'is_active' => true]);
    $dynamic = DynamicContent::factory()->create(['nfc_token_id' => $token->id, 'type' => 'GIFT']);
    ContentGift::factory()->create(['dynamic_content_id' => $dynamic->id, 'recipient_name' => 'Carlos', 'message' => 'Con cariño']);
    return $token;
}

// ─────────────────────────────────────────────────────────────
// Ruta web GET /token/{tokenId}
// ─────────────────────────────────────────────────────────────

test('GET /token/{id} devuelve 200 y renderiza el componente Livewire', function () {
    $token = makeProfileToken($this->user);

    $response = $this->get("/token/{$token->token_id}");

    $response->assertStatus(200);
    $response->assertSeeLivewire(TokenDisplay::class);
});

test('GET /token/{id} con token inexistente muestra mensaje de no encontrado', function () {
    $response = $this->get('/token/uuid-que-no-existe-123');

    $response->assertStatus(200);
    $response->assertSee('Token no encontrado');
});

// ─────────────────────────────────────────────────────────────
// Componente Livewire — PROFILE
// ─────────────────────────────────────────────────────────────

test('TokenDisplay renderiza la vista de perfil con datos del ContentProfile', function () {
    $token = makeProfileToken($this->user);

    Livewire::test(TokenDisplay::class, ['tokenId' => $token->token_id])
        ->assertSee('Ana Pérez')
        ->assertSee('Ingeniera')
        ->assertSet('notFound', false)
        ->assertSet('blocked', false);
});

// ─────────────────────────────────────────────────────────────
// Componente Livewire — GIFT
// ─────────────────────────────────────────────────────────────

test('TokenDisplay renderiza la vista de regalo con datos del ContentGift', function () {
    $token = makeGiftToken($this->user);

    Livewire::test(TokenDisplay::class, ['tokenId' => $token->token_id])
        ->assertSee('Carlos')
        ->assertSee('Con cariño')
        ->assertSet('notFound', false)
        ->assertSet('blocked', false);
});

// ─────────────────────────────────────────────────────────────
// Viewing lock — link compartido bloqueado
// ─────────────────────────────────────────────────────────────

test('TokenDisplay muestra pantalla de bloqueado cuando hay lock activo y llega con Referer', function () {
    $token = makeProfileToken($this->user);

    // Activar lock (como si alguien lo estuviera viendo físicamente)
    app(ViewingLockService::class)->setLock($token->token_id);

    // Request con Referer = link compartido
    Livewire::withHeaders(['Referer' => 'https://web.whatsapp.com'])
        ->test(TokenDisplay::class, ['tokenId' => $token->token_id])
        ->assertSet('blocked', true)
        ->assertSee('Cuadro en uso');

    // Limpiar
    app(ViewingLockService::class)->clearLock($token->token_id);
});

test('TokenDisplay scan fisico (sin Referer) siempre pasa aunque haya lock activo', function () {
    $token = makeProfileToken($this->user);

    // Activar lock previo
    app(ViewingLockService::class)->setLock($token->token_id);

    // Sin Referer = scan físico
    Livewire::test(TokenDisplay::class, ['tokenId' => $token->token_id])
        ->assertSet('blocked', false)
        ->assertSee('Ana Pérez');

    app(ViewingLockService::class)->clearLock($token->token_id);
});

// ─────────────────────────────────────────────────────────────
// Heartbeat
// ─────────────────────────────────────────────────────────────

test('sendHeartbeat renueva el lock cuando el componente es un scan fisico', function () {
    $token = makeProfileToken($this->user);

    // Activar lock inicial
    app(ViewingLockService::class)->setLock($token->token_id);

    // Sin Referer → isPhysical = true
    Livewire::test(TokenDisplay::class, ['tokenId' => $token->token_id])
        ->assertSet('isPhysical', true)
        ->call('sendHeartbeat');

    // El lock sigue activo
    expect(app(ViewingLockService::class)->hasLock($token->token_id))->toBeTrue();

    app(ViewingLockService::class)->clearLock($token->token_id);
});
