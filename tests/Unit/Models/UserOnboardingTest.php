<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\NfcToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserOnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_needs_onboarding_when_first_time(): void
    {
        $user = User::factory()->create([
            'is_first_time_user' => true,
            'onboarding_completed' => false
        ]);

        $this->assertTrue($user->needsOnboarding());
    }

    public function test_user_needs_onboarding_when_not_completed(): void
    {
        $user = User::factory()->create([
            'is_first_time_user' => false,
            'onboarding_completed' => false
        ]);

        $this->assertTrue($user->needsOnboarding());
    }

    public function test_user_does_not_need_onboarding_when_completed(): void
    {
        $user = User::factory()->create([
            'is_first_time_user' => false,
            'onboarding_completed' => true
        ]);

        $this->assertFalse($user->needsOnboarding());
    }

    public function test_complete_onboarding_updates_fields(): void
    {
        $user = User::factory()->create([
            'is_first_time_user' => true,
            'onboarding_completed' => false,
            'onboarding_completed_at' => null
        ]);

        $user->completeOnboarding();

        $this->assertFalse($user->is_first_time_user);
        $this->assertTrue($user->onboarding_completed);
        $this->assertNotNull($user->onboarding_completed_at);
    }

    public function test_update_onboarding_progress_merges_data(): void
    {
        $user = User::factory()->create([
            'onboarding_progress' => ['step1' => ['completed' => true]]
        ]);

        $newProgress = ['step2' => ['completed' => true, 'data' => 'test']];
        $user->updateOnboardingProgress($newProgress);

        $expected = [
            'step1' => ['completed' => true],
            'step2' => ['completed' => true, 'data' => 'test']
        ];

        $this->assertEquals($expected, $user->fresh()->onboarding_progress);
    }

    public function test_update_onboarding_progress_handles_null_initial(): void
    {
        $user = User::factory()->create([
            'onboarding_progress' => null
        ]);

        $newProgress = ['step1' => ['completed' => true]];
        $user->updateOnboardingProgress($newProgress);

        $this->assertEquals($newProgress, $user->fresh()->onboarding_progress);
    }

    public function test_get_status_attribute_returns_correct_structure(): void
    {
        $user = User::factory()->create([
            'is_first_time_user' => true,
            'onboarding_completed' => false,
            'onboarding_progress' => ['welcome' => ['completed' => true]]
        ]);

        // Create a token to test has_tokens
        NfcToken::factory()->create(['user_id' => $user->id]);

        $status = $user->status;

        $this->assertIsArray($status);
        $this->assertArrayHasKey('is_first_time_user', $status);
        $this->assertArrayHasKey('onboarding_completed', $status);
        $this->assertArrayHasKey('has_tokens', $status);
        $this->assertArrayHasKey('tokens_count', $status);
        $this->assertArrayHasKey('needs_onboarding', $status);
        $this->assertArrayHasKey('onboarding_progress', $status);

        $this->assertTrue($status['is_first_time_user']);
        $this->assertFalse($status['onboarding_completed']);
        $this->assertTrue($status['has_tokens']);
        $this->assertEquals(1, $status['tokens_count']);
        $this->assertTrue($status['needs_onboarding']);
        $this->assertEquals(['welcome' => ['completed' => true]], $status['onboarding_progress']);
    }

    public function test_get_status_attribute_with_no_tokens(): void
    {
        $user = User::factory()->create([
            'is_first_time_user' => false,
            'onboarding_completed' => true
        ]);

        $status = $user->status;

        $this->assertFalse($status['has_tokens']);
        $this->assertEquals(0, $status['tokens_count']);
        $this->assertFalse($status['needs_onboarding']);
    }

    public function test_onboarding_fields_are_fillable(): void
    {
        $user = new User();
        $fillable = $user->getFillable();

        $this->assertContains('onboarding_completed', $fillable);
        $this->assertContains('onboarding_completed_at', $fillable);
        $this->assertContains('onboarding_progress', $fillable);
        $this->assertContains('is_first_time_user', $fillable);
    }

    public function test_onboarding_fields_are_cast_correctly(): void
    {
        $user = User::factory()->create([
            'onboarding_completed' => true,
            'is_first_time_user' => false,
            'onboarding_progress' => ['test' => 'value']
        ]);

        $this->assertIsBool($user->onboarding_completed);
        $this->assertIsBool($user->is_first_time_user);
        $this->assertIsArray($user->onboarding_progress);
        $this->assertTrue($user->onboarding_completed);
        $this->assertFalse($user->is_first_time_user);
        $this->assertEquals(['test' => 'value'], $user->onboarding_progress);
    }

    public function test_new_user_defaults(): void
    {
        $user = User::factory()->create();

        // Verify default values from migration
        $this->assertFalse($user->onboarding_completed);
        $this->assertTrue($user->is_first_time_user);
        $this->assertNull($user->onboarding_completed_at);
        $this->assertNull($user->onboarding_progress);
    }
}