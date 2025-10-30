<?php

namespace Tests\Feature\Api;

use App\Models\NfcToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TokenRouteBindingTest extends TestCase
{
    use RefreshDatabase;

    public function test_token_route_binding_works(): void
    {
        $user = User::factory()->create();
        
        $token = NfcToken::factory()->create(['user_id' => $user->id]);
        
        Sanctum::actingAs($user);
        
        // First test that index route works
        $indexResponse = $this->getJson('/api/tokens');
        echo "\nIndex status: " . $indexResponse->getStatusCode();
        
        // Test route binding with update endpoint
        $response = $this->putJson("/api/tokens/{$token->id}", [
            'name' => 'Test Update'
        ]);
        
        // Should not be 404 - could be validation error or other, but route should exist
        $this->assertNotEquals(404, $response->getStatusCode(), 'Route should exist and not return 404');
        
        echo "\nActual status for token {$token->id}: " . $response->getStatusCode();
        echo "\nResponse: " . $response->getContent();
    }
}