<?php

namespace Tests\Feature\Api;

use App\Models\NfcToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrCodeRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_qr_route_exists(): void
    {
        $user = User::factory()->create();
        $token = NfcToken::factory()->create([
            'user_id' => $user->id,
            'name' => 'Test Token',
            'token_id' => 'test-token-123',
            'content_type' => 'GIFT',
            'is_active' => true,
        ]);

        // Test route exists and doesn't return 404
        $response = $this->getJson("/api/tokens/{$token->id}/qr");
        
        // Should not be 404 - could be 200, 500, or other error, but route should exist
        $this->assertNotEquals(404, $response->getStatusCode(), 'Route should exist and not return 404');
        
        echo "\nActual status: " . $response->getStatusCode();
        echo "\nResponse: " . $response->getContent();
    }
}