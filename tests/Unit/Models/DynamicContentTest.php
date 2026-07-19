<?php

use App\Models\DynamicContent;
use App\Models\NfcToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('DynamicContent Model', function () {
    test('puede crear contenido dinámico', function () {
        $content = DynamicContent::factory()->create();

        expect($content)->toBeInstanceOf(DynamicContent::class)
            ->and($content->content_id)->toBeString()
            ->and($content->type)->toBeString()
            ->and($content->title)->toBeString();
    });

    test('pertenece a un usuario', function () {
        $user = User::factory()->create();
        $content = DynamicContent::factory()->create(['user_id' => $user->id]);

        expect($content->user)->toBeInstanceOf(User::class)
            ->and($content->user->id)->toBe($user->id);
    });

    test('pertenece a un token NFC', function () {
        $token = NfcToken::factory()->create();
        $content = DynamicContent::factory()->create(['nfc_token_id' => $token->id]);

        expect($content->nfcToken)->toBeInstanceOf(NfcToken::class)
            ->and($content->nfcToken->id)->toBe($token->id);
    });

    test('tiene métodos para relaciones específicas', function () {
        $giftContent = DynamicContent::factory()->gift()->create();
        $profileContent = DynamicContent::factory()->profile()->create();

        expect(method_exists($giftContent, 'gift'))->toBeTrue();
        expect(method_exists($profileContent, 'profile'))->toBeTrue();
    });

    test('tiene métodos para relaciones de contenido', function () {
        $content = DynamicContent::factory()->create();

        expect(method_exists($content, 'multimedia'))->toBeTrue();
        expect(method_exists($content, 'socialLinks'))->toBeTrue();
        expect(method_exists($content, 'skills'))->toBeTrue();
        expect(method_exists($content, 'business'))->toBeTrue();
    });

    test('verifica si está públicamente accesible', function () {
        $publicContent = DynamicContent::factory()->create([
            'status' => 'published',
            'is_active' => true,
        ]);

        $draftContent = DynamicContent::factory()->create([
            'status' => 'draft',
            'is_active' => true,
        ]);

        $inactiveContent = DynamicContent::factory()->create([
            'status' => 'published',
            'is_active' => false,
        ]);

        expect($publicContent->isPubliclyAccessible())->toBeTrue()
            ->and($draftContent->isPubliclyAccessible())->toBeFalse()
            ->and($inactiveContent->isPubliclyAccessible())->toBeFalse();
    });

    test('verifica si está en borrador', function () {
        $draftContent = DynamicContent::factory()->create(['status' => 'draft']);
        $publishedContent = DynamicContent::factory()->create(['status' => 'published']);

        expect($draftContent->isDraft())->toBeTrue()
            ->and($publishedContent->isDraft())->toBeFalse();
    });

    test('verifica si está publicado', function () {
        $publishedContent = DynamicContent::factory()->create(['status' => 'published']);
        $draftContent = DynamicContent::factory()->create(['status' => 'draft']);

        expect($publishedContent->isPublished())->toBeTrue()
            ->and($draftContent->isPublished())->toBeFalse();
    });

    test('verifica si está pausado', function () {
        $pausedContent = DynamicContent::factory()->create(['status' => 'paused']);
        $publishedContent = DynamicContent::factory()->create(['status' => 'published']);

        expect($pausedContent->isPaused())->toBeTrue()
            ->and($publishedContent->isPaused())->toBeFalse();
    });

    test('encuentra contenido activo por content_id', function () {
        $activeContent = DynamicContent::factory()->create([
            'content_id' => 'TEST-ACTIVE-123',
            'status' => 'published',
            'is_active' => true,
        ]);

        $inactiveContent = DynamicContent::factory()->create([
            'content_id' => 'TEST-INACTIVE-123',
            'status' => 'draft',
            'is_active' => false,
        ]);

        $found = DynamicContent::findActiveByContentId('TEST-ACTIVE-123');
        $notFound = DynamicContent::findActiveByContentId('TEST-INACTIVE-123');

        expect($found)->toBeInstanceOf(DynamicContent::class)
            ->and($found->id)->toBe($activeContent->id)
            ->and($notFound)->toBeNull();
    });

    test('obtiene subtipos de regalo', function () {
        $subtypes = DynamicContent::getGiftSubtypes();

        expect($subtypes)->toBeArray()
            ->and($subtypes)->toHaveKey('birthday')
            ->and($subtypes)->toHaveKey('anniversary')
            ->and($subtypes)->toHaveKey('general');

        expect($subtypes['birthday'])->toHaveKeys(['name', 'color', 'icon']);
    });

    test('obtiene constantes de tipos', function () {
        expect(DynamicContent::TYPE_GIFT)->toBe('GIFT')
            ->and(DynamicContent::TYPE_BUSINESS)->toBe('BUSINESS')
            ->and(DynamicContent::TYPE_PROFILE)->toBe('PROFILE')
            ->and(DynamicContent::TYPE_EVENT)->toBe('EVENT')
            ->and(DynamicContent::TYPE_PRODUCT)->toBe('PRODUCT');
    });

    test('scope activo funciona correctamente', function () {
        DynamicContent::factory()->count(3)->create(['is_active' => true]);
        DynamicContent::factory()->count(2)->create(['is_active' => false]);

        $activeCount = DynamicContent::where('is_active', true)->count();

        expect($activeCount)->toBe(3);
    });

    test('scope publicado funciona correctamente', function () {
        DynamicContent::factory()->count(4)->create(['status' => 'published']);
        DynamicContent::factory()->count(2)->create(['status' => 'draft']);
        DynamicContent::factory()->count(1)->create(['status' => 'paused']);

        $publishedCount = DynamicContent::where('status', 'published')->count();

        expect($publishedCount)->toBe(4);
    });

    test('scope por tipo funciona correctamente', function () {
        DynamicContent::factory()->count(3)->gift()->create();
        DynamicContent::factory()->count(2)->profile()->create();
        DynamicContent::factory()->count(1)->create(['type' => 'MENU']);

        $giftCount = DynamicContent::where('type', 'GIFT')->count();
        $profileCount = DynamicContent::where('type', 'PROFILE')->count();
        $menuCount = DynamicContent::where('type', 'MENU')->count();

        expect($giftCount)->toBe(3)
            ->and($profileCount)->toBe(2)
            ->and($menuCount)->toBe(1);
    });

    test('scope por usuario funciona correctamente', function () {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        DynamicContent::factory()->count(3)->create(['user_id' => $user1->id]);
        DynamicContent::factory()->count(2)->create(['user_id' => $user2->id]);

        $user1Count = DynamicContent::where('user_id', $user1->id)->count();
        $user2Count = DynamicContent::where('user_id', $user2->id)->count();

        expect($user1Count)->toBe(3)
            ->and($user2Count)->toBe(2);
    });

    test('maneja correctamente los casts de atributos', function () {
        $content = DynamicContent::factory()->create([
            'data' => ['test' => 'value'],
            'is_active' => '1',
            'published_at' => '2023-01-01 12:00:00',
            'post_publish_modifications' => ['modified' => true],
        ]);

        expect($content->data)->toBeArray()
            ->and($content->is_active)->toBeBool()
            ->and($content->published_at)->toBeInstanceOf(\Carbon\Carbon::class)
            ->and($content->post_publish_modifications)->toBeArray();
    });

    test('genera URL correcta para contenido público', function () {
        $content = DynamicContent::factory()->create([
            'content_id' => 'TEST-URL-123',
        ]);

        // Simular método getPublicUrl
        $url = url("/nfc/{$content->content_id}");

        expect($url)->toContain('TEST-URL-123');
    });

    test('genera título automático basado en tipo', function () {
        $giftContent = DynamicContent::factory()->gift()->create(['title' => 'Mi Regalo Especial']);
        $profileContent = DynamicContent::factory()->profile()->create(['title' => 'Mi Perfil Profesional']);

        expect($giftContent->title)->toContain('Regalo')
            ->and($profileContent->title)->toContain('Perfil');
    });
});
