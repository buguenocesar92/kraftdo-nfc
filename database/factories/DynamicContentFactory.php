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
            'content_id' => $this->faker->uuid,
            'type' => $this->faker->randomElement(['GIFT', 'PROFILE', 'MENU', 'EVENT']),
            'gift_subtype' => $this->faker->optional()->randomElement(['birthday', 'anniversary', 'valentine']),
            'tier' => $this->faker->randomElement(['BASIC', 'PREMIUM']),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph,
            'data' => [
                'custom_field_1' => $this->faker->word,
                'custom_field_2' => $this->faker->sentence,
            ],
            'image_url' => $this->faker->optional()->imageUrl,
            'is_active' => true,
            'status' => $this->faker->randomElement(['draft', 'published']),
            'published_at' => $this->faker->optional()->dateTimeBetween('-1 month'),
            'last_draft_update' => $this->faker->dateTimeBetween('-1 week'),
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