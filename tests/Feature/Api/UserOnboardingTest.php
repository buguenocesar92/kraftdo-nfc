<?php

namespace Tests\Feature\Api;

use App\Models\NfcToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserOnboardingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_user_status_endpoint_returns_correct_data(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'is_first_time_user' => true,
            'onboarding_completed' => false,
            'onboarding_progress' => ['welcome' => ['completed' => true]]
        ]);

        Sanctum::actingAs($user);

        // Create a token for this user
        NfcToken::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/user/status');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                    'status' => [
                        'is_first_time_user',
                        'onboarding_completed',
                        'has_tokens',
                        'tokens_count',
                        'needs_onboarding',
                        'onboarding_progress'
                    ]
                ]
            ])
            ->assertJson([
                'user' => [
                    'id' => $user->id,
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                    'status' => [
                        'is_first_time_user' => true,
                        'onboarding_completed' => false,
                        'has_tokens' => true,
                        'tokens_count' => 1,
                        'needs_onboarding' => true,
                        'onboarding_progress' => ['welcome' => ['completed' => true]]
                    ]
                ]
            ]);
    }

    public function test_user_status_requires_authentication(): void
    {
        $response = $this->getJson('/api/user/status');

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_user_progress_endpoint_returns_correct_data(): void
    {
        $user = User::factory()->create([
            'is_first_time_user' => true,
            'onboarding_completed' => false,
            'onboarding_progress' => [
                'welcome' => ['completed' => true, 'completed_at' => '2025-01-01T00:00:00.000000Z']
            ]
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/user/progress');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'current_progress',
                'next_step',
                'completion_percentage',
                'recommended_actions'
            ])
            ->assertJson([
                'current_progress' => [
                    'welcome' => ['completed' => true, 'completed_at' => '2025-01-01T00:00:00.000000Z']
                ],
                'next_step' => 'content_type_selection',
                'completion_percentage' => 25
            ]);

        // Verify recommended actions structure
        $response->assertJsonCount(1, 'recommended_actions');
        $response->assertJsonPath('recommended_actions.0.type', 'select_content_type');
        $response->assertJsonPath('recommended_actions.0.action_url', '/onboarding/token-type');
    }

    public function test_user_progress_for_completed_onboarding(): void
    {
        $user = User::factory()->create([
            'is_first_time_user' => false,
            'onboarding_completed' => true
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/user/progress');

        $response->assertStatus(200)
            ->assertJson([
                'next_step' => null,
                'completion_percentage' => 100
            ]);
    }

    public function test_update_onboarding_progress_endpoint(): void
    {
        $user = User::factory()->create([
            'onboarding_progress' => null
        ]);

        Sanctum::actingAs($user);

        $progressData = [
            'step' => 'welcome',
            'completed' => true,
            'data' => ['viewed_at' => '2025-01-01T00:00:00Z']
        ];

        $response = $this->putJson('/api/user/onboarding/progress', $progressData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'progress'
            ])
            ->assertJson([
                'message' => 'Onboarding progress updated successfully'
            ]);

        // Verify database was updated
        $user->refresh();
        $this->assertArrayHasKey('welcome', $user->onboarding_progress);
        $this->assertTrue($user->onboarding_progress['welcome']['completed']);
        $this->assertArrayHasKey('data', $user->onboarding_progress['welcome']);
    }

    public function test_update_onboarding_progress_validates_input(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Test missing required fields
        $response = $this->putJson('/api/user/onboarding/progress', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'step',
                    'completed'
                ]
            ]);

        // Test invalid data types
        $response = $this->putJson('/api/user/onboarding/progress', [
            'step' => 123,
            'completed' => 'not_boolean',
            'data' => 'not_array'
        ]);

        $response->assertStatus(422);
    }

    public function test_complete_onboarding_endpoint(): void
    {
        $user = User::factory()->create([
            'is_first_time_user' => true,
            'onboarding_completed' => false,
            'onboarding_completed_at' => null
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/user/onboarding/complete');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'status'
                ]
            ])
            ->assertJson([
                'message' => 'Onboarding completed successfully'
            ]);

        // Verify database was updated
        $user->refresh();
        $this->assertFalse($user->is_first_time_user);
        $this->assertTrue($user->onboarding_completed);
        $this->assertNotNull($user->onboarding_completed_at);
    }

    public function test_complete_onboarding_requires_authentication(): void
    {
        $response = $this->putJson('/api/user/onboarding/complete');

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_progress_calculation_with_multiple_steps(): void
    {
        $user = User::factory()->create([
            'onboarding_progress' => [
                'welcome' => ['completed' => true],
                'content_type_selection' => ['completed' => true],
                'first_token_creation' => ['completed' => false],
                'dashboard_tour' => ['completed' => false]
            ]
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/user/progress');

        $response->assertStatus(200)
            ->assertJson([
                'next_step' => 'first_token_creation',
                'completion_percentage' => 50
            ]);
    }

    public function test_recommended_actions_for_user_without_tokens(): void
    {
        $user = User::factory()->create([
            'onboarding_completed' => true
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/user/progress');

        $response->assertStatus(200);
        
        $recommendedActions = $response->json('recommended_actions');
        $this->assertCount(1, $recommendedActions);
        $this->assertEquals('create_token', $recommendedActions[0]['type']);
        $this->assertEquals('Create your first NFC token', $recommendedActions[0]['title']);
    }

    public function test_recommended_actions_for_user_with_tokens(): void
    {
        $user = User::factory()->create([
            'onboarding_completed' => true
        ]);

        Sanctum::actingAs($user);

        // Create a token for this user
        NfcToken::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/user/progress');

        $response->assertStatus(200);
        
        $recommendedActions = $response->json('recommended_actions');
        $this->assertCount(1, $recommendedActions);
        $this->assertEquals('explore_features', $recommendedActions[0]['type']);
        $this->assertEquals('Explore advanced features', $recommendedActions[0]['title']);
    }

    public function test_next_step_progression(): void
    {
        $user = User::factory()->create([
            'onboarding_completed' => false,
            'onboarding_progress' => []
        ]);

        Sanctum::actingAs($user);

        // Test progression through steps
        $steps = [
            ['current' => [], 'expected' => 'welcome'],
            ['current' => ['welcome' => ['completed' => true]], 'expected' => 'content_type_selection'],
            [
                'current' => [
                    'welcome' => ['completed' => true],
                    'content_type_selection' => ['completed' => true]
                ],
                'expected' => 'first_token_creation'
            ],
            [
                'current' => [
                    'welcome' => ['completed' => true],
                    'content_type_selection' => ['completed' => true],
                    'first_token_creation' => ['completed' => true]
                ],
                'expected' => 'dashboard_tour'
            ],
            [
                'current' => [
                    'welcome' => ['completed' => true],
                    'content_type_selection' => ['completed' => true],
                    'first_token_creation' => ['completed' => true],
                    'dashboard_tour' => ['completed' => true]
                ],
                'expected' => 'complete_onboarding'
            ]
        ];

        foreach ($steps as $step) {
            $user->update(['onboarding_progress' => $step['current']]);
            
            $response = $this->getJson('/api/user/progress');
            $response->assertStatus(200)
                ->assertJson(['next_step' => $step['expected']]);
        }
    }
}