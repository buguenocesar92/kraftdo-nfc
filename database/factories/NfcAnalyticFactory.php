<?php

namespace Database\Factories;

use App\Models\NfcAnalytic;
use App\Models\NfcToken;
use Illuminate\Database\Eloquent\Factories\Factory;

class NfcAnalyticFactory extends Factory
{
    protected $model = NfcAnalytic::class;

    public function definition(): array
    {
        return [
            'content_id' => $this->faker->uuid,
            'content_type' => $this->faker->randomElement(['GIFT', 'PROFILE', 'BUSINESS']),
            'nfc_token_id' => NfcToken::factory(),
            'ip_address' => $this->faker->ipv4,
            'user_agent' => $this->faker->userAgent,
            'country' => $this->faker->optional()->country,
            'city' => $this->faker->optional()->city,
            'device_type' => $this->faker->randomElement(['mobile', 'desktop', 'tablet']),
            'browser' => $this->faker->randomElement(['Chrome', 'Firefox', 'Safari', 'Edge']),
            'referrer' => $this->faker->optional()->url,
            'is_unique_visit' => $this->faker->boolean(30), // 30% unique visits
            'accessed_at' => $this->faker->dateTimeBetween('-1 month'),
        ];
    }

    public function unique(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_unique_visit' => true,
        ]);
    }

    public function mobile(): static
    {
        return $this->state(fn (array $attributes) => [
            'device_type' => 'mobile',
            'browser' => 'Chrome',
        ]);
    }

    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'accessed_at' => now(),
        ]);
    }
}