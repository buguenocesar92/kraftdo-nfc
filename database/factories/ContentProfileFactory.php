<?php

namespace Database\Factories;

use App\Models\ContentProfile;
use App\Models\DynamicContent;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContentProfileFactory extends Factory
{
    protected $model = ContentProfile::class;

    public function definition(): array
    {
        return [
            'dynamic_content_id' => DynamicContent::factory(),
            'name' => $this->faker->name(),
            'bio' => $this->faker->paragraph(3),
            'profession' => $this->faker->jobTitle(),
            'company' => $this->faker->company(),
            'location' => $this->faker->city(),
            'contact_info' => $this->faker->email(),
            'contact_email' => $this->faker->email(),
            'contact_phone' => $this->faker->phoneNumber(),
            'contact_website' => $this->faker->url(),
        ];
    }
}