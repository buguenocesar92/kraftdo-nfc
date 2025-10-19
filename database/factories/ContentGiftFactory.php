<?php

namespace Database\Factories;

use App\Models\ContentGift;
use App\Models\DynamicContent;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContentGiftFactory extends Factory
{
    protected $model = ContentGift::class;

    public function definition(): array
    {
        return [
            'dynamic_content_id' => DynamicContent::factory(),
            'sender_name' => $this->faker->name(),
            'recipient_name' => $this->faker->name(),
            'message' => $this->faker->sentence(10),
        ];
    }
}
