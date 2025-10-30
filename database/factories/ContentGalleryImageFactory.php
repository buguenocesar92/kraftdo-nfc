<?php

namespace Database\Factories;

use App\Models\ContentGalleryImage;
use App\Models\ContentMultimedia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContentGalleryImage>
 */
class ContentGalleryImageFactory extends Factory
{
    protected $model = ContentGalleryImage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content_multimedia_id' => ContentMultimedia::factory(),
            'image_path' => 'gallery/' . fake()->lexify('?????') . '.jpg',
            'image_url' => fake()->imageUrl(),
            'alt_text' => fake()->sentence(),
            'caption' => fake()->optional()->paragraph(),
            'sort_order' => 0,
            'type' => fake()->randomElement(['upload', 'url']),
            'metadata' => [],
        ];
    }
}