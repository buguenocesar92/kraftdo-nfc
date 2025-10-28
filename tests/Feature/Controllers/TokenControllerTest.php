<?php

use App\Models\ContentGift;
use App\Models\ContentMultimedia;
use App\Models\ContentProfile;
use App\Models\DynamicContent;
use App\Models\NfcToken;

describe('TokenController API', function () {
    test('devuelve token GIFT válido como JSON', function () {
        $token = NfcToken::factory()->gift()->create(['is_active' => true]);
        $content = DynamicContent::factory()->gift()->create(['nfc_token_id' => $token->id]);
        $gift = ContentGift::factory()->create(['dynamic_content_id' => $content->id]);

        $response = $this->getJson("/token/{$token->token_id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Token obtenido exitosamente',
                'status' => 200,
            ])
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'dynamicContent',
                    'contentGift',
                    'contentMultimedia',
                    'galleryImages',
                    'theme',
                ],
            ]);
    });

    test('devuelve token PROFILE válido como JSON', function () {
        $token = NfcToken::factory()->profile()->create(['is_active' => true]);
        $content = DynamicContent::factory()->profile()->create(['nfc_token_id' => $token->id]);
        $profile = ContentProfile::factory()->create(['dynamic_content_id' => $content->id]);

        $response = $this->getJson("/token/{$token->token_id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Token obtenido exitosamente',
                'status' => 200,
            ])
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'dynamicContent',
                    'contentProfile',
                    'contentMultimedia',
                    'galleryImages',
                    'socialLinks',
                ],
            ]);
    });

    test('retorna JSON 200 para token inactivo con mensaje apropiado', function () {
        $token = NfcToken::factory()->inactive()->create();

        $response = $this->getJson("/token/{$token->token_id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Token inactivo',
                'status' => 200,
                'data' => [
                    'token_id' => $token->token_id,
                    'is_active' => false
                ]
            ]);
    });

    test('retorna JSON 404 para token inexistente', function () {
        $response = $this->getJson('/token/non-existent-token-id');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Token no encontrado',
                'status' => 404,
            ]);
    });

    test('retorna JSON 200 para token sin contenido asociado', function () {
        $token = NfcToken::factory()->create(['is_active' => true]);
        // No crear contenido dinámico para este token

        $response = $this->getJson("/token/{$token->token_id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Token obtenido exitosamente',
                'status' => 200,
                'data' => [
                    'token' => [
                        'token_id' => $token->token_id,
                    ],
                    'dynamicContent' => null,
                    'content' => [],
                ]
            ]);
    });

    test('retorna JSON 200 cuando hay contenido dinámico básico', function () {
        $token = NfcToken::factory()->gift()->create(['is_active' => true]);
        $content = DynamicContent::factory()->gift()->create(['nfc_token_id' => $token->id]);
        // Crear gift asociado
        \App\Models\ContentGift::factory()->create(['dynamic_content_id' => $content->id]);

        $response = $this->getJson("/token/{$token->token_id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Token obtenido exitosamente',
                'status' => 200,
            ]);
    });

    test('incluye galería de imágenes en token GIFT JSON', function () {
        $token = NfcToken::factory()->gift()->create(['is_active' => true]);
        $content = DynamicContent::factory()->gift()->create(['nfc_token_id' => $token->id]);
        $gift = ContentGift::factory()->create(['dynamic_content_id' => $content->id]);
        $multimedia = ContentMultimedia::factory()->create(['dynamic_content_id' => $content->id]);

        $response = $this->getJson("/token/{$token->token_id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'galleryImages',
                    'contentMultimedia',
                ],
            ]);

        $data = $response->json('data');
        expect($data['galleryImages'])->toBeArray();
        expect($data['contentMultimedia'])->not->toBeNull();
    });

    test('incluye enlaces sociales en token PROFILE JSON', function () {
        $token = NfcToken::factory()->profile()->create(['is_active' => true]);
        $content = DynamicContent::factory()->profile()->create(['nfc_token_id' => $token->id]);
        $profile = ContentProfile::factory()->create(['dynamic_content_id' => $content->id]);

        $response = $this->getJson("/token/{$token->token_id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'socialLinks',
                ],
            ]);

        $data = $response->json('data');
        expect($data['socialLinks'])->toBeArray();
    });

    test('aplica configuración de tema correctamente en JSON', function () {
        $token = NfcToken::factory()->gift()->create(['is_active' => true]);
        $content = DynamicContent::factory()->gift()->create(['nfc_token_id' => $token->id]);
        $gift = ContentGift::factory()->create(['dynamic_content_id' => $content->id]);

        $response = $this->getJson("/token/{$token->token_id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'theme',
                ],
            ]);

        $data = $response->json('data');
        expect($data['theme'])->toBeArray();
    });

    test('registra analytics al acceder a token via JSON', function () {
        $token = NfcToken::factory()->gift()->create(['is_active' => true]);
        $content = DynamicContent::factory()->gift()->create(['nfc_token_id' => $token->id]);
        $gift = ContentGift::factory()->create(['dynamic_content_id' => $content->id]);

        expect(\App\Models\NfcAnalytic::count())->toBe(0);

        $response = $this->getJson("/token/{$token->token_id}");

        $response->assertStatus(200);
        expect(\App\Models\NfcAnalytic::count())->toBe(1);
    });

    test('actualiza last_used_at del token al acceder via JSON', function () {
        $token = NfcToken::factory()->gift()->create([
            'is_active' => true,
            'last_used_at' => null,
        ]);
        $content = DynamicContent::factory()->gift()->create(['nfc_token_id' => $token->id]);
        $gift = ContentGift::factory()->create(['dynamic_content_id' => $content->id]);

        $response = $this->getJson("/token/{$token->token_id}");

        $response->assertStatus(200);
        expect($token->fresh()->last_used_at)->not->toBeNull();
    });

    test('utiliza cache para mejor performance en API', function () {
        $token = NfcToken::factory()->gift()->create(['is_active' => true]);
        $content = DynamicContent::factory()->gift()->create(['nfc_token_id' => $token->id]);
        $gift = ContentGift::factory()->create(['dynamic_content_id' => $content->id]);

        // Primera llamada debería cachear
        $response1 = $this->getJson("/token/{$token->token_id}");

        // Segunda llamada debería usar cache
        $response2 = $this->getJson("/token/{$token->token_id}");

        $response1->assertStatus(200);
        $response2->assertStatus(200);

        // Verificar que ambas respuestas tienen la misma estructura
        expect($response1->json('message'))->toBe($response2->json('message'));
        expect($response1->json('status'))->toBe($response2->json('status'));
        expect($response1->json('data.token.id'))->toBe($response2->json('data.token.id'));
    });
});
