<?php

use App\Models\NfcToken;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);

    $this->user = User::factory()->create();
    $this->user->assignRole('NFC');

    $this->token = NfcToken::factory()->create([
        'user_id' => $this->user->id,
        'is_active' => true,
    ]);
});

/**
 * Sobrescribe el limiter nfc-token-scan con un limite bajo para testing.
 * Reutiliza el mismo callback de respuesta 429 que el original.
 */
function setLowRateLimit(int $maxPerHour = 1): void
{
    RateLimiter::for('nfc-token-scan', function (Request $request) use ($maxPerHour) {
        $tokenId = $request->route('tokenId') ?? 'unknown';

        return Limit::perHour($maxPerHour)
            ->by($tokenId)
            ->response(function () {
                return response()->json([
                    'message' => 'Has realizado demasiadas solicitudes para este token. Intenta nuevamente en una hora.',
                    'status' => 429,
                ], 429);
            });
    });
}

test('permite requests normales bajo el limite', function () {
    $response = $this->getJson("/api/tokens/{$this->token->token_id}");

    $response->assertStatus(200);
});

test('bloquea con 429 al superar el limite por hora', function () {
    setLowRateLimit(maxPerHour: 1);

    $tokenId = $this->token->token_id;

    // Primera solicitud: debe pasar
    $this->getJson("/api/tokens/{$tokenId}")->assertStatus(200);

    // Segunda solicitud: supera el limite → 429
    $this->getJson("/api/tokens/{$tokenId}")
        ->assertStatus(429)
        ->assertJson([
            'message' => 'Has realizado demasiadas solicitudes para este token. Intenta nuevamente en una hora.',
            'status' => 429,
        ]);
});

test('el rate limit es por UUID, no global', function () {
    setLowRateLimit(maxPerHour: 1);

    $otherToken = NfcToken::factory()->create([
        'user_id' => $this->user->id,
        'is_active' => true,
    ]);

    // Agotar el limite del primer token
    $this->getJson("/api/tokens/{$this->token->token_id}")->assertStatus(200);
    $this->getJson("/api/tokens/{$this->token->token_id}")->assertStatus(429);

    // El segundo token NO debe estar bloqueado
    $this->getJson("/api/tokens/{$otherToken->token_id}")->assertStatus(200);
});

test('la respuesta 429 tiene el mensaje en espanol', function () {
    setLowRateLimit(maxPerHour: 1);

    $tokenId = $this->token->token_id;

    $this->getJson("/api/tokens/{$tokenId}")->assertStatus(200);

    $this->getJson("/api/tokens/{$tokenId}")
        ->assertStatus(429)
        ->assertJsonPath('message', 'Has realizado demasiadas solicitudes para este token. Intenta nuevamente en una hora.')
        ->assertJsonPath('status', 429);
});
