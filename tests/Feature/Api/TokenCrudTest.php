<?php

use App\Models\NfcToken;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    
    $this->user = User::factory()->create();
    $this->user->assignRole('NFC');
});

test('can list user tokens with pagination', function () {
    Sanctum::actingAs($this->user);

    // Create tokens for this user
    NfcToken::factory()->count(3)->create(['user_id' => $this->user->id]);
    
    // Create tokens for another user (should not appear)
    $otherUser = User::factory()->create();
    NfcToken::factory()->count(2)->create(['user_id' => $otherUser->id]);

    $response = $this->getJson('/api/tokens');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'token_id',
                    'user_id',
                    'name',
                    'content_type',
                    'customization_plan',
                    'is_active',
                    'total_investment_views',
                    'created_at',
                    'updated_at'
                ]
            ],
            'meta' => [
                'current_page',
                'last_page',
                'per_page',
                'total',
                'from',
                'to'
            ],
            'message'
        ])
        ->assertJsonCount(3, 'data');

    // Verify only user's tokens are returned
    $returnedUserIds = collect($response->json('data'))->pluck('user_id')->unique();
    expect($returnedUserIds->toArray())->toBe([$this->user->id]);
});

test('can filter tokens by content type', function () {
    Sanctum::actingAs($this->user);

    NfcToken::factory()->create([
        'user_id' => $this->user->id,
        'content_type' => 'PROFILE'
    ]);
    
    NfcToken::factory()->create([
        'user_id' => $this->user->id,
        'content_type' => 'BUSINESS'
    ]);

    $response = $this->getJson('/api/tokens?content_type=PROFILE');

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.content_type', 'PROFILE');
});

test('can search tokens by name', function () {
    Sanctum::actingAs($this->user);

    NfcToken::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Mi Perfil Personal'
    ]);
    
    NfcToken::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Negocio Café'
    ]);

    $response = $this->getJson('/api/tokens?search=Perfil');

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Mi Perfil Personal');
});

test('can create a new token', function () {
    Sanctum::actingAs($this->user);

    $tokenData = [
        'name' => 'Nuevo Token',
        'content_type' => 'PROFILE',
        'customization_plan' => 'STANDARD'
    ];

    $response = $this->postJson('/api/tokens', $tokenData);

    $response->assertCreated()
        ->assertJsonStructure([
            'data' => [
                'id',
                'token_id',
                'user_id',
                'name',
                'content_type',
                'customization_plan',
                'is_active'
            ],
            'message'
        ])
        ->assertJsonPath('data.name', 'Nuevo Token')
        ->assertJsonPath('data.content_type', 'PROFILE')
        ->assertJsonPath('data.customization_plan', 'STANDARD')
        ->assertJsonPath('data.user_id', $this->user->id)
        ->assertJsonPath('data.is_active', true);

    $this->assertDatabaseHas('nfc_tokens', [
        'name' => 'Nuevo Token',
        'content_type' => 'PROFILE',
        'user_id' => $this->user->id
    ]);
});

test('validates required fields when creating token', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/tokens', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'content_type']);
});

test('can update a token', function () {
    Sanctum::actingAs($this->user);

    $token = NfcToken::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Original Name',
        'content_type' => 'PROFILE'
    ]);

    $updateData = [
        'name' => 'Updated Name',
        'content_type' => 'BUSINESS',
        'is_active' => false
    ];

    $response = $this->putJson("/api/tokens/{$token->id}", $updateData);

    $response->assertOk()
        ->assertJsonPath('data.name', 'Updated Name')
        ->assertJsonPath('data.content_type', 'BUSINESS')
        ->assertJsonPath('data.is_active', false);

    $this->assertDatabaseHas('nfc_tokens', [
        'id' => $token->id,
        'name' => 'Updated Name',
        'content_type' => 'BUSINESS',
        'is_active' => false
    ]);
});

test('cannot update another users token', function () {
    Sanctum::actingAs($this->user);

    $otherUser = User::factory()->create();
    $token = NfcToken::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->putJson("/api/tokens/{$token->id}", [
        'name' => 'Hacked Name'
    ]);

    $response->assertNotFound();
});

test('can delete a token', function () {
    Sanctum::actingAs($this->user);

    $token = NfcToken::factory()->create(['user_id' => $this->user->id]);

    $response = $this->deleteJson("/api/tokens/{$token->id}");

    $response->assertOk()
        ->assertJsonPath('message', 'Token eliminado exitosamente');

    $this->assertDatabaseMissing('nfc_tokens', ['id' => $token->id]);
});

test('requires authentication for all CRUD endpoints', function () {
    $token = NfcToken::factory()->create();

    // Test index
    $this->getJson('/api/tokens')->assertUnauthorized();
    
    // Test store
    $this->postJson('/api/tokens', [])->assertUnauthorized();
    
    // Test update
    $this->putJson("/api/tokens/{$token->id}", [])->assertUnauthorized();
    
    // Test delete
    $this->deleteJson("/api/tokens/{$token->id}")->assertUnauthorized();
});