<?php

namespace Database\Factories;

use App\Models\NfcToken;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NfcTokenFactory extends Factory
{
    protected $model = NfcToken::class;

    public function definition(): array
    {
        return [
            'token_id' => $this->faker->uuid,
            'user_id' => User::factory(),
            'name' => $this->faker->words(3, true),
            'content_type' => $this->faker->randomElement(['GIFT', 'PROFILE', 'MENU', 'EVENT']),
            'customization_plan' => $this->faker->randomElement(['BASIC', 'STANDARD', 'PREMIUM', 'DELUXE']),
            'purchase_price' => $this->faker->randomFloat(2, 10, 500),
            'purchased_at' => $this->faker->dateTimeBetween('-1 year'),
            'purchase_notes' => $this->faker->optional()->sentence(),
            'purchase_currency' => 'USD',
            'cost_per_view' => $this->faker->randomFloat(4, 0.01, 5.0),
            'total_investment_views' => $this->faker->numberBetween(0, 1000),
            'is_active' => true,
            'last_used_at' => $this->faker->optional()->dateTimeBetween('-1 month'),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function gift(): static
    {
        return $this->state(fn (array $attributes) => [
            'content_type' => 'GIFT',
        ]);
    }

    public function profile(): static
    {
        return $this->state(fn (array $attributes) => [
            'content_type' => 'PROFILE',
        ]);
    }

    public function withoutUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
        ]);
    }
}