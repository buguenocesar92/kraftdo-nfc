<?php

namespace Database\Factories;

use App\Models\ContentMultimedia;
use App\Models\DynamicContent;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContentMultimediaFactory extends Factory
{
    protected $model = ContentMultimedia::class;

    public function definition(): array
    {
        return [
            'dynamic_content_id' => DynamicContent::factory(),
            'video_url' => $this->faker->optional()->url(),
            'video_type' => $this->faker->randomElement(['file_upload', 'youtube', 'vimeo', 'direct']),
            'audio_url' => $this->faker->optional()->url(),
            'audio_type' => $this->faker->randomElement(['file_upload', 'youtube_music', 'spotify', 'soundcloud', 'direct']),
            'gallery_images' => [],
            'settings' => [
                'video' => [
                    'autoplay' => $this->faker->boolean(),
                    'muted' => $this->faker->boolean(),
                ],
                'audio' => [
                    'autoplay' => $this->faker->boolean(),
                    'loop' => $this->faker->boolean(),
                ],
            ],
        ];
    }
}