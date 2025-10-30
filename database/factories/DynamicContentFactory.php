<?php

namespace Database\Factories;

use App\Models\DynamicContent;
use App\Models\NfcToken;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DynamicContentFactory extends Factory
{
    protected $model = DynamicContent::class;

    public function definition(): array
    {
        return [
            'content_id' => fake()->uuid,
            'type' => fake()->randomElement(['GIFT', 'PROFILE', 'MENU', 'EVENT']),
            'gift_subtype' => fake()->optional()->randomElement(['birthday', 'anniversary', 'valentine']),
            'tier' => fake()->randomElement(['BASIC', 'PREMIUM']),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph,
            'data' => [
                'custom_field_1' => fake()->word,
                'custom_field_2' => fake()->sentence,
            ],
            'image_url' => fake()->optional()->imageUrl,
            'is_active' => true,
            'status' => fake()->randomElement(['draft', 'published']),
            'published_at' => fake()->optional()->dateTimeBetween('-1 month'),
            'last_draft_update' => fake()->dateTimeBetween('-1 week'),
            'post_publish_modifications' => [],
            'published_snapshot' => [],
            'user_id' => User::factory(),
            'nfc_token_id' => NfcToken::factory(),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    public function gift(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'GIFT',
            'gift_subtype' => $this->faker->randomElement(['birthday', 'anniversary', 'valentine']),
        ]);
    }

    public function profile(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'PROFILE',
            'gift_subtype' => null,
        ]);
    }
}
