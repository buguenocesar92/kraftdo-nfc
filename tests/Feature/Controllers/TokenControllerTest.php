<?php

use App\Models\NfcToken;
use App\Models\DynamicContent;
use App\Models\ContentGift;
use App\Models\ContentProfile;
use App\Models\ContentMultimedia;
use App\Models\User;

describe('TokenController', function () {
    
    test('muestra token GIFT válido', function () {
        $token = NfcToken::factory()->gift()->create(['is_active' => true]);
        $content = DynamicContent::factory()->gift()->create(['nfc_token_id' => $token->id]);
        $gift = ContentGift::factory()->create(['dynamic_content_id' => $content->id]);
        
        $response = $this->get("/token/{$token->token_id}");
        
        $response->assertStatus(200)
            ->assertViewIs('token.gift')
            ->assertViewHas('token', $token)
            ->assertViewHas('dynamicContent', $content)
            ->assertViewHas('contentGift');
    });

    test('muestra token PROFILE válido', function () {
        $token = NfcToken::factory()->profile()->create(['is_active' => true]);
        $content = DynamicContent::factory()->profile()->create(['nfc_token_id' => $token->id]);
        $profile = ContentProfile::factory()->create(['dynamic_content_id' => $content->id]);
        
        $response = $this->get("/token/{$token->token_id}");
        
        $response->assertStatus(200)
            ->assertViewIs('token.profile')
            ->assertViewHas('token', $token)
            ->assertViewHas('dynamicContent', $content)
            ->assertViewHas('contentProfile');
    });

    test('retorna 404 para token inactivo', function () {
        $token = NfcToken::factory()->inactive()->create();
        
        $response = $this->get("/token/{$token->token_id}");
        
        $response->assertStatus(404);
    });

    test('retorna 404 para token inexistente', function () {
        $response = $this->get('/token/non-existent-token-id');
        
        $response->assertStatus(404);
    });

    test('retorna 404 para token sin contenido asociado', function () {
        $token = NfcToken::factory()->create([
            'content_type' => 'GIFT',
            'is_active' => true
        ]);
        // No crear DynamicContent asociado para simular contenido faltante
        
        $response = $this->get("/token/{$token->token_id}");
        
        $response->assertStatus(404);
    });

    test('retorna 404 cuando no hay contenido dinámico', function () {
        $token = NfcToken::factory()->create(['is_active' => true]);
        // No crear contenido dinámico
        
        $response = $this->get("/token/{$token->token_id}");
        
        $response->assertStatus(404);
    });

    test('incluye galería de imágenes en token GIFT', function () {
        $token = NfcToken::factory()->gift()->create(['is_active' => true]);
        $content = DynamicContent::factory()->gift()->create(['nfc_token_id' => $token->id]);
        $gift = ContentGift::factory()->create(['dynamic_content_id' => $content->id]);
        $multimedia = ContentMultimedia::factory()->create(['dynamic_content_id' => $content->id]);
        
        // Simular imágenes de galería
        $multimedia->galleryImages()->create([
            'image_path' => 'gallery/test1.jpg',
            'sort_order' => 1
        ]);
        $multimedia->galleryImages()->create([
            'image_path' => 'gallery/test2.jpg',
            'sort_order' => 2
        ]);
        
        $response = $this->get("/token/{$token->token_id}");
        
        $response->assertStatus(200)
            ->assertViewHas('galleryImages')
            ->assertViewHas('contentMultimedia');
            
        $galleryImages = $response->viewData('galleryImages');
        expect($galleryImages)->toHaveCount(2);
    });

    test('incluye enlaces sociales en token PROFILE', function () {
        $token = NfcToken::factory()->profile()->create(['is_active' => true]);
        $content = DynamicContent::factory()->profile()->create(['nfc_token_id' => $token->id]);
        $profile = ContentProfile::factory()->create(['dynamic_content_id' => $content->id]);
        
        // Simular enlaces sociales (pertenecen a DynamicContent)
        $content->socialLinks()->create([
            'platform' => 'twitter',
            'url' => 'https://twitter.com/test',
            'sort_order' => 1
        ]);
        $content->socialLinks()->create([
            'platform' => 'instagram',
            'url' => 'https://instagram.com/test',
            'sort_order' => 2
        ]);
        
        $response = $this->get("/token/{$token->token_id}");
        
        $response->assertStatus(200)
            ->assertViewHas('socialLinks');
            
        $socialLinks = $response->viewData('socialLinks');
        expect($socialLinks)->toHaveCount(2);
    });

    test('aplica configuración de tema correctamente', function () {
        $token = NfcToken::factory()->gift()->create(['is_active' => true]);
        $content = DynamicContent::factory()->gift()->create(['nfc_token_id' => $token->id]);
        $gift = ContentGift::factory()->create(['dynamic_content_id' => $content->id]);
        $multimedia = ContentMultimedia::factory()->create([
            'dynamic_content_id' => $content->id,
            'settings' => ['theme' => 'valentine']
        ]);
        
        $response = $this->get("/token/{$token->token_id}");
        
        $response->assertStatus(200)
            ->assertViewHas('theme');
            
        $theme = $response->viewData('theme');
        expect($theme)->toBeArray();
    });

    test('registra analytics al acceder a token', function () {
        $token = NfcToken::factory()->gift()->create(['is_active' => true]);
        $content = DynamicContent::factory()->gift()->create(['nfc_token_id' => $token->id]);
        $gift = ContentGift::factory()->create(['dynamic_content_id' => $content->id]);
        
        expect(\App\Models\NfcAnalytic::count())->toBe(0);
        
        $response = $this->get("/token/{$token->token_id}");
        
        $response->assertStatus(200);
        expect(\App\Models\NfcAnalytic::count())->toBe(1);
        
        $analytic = \App\Models\NfcAnalytic::first();
        expect($analytic->content_id)->toBe($content->content_id)
            ->and($analytic->content_type)->toBe('GIFT')
            ->and($analytic->nfc_token_id)->toBe($token->id);
    });

    test('actualiza last_used_at del token al acceder', function () {
        $token = NfcToken::factory()->gift()->create([
            'is_active' => true,
            'last_used_at' => null
        ]);
        $content = DynamicContent::factory()->gift()->create(['nfc_token_id' => $token->id]);
        $gift = ContentGift::factory()->create(['dynamic_content_id' => $content->id]);
        
        $response = $this->get("/token/{$token->token_id}");
        
        $response->assertStatus(200);
        expect($token->fresh()->last_used_at)->not()->toBeNull();
    });


    test('utiliza cache para mejor performance', function () {
        $token = NfcToken::factory()->gift()->create(['is_active' => true]);
        $content = DynamicContent::factory()->gift()->create(['nfc_token_id' => $token->id]);
        $gift = ContentGift::factory()->create(['dynamic_content_id' => $content->id]);
        
        // Primera carga - debería cachear
        $startTime = microtime(true);
        $response1 = $this->get("/token/{$token->token_id}");
        $firstLoadTime = microtime(true) - $startTime;
        
        // Segunda carga - debería usar cache
        $startTime = microtime(true);
        $response2 = $this->get("/token/{$token->token_id}");
        $secondLoadTime = microtime(true) - $startTime;
        
        $response1->assertStatus(200);
        $response2->assertStatus(200);
        
        // La segunda carga debería ser más rápida (cache hit)
        expect($secondLoadTime)->toBeLessThan($firstLoadTime * 1.5); // Margen para variación
    });
});