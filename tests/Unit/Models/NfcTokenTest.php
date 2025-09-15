<?php

use Tests\TestCase;
use App\Models\NfcToken;
use App\Models\User;
use App\Models\DynamicContent;
use App\Models\NfcAnalytic;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

describe('NfcToken Model', function () {
    
    test('puede crear un token NFC', function () {
        $token = NfcToken::factory()->create();
        
        expect($token)->toBeInstanceOf(NfcToken::class)
            ->and($token->token_id)->toBeString()
            ->and($token->is_active)->toBeTrue();
    });

    test('genera token_id único automáticamente', function () {
        $token = new NfcToken([
            'name' => 'Test Token',
            'content_type' => 'GIFT',
            'customization_plan' => 'BASIC',
            'purchase_price' => 100.00,
            'cost_per_view' => 0.01,
        ]);
        
        $token->save();
        
        expect($token->token_id)->toBeString()
            ->and($token->token_id)->toHaveLength(36); // UUID length
    });

    test('pertenece a un usuario', function () {
        $user = User::factory()->create();
        $token = NfcToken::factory()->create(['user_id' => $user->id]);
        
        expect($token->user)->toBeInstanceOf(User::class)
            ->and($token->user->id)->toBe($user->id);
    });

    test('tiene contenido dinámico asociado', function () {
        $token = NfcToken::factory()->create();
        $content = DynamicContent::factory()->create(['nfc_token_id' => $token->id]);
        
        expect($token->dynamicContent)->toBeInstanceOf(DynamicContent::class)
            ->and($token->dynamicContent->id)->toBe($content->id);
    });

    test('puede tener múltiples analytics', function () {
        $token = NfcToken::factory()->create();
        NfcAnalytic::factory()->count(3)->create(['nfc_token_id' => $token->id]);
        
        expect($token->analytics)->toHaveCount(3);
    });

    test('actualiza último uso correctamente', function () {
        $token = NfcToken::factory()->create(['last_used_at' => null]);
        
        $token->updateLastUsed();
        
        expect($token->last_used_at)->not()->toBeNull()
            ->and($token->last_used_at)->toBeInstanceOf(Carbon\Carbon::class);
    });

    test('crea o obtiene contenido dinámico', function () {
        $token = NfcToken::factory()->create();
        
        // Primera llamada debe crear el contenido
        $content1 = $token->getOrCreateContent();
        expect($content1)->toBeInstanceOf(DynamicContent::class);
        
        // Segunda llamada debe retornar el mismo contenido
        $content2 = $token->getOrCreateContent();
        expect($content2->id)->toBe($content1->id);
    });

    test('verifica si tiene contenido', function () {
        $tokenWithContent = NfcToken::factory()->create();
        DynamicContent::factory()->create(['nfc_token_id' => $tokenWithContent->id]);
        
        $tokenWithoutContent = NfcToken::factory()->create();
        
        expect($tokenWithContent->hasContent())->toBeTrue()
            ->and($tokenWithoutContent->hasContent())->toBeFalse();
    });

    test('verifica si el contenido está listo', function () {
        $token = NfcToken::factory()->create();
        $content = DynamicContent::factory()->published()->create(['nfc_token_id' => $token->id]);
        
        expect($token->isContentReady())->toBeTrue();
        
        $content->update(['status' => 'draft']);
        $token->refresh();
        
        expect($token->isContentReady())->toBeFalse();
    });

    test('verifica si está asignado a usuario', function () {
        $user = User::factory()->create();
        $assignedToken = NfcToken::factory()->create(['user_id' => $user->id]);
        $unassignedToken = NfcToken::factory()->withoutUser()->create();
        
        expect($assignedToken->isAssigned())->toBeTrue()
            ->and($unassignedToken->isAssigned())->toBeFalse();
    });

    test('incrementa contador de vistas', function () {
        $token = NfcToken::factory()->create(['total_investment_views' => 10]);
        
        $token->incrementViews();
        
        expect($token->total_investment_views)->toBe(11);
    });

    test('calcula costo por vista correctamente', function () {
        $token = NfcToken::factory()->create([
            'purchase_price' => 100.0,
            'total_investment_views' => 50
        ]);
        
        $costPerView = $token->calculateCostPerView();
        
        expect($costPerView)->toBe(2.0); // 100 / 50 = 2.0
    });

    test('obtiene ROI y métricas financieras', function () {
        $token = NfcToken::factory()->create([
            'purchase_price' => 100.0,
            'total_investment_views' => 100,
            'cost_per_view' => 0.5
        ]);
        
        $roi = $token->getROIMetrics();
        
        expect($roi)->toBeArray()
            ->and($roi['cost_per_view'])->toBe(1.0) // 100 / 100 = 1.0
            ->and($roi['total_investment'])->toBe(100.0)
            ->and($roi['total_views'])->toBe(100);
    });

    test('obtiene planes de personalización', function () {
        $plans = NfcToken::getCustomizationPlans();
        
        expect($plans)->toBeArray()
            ->and($plans)->toHaveKey('BASIC')
            ->and($plans)->toHaveKey('STANDARD')
            ->and($plans)->toHaveKey('PREMIUM')
            ->and($plans)->toHaveKey('DELUXE');
    });

    test('obtiene plan de personalización actual', function () {
        $token = NfcToken::factory()->create(['customization_plan' => 'PREMIUM']);
        
        $plan = $token->getCurrentPlan();
        
        expect($plan)->toBeArray()
            ->and($plan['name'])->toBe('Premium')
            ->and($plan['features'])->toBeArray();
    });

    test('verifica características disponibles', function () {
        $basicToken = NfcToken::factory()->create(['customization_plan' => 'BASIC']);
        $premiumToken = NfcToken::factory()->create(['customization_plan' => 'PREMIUM']);
        
        expect($basicToken->hasFeature('social_links'))->toBeFalse()
            ->and($premiumToken->hasFeature('social_links'))->toBeTrue();
    });

    test('busca token activo por ID', function () {
        $activeToken = NfcToken::factory()->create(['is_active' => true]);
        $inactiveToken = NfcToken::factory()->inactive()->create();
        
        $found = NfcToken::findActiveByTokenId($activeToken->token_id);
        $notFound = NfcToken::findActiveByTokenId($inactiveToken->token_id);
        
        expect($found)->toBeInstanceOf(NfcToken::class)
            ->and($found->id)->toBe($activeToken->id)
            ->and($notFound)->toBeNull();
    });

    test('scopes funcionan correctamente', function () {
        $activeToken = NfcToken::factory()->create(['is_active' => true]);
        $inactiveToken = NfcToken::factory()->inactive()->create();
        $giftToken = NfcToken::factory()->gift()->create();
        
        $activeTokensIds = NfcToken::active()->pluck('id')->toArray();
        $giftTokensIds = NfcToken::ofType('GIFT')->pluck('id')->toArray();
        
        expect($activeTokensIds)->toContain($activeToken->id)
            ->and($activeTokensIds)->not->toContain($inactiveToken->id)
            ->and($giftTokensIds)->toContain($giftToken->id);
    });

    test('genera token_id único', function () {
        $token1 = NfcToken::generateUniqueTokenId();
        $token2 = NfcToken::generateUniqueTokenId();
        
        expect($token1)->toBeString()
            ->and($token2)->toBeString()
            ->and($token1)->not->toBe($token2)
            ->and($token1)->toHaveLength(36)
            ->and($token2)->toHaveLength(36);
    });
});